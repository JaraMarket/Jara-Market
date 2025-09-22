<?php
namespace App\Services;

use App\Models\Setting;
use App\Services\Kafka\KafkaProducerService;
use App\Enums\WalletTransactionTypeEnum;
use App\Notifications\WalletNotification;
use App\Enums\OrderNotificationTypeEnum;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use App\Models\OrderItemLog;
use Exception;
use App\Utils\Util;
use App\Models\Order;
use App\Models\Product;
use App\Enums\StatusEnum;
use App\Models\OrderItem;
use App\Models\Ingredient;
use App\Enums\UserPermissionsEnum;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderNotification;
use App\Notifications\OrderCancelledNotification;

class OrderService
{
    public function __construct(public TransactionLogService $transactionLogService)
    { }

    public function all()
    {
        return Order::with(['items.product','items.ingredient', 'address'])
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->get();
    }

    public function getOrderById(int $id)
    {
        return Order::with(['items.product', 'items.ingredient', 'address'])
                    ->where('user_id', auth()->id())
                    ->findOrFail($id);
    }

    public function createOrder($request): Order
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $user = auth()->user();
            $wallet = $user->wallet;
    
            if (!$wallet || $wallet->balance < $data['total']) {
                throw new Exception('Insufficient wallet balance.');
            }
    
            $audio_url = null;

            if ($request->hasFile('audio')) {
                $audio_url = upload_image("orders/audio", $request->audio);
            }
                        
            $order = Order::create([
                'order_date'     => $data['order_date'],
                'reference'      => Util::generate_order_txn_ref(),
                'user_id'        => $user->id,
                'address_id'     => $data['address_id'] ?? null,
                'delivery_type'  => $data['delivery_type'],
                'shipping_fee'   => $data['shipping_fee'] ?? 0,
                'service_charge' => $data['service_charge'],
                'vat'            => $data['vat'] ?? 0,
                'total'          => $data['total'],
                'status'         => StatusEnum::PENDING(),
                'audio'          => $audio_url,
                'remarks'        => $data['remarks']
            ]);
            // Deduct from wallet
            $this->transactionLogService::debit($user->id,get_class($user),$data['total'],$order->id,get_class($order));

            $this->saveFood($data, $order, $user);  
            $this->saveIngredients($data, $order, $user);
            
            $order->load(['items.product', 'items.ingredient', 'address']);

            KafkaProducerService::publish('order-events', [
                'type'           => StatusEnum::PENDING(),
                'order'          => $order,
                'recipient_type' => OrderNotificationTypeEnum::CUSTOMER(),
                'user_id'        => $order->user_id,
                'status'         => $order->status,
                'timestamp'      => now()->toISOString(),
            ]);

