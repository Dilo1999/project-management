<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:'.implode(',', [User::ROLE_NORMAL, User::ROLE_DEVELOPER, User::ROLE_DESIGNER])],
        ]);

        $user->update([
            'role' => $validated['role'],
            'approved_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', "{$user->name}'s account has been approved. They can now log in.");
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Super admin role cannot be changed here.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:'.implode(',', [User::ROLE_NORMAL, User::ROLE_DEVELOPER, User::ROLE_DESIGNER])],
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('users.index')->with('success', "{$user->name}'s role has been updated.");
    }

    /**
     * Remove a user from the system (super admin only).
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot remove your own account.');
        }

        if ($user->isSuperAdmin()) {
            $otherSuperAdmins = User::where('role', User::ROLE_SUPER_ADMIN)
                ->where('id', '!=', $user->id)
                ->count();
            if ($otherSuperAdmins === 0) {
                return redirect()->route('users.index')->with('error', 'Cannot remove the last super admin.');
            }
        }

        $name = $user->name;

        if (Schema::hasTable('chat_messages')) {
            ChatMessage::where('user_id', $user->id)->delete();
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', "User \"{$name}\" has been removed.");
    }
}
