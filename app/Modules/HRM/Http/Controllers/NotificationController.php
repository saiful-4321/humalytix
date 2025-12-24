<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        $employees = \App\Modules\HRM\Models\Employee::active()->get();
        $departments = \App\Modules\HRM\Models\Department::all();
        return view('HRM::pages.notifications.index', compact('notifications', 'employees', 'departments'));
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        // Mark as read when viewing
        if (!$notification->read_at) {
            $notification->markAsRead();
        }
        
        return view('HRM::pages.notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadNotifications->count()
            ]);
        }
        
        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        }
        
        return back()->with('success', 'All notifications marked as read');
    }

    
    public function create()
    {
        $employees = \App\Modules\HRM\Models\Employee::active()->get();
        $departments = \App\Modules\HRM\Models\Department::all();
        return view('HRM::pages.notifications.create', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string',
            'recipient_type' => 'required|in:all,department,individual',
            'department_id' => 'required_if:recipient_type,department',
            'user_ids' => 'required_if:recipient_type,individual|array',
        ]);

        // Determine recipients
        $users = collect();
        
        if ($request->recipient_type === 'all') {
            $users = \App\Models\User::all();
        } elseif ($request->recipient_type === 'department') {
            $employees = \App\Modules\HRM\Models\Employee::where('department_id', $request->department_id)->get();
            $users = \App\Models\User::whereIn('id', $employees->pluck('user_id'))->get();
        } elseif ($request->recipient_type === 'individual') {
            $users = \App\Models\User::whereIn('id', $request->user_ids)->get();
        }

        // Prepare notification data
        $notificationData = [
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
            'action_url' => $request->action_url ?? '#',
        ];

        // Dispatch to queue for async processing
        \App\Jobs\SendBulkNotificationsJob::dispatch($users, $notificationData);

        return redirect()->route('hrm.notifications.index')
            ->with('success', "Notification queued for {$users->count()} user(s)!");
    }

    public function toggleRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        if ($notification->read_at) {
            $notification->update(['read_at' => null]);
        } else {
            $notification->markAsRead();
        }
        
        return back()->with('success', 'Notification status updated');
    }
}