            KafkaProducerService::publish('wallet-events', [
                'type'      => WalletTransactionTypeEnum::DEBIT(),
                'amount'    => $data['total'],
                'balance'   => $user->wallet->balance,
                'reference' => $order->reference,
                'remarks'   => "Payment for Order #{$order->reference}",
                'user_id'   => $user->id,
            ]);
            return $order;
        });
    }

    private function getBonuses($price, $quantity, $order, $user)
    {
        $settings = Setting::whereIn('key', ['first_order_bonus', 'repeat_order_bonus'])->pluck('value', 'key');
        $item_total = $price * $quantity;
        $commission_result = Util::getCommission($item_total, $order->total);
        $commission = $commission_result['commission'];

        $referral_commission = 0;
        $referral_id = null;

        if ($user->referrer_id) {
            // Auto detect first/repeat shopping
            $previous_orders = Order::where('user_id', $user->id)->count();
            $is_first_shopping = $previous_orders === 0;

            $percentage = $is_first_shopping ? $settings['first_order_bonus'] : $settings['repeat_order_bonus'];
            $referral_commission = ($commission * $percentage) / 100;
            $referral_id = $user->referrer_id;
        }

        return [
            'referral_commission' => $referral_commission ?? 0,
            'referral_id' => $referral_id,
            'commission' => $commission ?? 0,
            'item_total' => $item_total
        ];
    }

    private function saveIngredients($data, $order, $user)
    {
        foreach ($data['ingredients'] ?? [] as $ingredient) {
            
            $ingredient_model = Ingredient::find($ingredient['ingredient_id']);
            $price = $ingredient['price'] ?? $ingredient_model->price;
            
            $bonus = $this->getBonuses($price, $ingredient['quantity'], $order, $user);
            
            //if (!$ingredient_model) {
              //  continue;
            //}
        
            $order->items()->create([
                'ingredient_id' => $ingredient_model->id,
                'quantity'      => $ingredient['quantity'],
                'price'         => $price,
                'unit'          => $ingredient['unit'],
                'amount'        => $bonus['item_total'],
                'commision'     => $bonus['commission'],
                'vendor_amount' => $bonus['item_total'] - $bonus['commission'],
                'referral'      => $bonus['referral_commission'],
                'referral_id'   => $bonus['referral_id']
            ]);
        }
    }

    private function saveFood($data, $order, $user)
    {
        foreach ($data['products'] ?? [] as $product_data) {
            $product = Product::with('ingredients')->find($product_data['product_id']);
            foreach ($product->ingredients as $ingredient) {

                $quantity = $ingredient->quantity ?? 1;
                $bonus = $this->getBonuses($ingredient->price, $quantity, $order, $user);

                $order->items()->create([
                    'product_id'    => $product->id,
                    'ingredient_id' => $ingredient->id,
                    'quantity'      => $quantity,
                    'price'         => $ingredient->price,
                    'unit'          => $ingredient->unit ?? null,
                    'amount'        => $bonus['item_total'],
                    'commision'     => $bonus['commission'],
                    'vendor_amount' => $bonus['item_total'] - $bonus['commission'],
                    'referral'      => $bonus['referral_commission'],
                    'referral_id'   => $bonus['referral_id']
                ]);
            }
        }
    }

    public function cancelOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            if ($order->status !== StatusEnum::PENDING()) {
                throw new Exception("You cannot cancel this order");
            }
            $user = auth()->user();
            $this->transactionLogService::credit($user->id,get_class($user),$order->total,$order->id,get_class($order));
            $order->update(['status' => StatusEnum::CANCELLED()]);

            KafkaProducerService::publish('order-events', [
                'type'           => StatusEnum::CANCELLED(),
                'order'          => $order,
                'recipient_type' => OrderNotificationTypeEnum::CUSTOMER(),
                'user_id'        => $order->user_id,
                'status'         => $order->status,
                'timestamp'      => now()->toISOString(),
            ]);

            KafkaProducerService::publish('wallet-events', [
                'type'      => WalletTransactionTypeEnum::CREDIT(),
                'amount'    => $order->total,
                'balance'   => $user->wallet->balance,
                'reference' => $order->reference,
                'remarks'   => "Refund from Order #{$order->reference}",
                'user_id'   => $user->id,
            ]);
            return $order;
        });
    }

    public function getAvailableOrders()
    {
        $user = auth()->user();
        $query = OrderItem::with(['ingredient.category', 'order'])
            ->where('status', StatusEnum::PENDING());

        if ($user->role !== UserPermissionsEnum::ADMIN()) {
            $userCategoryIds = $user->categories()->pluck('category_id')->toArray();
    
            $query->whereHas('ingredient', function ($q) use ($userCategoryIds) {
                $q->whereIn('category_id', $userCategoryIds);
            });
        }
        
        return $query->orderByDesc('created_at')->get();
    }

    public function showOrderByItemId($itemId)
    {
        return OrderItem::with(['ingredient', 'order'])->find($itemId);
    }

    public function decide(array $data, int $itemId)
    {
        $user = auth()->user();
        return DB::transaction(function () use ($user, $data, $itemId) {
            $status = $data['status'] === StatusEnum::ACCEPTED()
                ? StatusEnum::PROCESSING()
                : StatusEnum::PENDING();
    
            $orderItem = OrderItem::findOrFail($itemId);
    
            $vendor_id = $user->id;
            $vendor_at = now();
    
            if ($user->role === UserPermissionsEnum::ADMIN() && isset($data['vendor_id'])) {
                $vendor_id = $data['vendor_id'];
            }
    
            // update order item
            $orderItem->update([
                'status'     => $status,
                'vendor_id'  => $vendor_id,
                'vendor_at'  => $vendor_at,
            ]);
    
            // log vendor decision
            OrderItemLog::create([
                'order_item_id' => $orderItem->id,
                'vendor_id'     => $vendor_id,
                'status'        => $status,
                'changed_at'    => now(),
            ]);
    
            $order = $orderItem->order;
    
            // check if all accepted
            $allAccepted = $order->items()->where('status', '!=', StatusEnum::PROCESSING())->count() === 0;
            
    
            if ($allAccepted) {
                $order->update(['status' => StatusEnum::PROCESSING()]);
    
                // customer
                $order->user->notify(new OrderStatusNotification($order,  OrderNotificationTypeEnum::CUSTOMER(), StatusEnum::PROCESSING()));
    
                KafkaProducerService::publish('order-events', [
                    'type'           => 'order.update',
                    'order'          => $order,
                    'recipient_type' => OrderNotificationTypeEnum::CUSTOMER(),
                    'user_id'        => $order->user_id,
                    'status'         => $order->status,
                    'timestamp'      => now()->toISOString(),
                ]);

                // vendors
                $vendors = $order->items->pluck('vendor')->unique()->filter();
                foreach ($vendors as $vendor) {
                    KafkaProducerService::publish('order-events', [
                        'type'           => 'order.update',
                        'order'          => $order,
                        'recipient_type' => OrderNotificationTypeEnum::VENDOR(),
                        'user_id'        => $vendor->user_id,
                        'status'         => $order->status,
                        'timestamp'      => now()->toISOString(),
                    ]);
                }
    
                // admins
                //User::where('role', UserPermissionsEnum::ADMIN())->each(function ($admin) use ($order) {
                  //  $admin->notify(new OrderStatusNotification($order, OrderNotificationTypeEnum::ADMIN(), StatusEnum::PROCESSING()));
                //});
            }

            return $orderItem;
        });
    }

    public function getMyOrders()
    {
        $user = auth()->user();
        $query = OrderItem::with(['order', 'vendor']);

        if ($user->role === UserPermissionsEnum::ADMIN()) {
            $query->whereNotNull('vendor_id');
        } else {
            $query->where('vendor_id', $user->id);
        }
        
        return $query->orderByDesc('vendor_at', 'desc')->get();
    }

    public function markAsCompleted(int $id)
    {
        return DB::transaction(function () use ($id) {
            $order = Order::with('items')->findOrFail($id);
            $user = auth()->user();

            // --- 1. Validate status ---
            if (in_array($order->status, [StatusEnum::COMPLETED(), StatusEnum::CANCELLED()])) {
                throw new Exception("Order #{$order->reference} has already been {$order->status}, cannot be marked as completed again.");
            }

            // --- 2. Update order + items status ---
            $order->update(['status' => StatusEnum::COMPLETED()]);
            $order->items()->update([
                 'status' => StatusEnum::COMPLETED(), 
                 'assurance_user_id' => $user->id,
                 'assurance_at' => now(),
                 'pass_quality_assurance' => true
            ]);

            // --- 3. Notify customer ---
            KafkaProducerService::publish('order-events', [
                'type'           => 'order.completed',
                'order'          => $order,
                'recipient_type' => OrderNotificationTypeEnum::CUSTOMER(),
                'user_id'        => $order->user->id,
                'status'         => $order->status,
                'timestamp'      => now()->toISOString(),
            ]);

            // --- 5. Credit vendors ---
            $vendor_credits = $order->items
                ->whereNotNull('vendor_id')
                ->groupBy('vendor_id')
                ->map(fn($items) => $items->sum('vendor_amount'));

            foreach ($vendor_credits as $vendor_id => $amount) {
                if ($amount > 0) {
                    $vendor = User::find($vendor_id);
                    if ($vendor) { 
                        $this->transactionLogService::credit($vendor_id, get_class($vendor),$amount,$order->id,get_class($order));
                        KafkaProducerService::publish('order-events', [
                            'type'           => 'order.completed',
                            'order'          => $order,
                            'recipient_type' => OrderNotificationTypeEnum::VENDOR(),
                            'user_id'        => $vendor_id,
                            'status'         => $order->status,
                            'timestamp'      => now()->toISOString(),
                        ]);
                        
                        KafkaProducerService::publish('wallet-events', [
                            'type'      => WalletTransactionTypeEnum::CREDIT(),
                            'amount'    => $amount,
                            'balance'   => $vendor->wallet->balance,
                            'reference' => $order->reference,
                            'remarks'   => "Payment from Order #{$order->reference}",
                            'user_id'   => $vendor->id,
                        ]);
                    }
                }
            }

            // --- 6. Credit referrals ---
            $referral_credits = $order->items
                ->whereNotNull('referral_id')
                ->groupBy('referral_id')
                ->map(fn($items) => $items->sum('referral'));


            foreach ($referral_credits as $referral_id => $amount) {
                if ($amount > 0) {
                    $referral = User::find($referral_id);
                    if ($referral) { 
                        $this->transactionLogService::credit($referral_id, get_class($referral),$amount,$order->id,get_class($order));
                        KafkaProducerService::publish('wallet-events', [
                            'type'      => WalletTransactionTypeEnum::CREDIT(),
                            'amount'    => $amount,
                            'balance'   => $referral->wallet->balance,
                            'reference' => $order->reference,
                            'remarks'   => "Commissions",
                            'user_id'   => $referral->id,
                        ]);
                    }
                }
            }

            return $order;
        });
    }

}