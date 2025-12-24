<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SendBulkNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $notificationData;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $users, array $notificationData)
    {
        $this->users = $users;
        $this->notificationData = $notificationData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("SendBulkNotificationsJob: Sending to {$this->users->count()} users");

        foreach ($this->users as $user) {
            try {
                $user->notify(new \App\Notifications\CustomNotification($this->notificationData));
            } catch (\Exception $e) {
                Log::error("SendBulkNotificationsJob: Failed to send to user {$user->id}: " . $e->getMessage());
            }
        }

        Log::info("SendBulkNotificationsJob: Completed");
    }
}
