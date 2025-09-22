<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'txn_ref' => $this->txn_ref,
            'amount' => $this->amount,
            'user_name' => $this?->transaction_initiator?->name,
            'transaction_mode' => $this->transaction_mode,
            'gateway_response' => $this->gateway_response,
            'provider' => $this->provider,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
        return $response;
    }
}
