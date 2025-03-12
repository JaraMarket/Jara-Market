<?php

namespace App\Http\Controllers;

use App\Models\StateRepresentative;
use App\Models\User;
use Illuminate\Http\Request;

class StateRepresentativeController extends Controller
{
    public function index()
    {
        $representatives = StateRepresentative::with('user')->latest()->paginate(10);
        return view('representatives.index', compact('representatives'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->get();
        return view('representatives.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:state_representatives'],
            'phone' => ['required', 'string', 'max:20'],
            'state' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        StateRepresentative::create($validated);

        return redirect()->route('representatives.index')
            ->with('success', 'State Representative created successfully');
    }

    public function edit(StateRepresentative $representative)
    {
        $users = User::where('is_active', true)->get();
        return view('representatives.edit', compact('representative', 'users'));
    }

    public function update(Request $request, StateRepresentative $representative)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:state_representatives,email,' . $representative->id],
            'phone' => ['required', 'string', 'max:20'],
            'state' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        $representative->update($validated);

        return redirect()->route('representatives.index')
            ->with('success', 'State Representative updated successfully');
    }

    public function destroy(StateRepresentative $representative)
    {
        $representative->delete();
        return redirect()->route('representatives.index')
            ->with('success', 'State Representative deleted successfully');
    }

    public function toggleStatus(StateRepresentative $representative)
    {
        $representative->is_active = !$representative->is_active;
        $representative->save();

        return redirect()->route('representatives.index')
            ->with('success', 'Representative status updated successfully');
    }
}
