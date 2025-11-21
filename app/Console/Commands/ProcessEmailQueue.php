<?php
// app/Console/Commands/ProcessEmailQueue.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailQueue;
use App\Services\GoogleMailService;
use Illuminate\Support\Facades\Log;

class ProcessEmailQueue extends Command
{
    protected $signature = 'email:process-queue';
    protected $description = 'Process pending emails in the queue';

    protected $googleMailService;

    public function __construct(GoogleMailService $googleMailService)
    {
        parent::__construct();
        $this->googleMailService = $googleMailService;
    }

    public function handle()
    {
        $this->info('Processing email queue...');

        $emails = EmailQueue::pending()
                           ->orderBy('created_at', 'asc')
                           ->limit(10)
                           ->get();

        if ($emails->isEmpty()) {
            $this->info('No pending emails to process.');
            return 0;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($emails as $email) {
            $this->info("Sending email to: {$email->recipient_email}");

            $email->incrementAttempts();

            try {
                $success = $this->googleMailService->sendEmail(
                    $email->recipient_email,
                    $email->subject,
                    $email->body,
                    $email->recipient_name
                );

                if ($success) {
                    $email->markAsSent();
                    $this->info("✓ Email sent successfully to {$email->recipient_email}");
                    $successCount++;
                } else {
                    $email->markAsFailed('Failed to send via Gmail API');
                    $this->error("✗ Failed to send email to {$email->recipient_email}");
                    $failCount++;
                }
            } catch (\Exception $e) {
                $email->markAsFailed($e->getMessage());
                $this->error("✗ Error sending email to {$email->recipient_email}: {$e->getMessage()}");
                $failCount++;
                
                Log::error('Email sending error', [
                    'queue_id' => $email->queue_id,
                    'email' => $email->recipient_email,
                    'error' => $e->getMessage()
                ]);
            }

            // Small delay between emails to avoid rate limiting
            sleep(1);
        }

        $this->info("\nProcessing complete:");
        $this->info("✓ Success: {$successCount}");
        $this->error("✗ Failed: {$failCount}");

        return 0;
    }
}