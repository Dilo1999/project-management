<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users (super admin only).
     */
    public function index()
    {
        $user = Auth::user();
        $users = User::orderByRaw('approved_at IS NULL DESC')->orderBy('name')->get();
        $pendingUsers = User::whereNull('approved_at')->orderBy('created_at')->get();

        $stats = [
            'totalUsers' => $users->count(),
            'superAdmins' => $users->where('role', 'super_admin')->count(),
            'pendingCount' => $pendingUsers->count(),
        ];

        return view('users.index', compact('user', 'users', 'pendingUsers', 'stats'));
    }

    /**
     * Approve a pending user account.
     */
    public function approve(Request $request, User $user)
    {
        if ($user->isApproved()) {
            return redirect()->route('users.index')->with('success', 'User is already approved.');
        }

        $user->update(['approved_at' => now()]);

        return redirect()->route('users.index')->with('success', "{$user->name}'s account has been approved. They can now log in.");
    }
}
