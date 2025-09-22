<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Enums\UserPermissionsEnum;
use App\Http\Requests\UserProfileRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;

class UserController extends Controller
{
    // List all users
    public function index()
    {
        return view('users.index');
    }

    public function getData(Request $request)
    {
        $query = User::where('role', UserPermissionsEnum::CUSTOMER());

        $query->when($request->status, function ($q) use ($request) {
            $q->where('is_active', $request->status === 'active' ? 1 : 0);
        }, function ($q) {
            $q->whereDate('created_at', now()->toDateString());
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // serial number column
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('M d, Y H:i');
            })
            ->editColumn('status', function ($user) {
                return $user->is_active
                    ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
                    : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
            })
            ->addColumn('actions', function ($user) {
                return '
                    <a href="'.route('users.edit', $user).'" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                    <button type="button" class="text-red-600 hover:text-red-900 delete-user" data-user-id="'.$user->id.'">Delete</button>
                ';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    // Show create form
    public function create()
    {
        return view('users.create');
    }

    // Store new user
    public function store(UserProfileRequest $request)
    {
       try{
            $data = $request->validated();

            $user = User::create([
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'email'     => $data['email'],
                'password'  => $data['password'],
                'role'      => $data['role'],
                'referral_code' => Str::random(10),
            ]);

            Wallet::create(['user_id' => $user->id]);

            return redirect()->back()
                ->with('success', 'User created successfully');
        }catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    // Show edit form
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Update user
    public function update(UserProfileRequest $request, User $user)
    {
       try{
            $data = $request->validated();

            $userData = [
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'is_active' => $data['is_active']
            ];

            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            $user->update($userData);

            return redirect()->back()
                ->with('success', 'User updated successfully');
        } catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    // Toggle active/inactive
    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'Activated' : 'Deactivated';

        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully");
    }

    // Delete user
    public function destroy(User $user)
    {
        try{
        $user->delete();

        return redirect()->back()
            ->with('success', 'User deleted successfully');
        }catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
