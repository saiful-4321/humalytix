<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Get the first user (typically admin)
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Skipping notification seeding.');
            return;
        }

        // Sample notifications
        $notifications = [
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\LeaveApprovalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'leave',
                    'title' => 'Leave Request Approved',
                    'message' => 'Your leave request for 3 days has been approved by your manager.',
                    'action_url' => route('hrm.leaves.index'),
                ]),
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\WorkflowNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'approval',
                    'title' => 'Pending Approval',
                    'message' => 'You have a new leave request pending your approval.',
                    'action_url' => route('hrm.leaves.index'),
                ]),
                'read_at' => null,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\ExpenseNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'expense',
                    'title' => 'Expense Claim Submitted',
                    'message' => 'Your expense claim for BDT 5,000 has been submitted successfully.',
                    'action_url' => route('hrm.expenses.index'),
                ]),
                'read_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\TrainingNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'training',
                    'title' => 'Training Session Reminder',
                    'message' => 'Your training session "Advanced Laravel" starts tomorrow at 10:00 AM.',
                    'action_url' => route('hrm.trainings.my-trainings'),
                ]),
                'read_at' => null,
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\DocumentExpiryNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'document',
                    'title' => 'Document Expiring Soon',
                    'message' => 'Your passport will expire in 30 days. Please renew it.',
                    'action_url' => '#',
                ]),
                'read_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }

        $this->command->info('Sample notifications seeded successfully!');
    }
}
