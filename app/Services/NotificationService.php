<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\EmailQueue;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
class NotificationService
{
    /**
     * Create in-app notification
     */
    public function createNotification($userId, $type, $title, $message, $referenceId = null)
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'reference_id' => $referenceId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Queue email for sending
     */
    public function queueEmail($email, $name, $subject, $body)
    {
        try {
            return EmailQueue::create([
                'recipient_email' => $email,
                'recipient_name' => $name,
                'subject' => $subject,
                'body' => $body,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Notify all admins about new registration
     */
   public function notifyAdminsNewRegistration($newUserId, $userName, $userEmail)
{
    $admins = User::where('role', 'admin')->get();

    foreach ($admins as $admin) {

    // Create in-app notification
    $this->createNotification(
        $admin->id,
        'new_registration',
        'New User Registration',
        "{$userName} ({$userEmail}) has registered and is waiting for approval.",
        $newUserId
    );

    // Send HTML email
    try {
        Mail::html("
            Hello {$admin->name},<br><br>
            A new user, <strong>{$userName}</strong> ({$userEmail}), has registered and is pending approval.<br>
            Please log in to the admin panel to approve or reject the registration.<br><br>
            <a href='" . url('/admin/pending-registrations') . "'>View Pending Registrations</a><br><br>
            Thank you.
        ", function ($message) use ($admin) {
            $message->to($admin->email, $admin->name)
                    ->subject("New User Registration");
        });
    } catch (\Exception $e) {
        \Log::error("Failed to send email to admin {$admin->email}", [
            'error' => $e->getMessage()
        ]);
    }
}

    \Log::info('Admins notified of new registration', [
        'new_user_id' => $newUserId,
        'admin_count' => $admins->count()
    ]);
}

    /**
     * Notify user of account rejection
     */
    public function notifyUserRejected($user, $reason)
    {
        // Create in-app notification
        $this->createNotification(
            $user->id,
            'account_rejection',
            'Account Registration Update',
            "Your account registration was not approved. Reason: {$reason}"
        );

        // Queue email
        $emailBody = $this->getRejectionEmailTemplate($user->name, $reason);
        $this->queueEmail(
            $user->email,
            $user->name,
            'Account Registration Update - SLSU Boarding House',
            $emailBody
        );
    }

    /**
     * Get approval email template
     */
    protected function getApprovalEmailTemplate($userName)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { background-color: #f9f9f9; padding: 20px; }
                .button { background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; display: inline-block; margin: 20px 0; border-radius: 4px; }
                .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Account Approved!</h1>
                </div>
                <div class='content'>
                    <p>Dear {$userName},</p>
                    <p>Great news! Your account has been approved.</p>
                    <p>You can now log in to your account and start exploring available boarding houses near SLSU campus.</p>
                    <p style='text-align: center;'>
                        <a href='" . url('/login') . "' class='button'>Login to Your Account</a>
                    </p>
                    <p>If you have any questions, feel free to contact us.</p>
                    <p>Best regards,<br>SLSU Boarding House Team</p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get rejection email template
     */
    protected function getRejectionEmailTemplate($userName, $reason)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f44336; color: white; padding: 20px; text-align: center; }
                .content { background-color: #f9f9f9; padding: 20px; }
                .reason-box { background-color: #fff3cd; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Account Registration Update</h1>
                </div>
                <div class='content'>
                    <p>Dear {$userName},</p>
                    <p>Thank you for your interest in registering with SLSU Boarding House System.</p>
                    <p>After reviewing your application, we regret to inform you that your account registration was not approved at this time.</p>
                    <div class='reason-box'>
                        <strong>Reason:</strong><br>
                        {$reason}
                    </div>
                    <p>If you believe this was a mistake or have questions about this decision, please contact the administrator.</p>
                    <p>Best regards,<br>SLSU Boarding House Team</p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get user's unread notifications
     */
    public function getUserNotifications($userId, $unreadOnly = false)
    {
        $query = Notification::where('user_id', $userId);

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->orderBy('created_at', 'desc')->limit(50)->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('notification_id', $notificationId)
                                   ->where('user_id', $userId)
                                   ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
                          ->where('is_read', false)
                          ->update(['is_read' => true]);
    }protected function getAdminNewRegistrationEmailTemplate($adminName, $newUserName, $newUserEmail)
{
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2196F3; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f9f9f9; padding: 20px; }
            .button { background-color: #2196F3; color: white; padding: 12px 24px; text-decoration: none; display: inline-block; margin: 20px 0; border-radius: 4px; }
            .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New User Registration</h1>
            </div>
            <div class='content'>
                <p>Dear {$adminName},</p>
                <p>A new user has registered and is awaiting your approval:</p>
                <p><strong>Name:</strong> {$newUserName}<br>
                   <strong>Email:</strong> {$newUserEmail}</p>
                <p>Please log in to the admin panel to approve or reject this user.</p>
                <p style='text-align: center;'>
                    <a href='" . url('/admin/users/pending') . "' class='button'>Review Registration</a>
                </p>
                <p>Best regards,<br>SLSU Boarding House Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

}