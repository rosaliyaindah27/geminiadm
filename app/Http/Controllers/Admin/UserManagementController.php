<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserNotification;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{


    public function index(Request $request)
    {
        $query = User::with(['roles', 'activeSubscription.subscriptionPlan']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('subscription_status')) {
            if ($request->subscription_status === 'active') {
                $query->whereHas('activeSubscription');
            } elseif ($request->subscription_status === 'inactive') {
                $query->whereDoesntHave('activeSubscription');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load([
            'roles',
            'subscriptions.subscriptionPlan',
            'payments',
            'userBots.tradingBot',
            'userNotifications' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        $stats = [
            'total_payments' => $user->payments()->count(),
            'total_spent' => $user->payments()->where('payment_status', 'finished')->sum('price_amount'),
            'active_bots' => $user->userBots()->active()->count(),
            'total_notifications' => $user->userNotifications()->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        // Create notification for the new user
        UserNotification::createForUser(
            $user,
            'account',
            'Welcome to Gemini Pro Trader',
            'Your account has been created by an administrator. You can now log in and start using our services.',
            ['created_by_admin' => true],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Cancel active subscriptions
        $user->subscriptions()->active()->update(['status' => 'cancelled']);

        // Deactivate user bots
        $user->userBots()->active()->update(['status' => 'inactive']);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot suspend your own account.');
        }

        $user->update(['suspended_at' => now()]);

        // Cancel active subscriptions
        $user->subscriptions()->active()->update(['status' => 'suspended']);

        // Deactivate user bots
        $user->userBots()->active()->update(['status' => 'suspended']);

        // Create notification
        UserNotification::createForUser(
            $user,
            'account',
            'Account Suspended',
            'Your account has been suspended by an administrator. Please contact support for more information.',
            ['suspended_by' => auth()->id()],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User suspended successfully.');
    }

    public function unsuspend(User $user)
    {
        $user->update(['suspended_at' => null]);

        // Reactivate subscriptions that were suspended
        $user->subscriptions()->where('status', 'suspended')->update(['status' => 'active']);

        // Reactivate user bots that were suspended
        $user->userBots()->where('status', 'suspended')->update(['status' => 'active']);

        // Create notification
        UserNotification::createForUser(
            $user,
            'account',
            'Account Reactivated',
            'Your account has been reactivated. You can now access all services again.',
            ['unsuspended_by' => auth()->id()],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User unsuspended successfully.');
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot impersonate yourself.');
        }

        session(['impersonating' => auth()->id()]);
        auth()->login($user);

        return redirect()
            ->route('dashboard')
            ->with('info', 'You are now impersonating ' . $user->name);
    }

    public function stopImpersonating()
    {
        if (!session('impersonating')) {
            return redirect()->route('dashboard');
        }

        $adminId = session('impersonating');
        session()->forget('impersonating');
        
        $admin = User::find($adminId);
        auth()->login($admin);

        return redirect()
            ->route('admin.dashboard')
            ->with('info', 'Stopped impersonating user.');
    }

    public function sendNotification(Request $request, User $user)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'action_url' => 'nullable|url',
        ]);

        UserNotification::createForUser(
            $user,
            'admin',
            $validated['title'],
            $validated['message'],
            ['sent_by_admin' => auth()->id()],
            $validated['priority'],
            $validated['action_url']
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Notification sent successfully.');
    }
}
