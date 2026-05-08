<?php
// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $user = auth()->user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile_photos', 'public');
        $user->update(['profile_photo' => $path]);

        return back()->with('success', 'Profile photo updated successfully!');
    }

    public function removePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return back()->with('success', 'Profile photo removed.');
    }

    public function getNotifications()
    {
        $user = auth()->user();
        $notifications = [];

        if ($user->isAccountant()) {
            // Rejected journal entries
            $rejected = \App\Models\JournalEntry::where('company_id', $user->company_id)
                ->where('created_by', $user->id)
                ->where('status', 'rejected')
                ->whereDate('updated_at', '>=', now()->subDays(7))
                ->count();

            if ($rejected > 0) {
                $notifications[] = [
                    'icon' => 'fa-times-circle',
                    'bg' => '#fee2e2',
                    'color' => '#dc2626',
                    'message' => "{$rejected} journal entr" . ($rejected > 1 ? 'ies' : 'y') . " rejected — please review",
                    'time' => 'Recently',
                ];
            }

            // Overdue invoices
            $overdue = \App\Models\Invoice::where('company_id', $user->company_id)
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['paid', 'draft'])
                ->count();

            if ($overdue > 0) {
                $notifications[] = [
                    'icon' => 'fa-exclamation-circle',
                    'bg' => '#fef3c7',
                    'color' => '#d97706',
                    'message' => "{$overdue} overdue invoice" . ($overdue > 1 ? 's' : '') . " need attention",
                    'time' => 'Today',
                ];
            }
        }

        if ($user->isManager()) {
            // Pending approvals
            $pending = \App\Models\JournalEntry::where('company_id', $user->company_id)
                ->where('status', 'pending')
                ->count();

            if ($pending > 0) {
                $notifications[] = [
                    'icon' => 'fa-clock',
                    'bg' => '#fef3c7',
                    'color' => '#d97706',
                    'message' => "{$pending} journal entr" . ($pending > 1 ? 'ies' : 'y') . " awaiting your approval",
                    'time' => 'Pending',
                ];
            }
        }

        if ($user->isAdmin()) {
            // Pending subscription payments (developer approval)
            $pendingPayments = \App\Models\SubscriptionPayment::where('status', 'pending')->count();
            if ($pendingPayments > 0) {
                $notifications[] = [
                    'icon' => 'fa-money-bill-wave',
                    'bg' => '#d1fae5',
                    'color' => '#065f46',
                    'message' => "{$pendingPayments} payment receipt" . ($pendingPayments > 1 ? 's' : '') . " awaiting approval",
                    'time' => 'Action required',
                ];
            }
        }

        return response()->json(['notifications' => $notifications]);
    }
}
