<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\EmailQueue;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     * Send email verification
     */
    public function sendEmailVerification($user, $verificationUrl)
    {
        try {
            $emailBody = $this->getEmailVerificationTemplate(
                $user->first_name ?? $user->name,
                $verificationUrl
            );
            
            Mail::html($emailBody, function ($message) use ($user) {
                $message->to($user->email, $user->first_name ?? $user->name)
                        ->subject('Verify Your Email - SLSU Boarding House System');
            });

            Log::info('Email verification sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notify user of account approval
     */
    public function notifyUserApproved($user)
    {
        // Create in-app notification
        $this->createNotification(
            $user->id,
            'account_approval',
            'Account Approved!',
            'Congratulations! Your account has been approved. You can now access all features of the platform.'
        );

        // Send HTML email
        try {
            $emailBody = $this->getApprovalEmailTemplate($user->first_name ?? $user->name);
            
            Mail::html($emailBody, function ($message) use ($user) {
                $message->to($user->email, $user->first_name ?? $user->name)
                        ->subject('Account Approved - SLSU Boarding House System');
            });

            Log::info('Approval email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send approval email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
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
                $emailBody = $this->getAdminNewRegistrationEmailTemplate(
                    $admin->first_name ?? $admin->name,
                    $userName,
                    $userEmail
                );

                Mail::html($emailBody, function ($message) use ($admin) {
                    $message->to($admin->email, $admin->first_name ?? $admin->name)
                            ->subject('New User Registration - SLSU Boarding House System');
                });
            } catch (\Exception $e) {
                Log::error("Failed to send email to admin {$admin->email}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Admins notified of new registration', [
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

        // Send HTML email
        try {
            $emailBody = $this->getRejectionEmailTemplate($user->first_name ?? $user->name, $reason);
            
            Mail::html($emailBody, function ($message) use ($user) {
                $message->to($user->email, $user->first_name ?? $user->name)
                        ->subject('Account Registration Update - SLSU Boarding House System');
            });

            Log::info('Rejection email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get email verification template
     */
    protected function getEmailVerificationTemplate($userName, $verificationUrl)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background-color: white;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white; 
                    padding: 40px 20px; 
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 32px;
                    font-weight: 600;
                }
                .header .icon {
                    font-size: 60px;
                    margin-bottom: 10px;
                }
                .content { 
                    padding: 40px 30px;
                    background-color: white;
                }
                .content p {
                    margin: 15px 0;
                    font-size: 16px;
                    line-height: 1.8;
                }
                .verify-box {
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    padding: 30px;
                    margin: 30px 0;
                    border-radius: 8px;
                    text-align: center;
                }
                .verify-box p {
                    margin: 10px 0;
                    color: #555;
                }
                .button { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white !important; 
                    padding: 16px 40px; 
                    text-decoration: none; 
                    display: inline-block; 
                    margin: 20px 0; 
                    border-radius: 50px;
                    font-weight: bold;
                    font-size: 16px;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                    transition: all 0.3s ease;
                }
                .button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
                }
                .divider {
                    border-top: 1px solid #e0e0e0;
                    margin: 30px 0;
                }
                .info-box {
                    background-color: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px 20px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .info-box p {
                    margin: 5px 0;
                    font-size: 14px;
                    color: #856404;
                }
                .benefits {
                    background-color: #f8f9fa;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 8px;
                }
                .benefits ul {
                    margin: 10px 0;
                    padding-left: 20px;
                }
                .benefits li {
                    margin: 10px 0;
                    font-size: 15px;
                    color: #555;
                }
                .footer { 
                    text-align: center; 
                    padding: 30px 20px; 
                    color: #777; 
                    font-size: 13px;
                    background-color: #f8f9fa;
                    border-top: 1px solid #e0e0e0;
                }
                .footer a {
                    color: #667eea;
                    text-decoration: none;
                }
                .security-notice {
                    background-color: #e7f3ff;
                    border-left: 4px solid #2196F3;
                    padding: 15px 20px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .security-notice p {
                    margin: 5px 0;
                    font-size: 14px;
                    color: #0d47a1;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='icon'>✉️</div>
                    <h1>Verify Your Email</h1>
                </div>
                <div class='content'>
                    <p>Hi <strong>{$userName}</strong>,</p>
                    
                    <p>Welcome to the <strong>SLSU Boarding House System</strong>! We're excited to have you join our community.</p>
                    
                    <p>To complete your registration and unlock all features, please verify your email address by clicking the button below:</p>
                    
                    <div class='verify-box'>
                        <p style='font-size: 18px; font-weight: 600; color: #333; margin-bottom: 20px;'>
                            Click here to verify your email
                        </p>
                        <a href='{$verificationUrl}' class='button'>Verify Email Address</a>
                        <p style='margin-top: 20px; font-size: 13px; color: #777;'>
                            This link will expire in 60 minutes
                        </p>
                    </div>
                    
                    <div class='benefits'>
                        <strong style='font-size: 16px; color: #333;'>✨ What you can do after verification:</strong>
                        <ul>
                            <li>🏠 Browse verified boarding houses near SLSU campus</li>
                            <li>📸 View detailed property photos and information</li>
                            <li>📅 Make and manage booking requests</li>
                            <li>💬 Communicate directly with landlords</li>
                            <li>⭐ Leave reviews and ratings</li>
                            <li>🔔 Receive important notifications</li>
                        </ul>
                    </div>
                    
                    <div class='divider'></div>
                    
                    
                    <div class='security-notice'>
                        <p><strong>🔒 Security Tips:</strong></p>
                        <p>• Never share this verification link with anyone</p>
                        <p>• The SLSU team will never ask for your password</p>
                        <p>• Always verify the sender's email address</p>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p style='font-size: 14px; color: #666;'>
                        <strong>Having trouble clicking the button?</strong><br>
                        Copy and paste this link into your browser:
                    </p>
                    <p style='font-size: 13px; color: #667eea; word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 4px;'>
                        {$verificationUrl}
                    </p>
                    
                    <p style='margin-top: 40px; font-size: 15px;'>
                        If you need any assistance, feel free to contact our support team.
                    </p>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>SLSU Boarding House Team</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply directly to this message.</p>
                    <p style='margin-top: 10px;'>
                        <a href='" . url('/') . "'>Visit SLSU Boarding House System</a>
                    </p>
                    <p style='margin-top: 15px;'>&copy; " . date('Y') . " SLSU Boarding House System. All rights reserved.</p>
                    <p style='margin-top: 10px; font-size: 11px; color: #999;'>
                        Southern Luzon State University<br>
                        Lucban, Quezon, Philippines
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get approval email template
     */
    protected function getApprovalEmailTemplate($userName)
    {
        $loginUrl = url('/login');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    padding: 20px; 
                }
                .header { 
                    background-color: #4CAF50; 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                }
                .content { 
                    background-color: #f9f9f9; 
                    padding: 30px 20px;
                    border-left: 1px solid #ddd;
                    border-right: 1px solid #ddd;
                }
                .content p {
                    margin: 15px 0;
                }
                .button { 
                    background-color: #4CAF50; 
                    color: white !important; 
                    padding: 14px 28px; 
                    text-decoration: none; 
                    display: inline-block; 
                    margin: 20px 0; 
                    border-radius: 5px;
                    font-weight: bold;
                }
                .button:hover {
                    background-color: #45a049;
                }
                .features {
                    background-color: white;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 5px;
                    border: 1px solid #e0e0e0;
                }
                .features ul {
                    margin: 10px 0;
                    padding-left: 20px;
                }
                .features li {
                    margin: 8px 0;
                }
                .footer { 
                    text-align: center; 
                    padding: 20px; 
                    color: #777; 
                    font-size: 12px;
                    background-color: #f9f9f9;
                    border-radius: 0 0 5px 5px;
                    border: 1px solid #ddd;
                    border-top: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Account Approved!</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$userName}</strong>,</p>
                    
                    <p>Great news! Your account has been approved and you now have full access to the SLSU Boarding House System.</p>
                    
                    <div class='features'>
                        <strong>What you can do now:</strong>
                        <ul>
                            <li>Browse available boarding houses near SLSU campus</li>
                            <li>View detailed property information and photos</li>
                            <li>Make booking requests</li>
                            <li>Communicate with landlords</li>
                            <li>Leave reviews and ratings</li>
                        </ul>
                    </div>
                    
                    <p style='text-align: center;'>
                        <a href='{$loginUrl}' class='button'>Login to Your Account</a>
                    </p>
                    
                    <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
                    
                    <p>Welcome aboard!</p>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>SLSU Boarding House Team</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply directly to this message.</p>
                    <p>&copy; " . date('Y') . " SLSU Boarding House System. All rights reserved.</p>
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
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    padding: 20px; 
                }
                .header { 
                    background-color: #f44336; 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                }
                .content { 
                    background-color: #f9f9f9; 
                    padding: 30px 20px;
                    border-left: 1px solid #ddd;
                    border-right: 1px solid #ddd;
                }
                .content p {
                    margin: 15px 0;
                }
                .reason-box { 
                    background-color: #fff3cd; 
                    border-left: 4px solid #ff9800; 
                    padding: 20px; 
                    margin: 20px 0;
                    border-radius: 5px;
                }
                .reason-box strong {
                    color: #ff9800;
                    font-size: 16px;
                }
                .footer { 
                    text-align: center; 
                    padding: 20px; 
                    color: #777; 
                    font-size: 12px;
                    background-color: #f9f9f9;
                    border-radius: 0 0 5px 5px;
                    border: 1px solid #ddd;
                    border-top: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Account Registration Update</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$userName}</strong>,</p>
                    
                    <p>Thank you for your interest in registering with the SLSU Boarding House System.</p>
                    
                    <p>After careful review of your application, we regret to inform you that your account registration was not approved at this time.</p>
                    
                    <div class='reason-box'>
                        <strong>Reason for Rejection:</strong><br><br>
                        {$reason}
                    </div>
                    
                    <p>If you believe this was a mistake or have questions about this decision, please contact the system administrator for clarification.</p>
                    
                    <p>Thank you for your understanding.</p>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>SLSU Boarding House Team</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply directly to this message.</p>
                    <p>&copy; " . date('Y') . " SLSU Boarding House System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get admin new registration email template
     */
    protected function getAdminNewRegistrationEmailTemplate($adminName, $newUserName, $newUserEmail)
    {
        $reviewUrl = url('/admin/users/pending');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    padding: 20px; 
                }
                .header { 
                    background-color: #2196F3; 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                }
                .content { 
                    background-color: #f9f9f9; 
                    padding: 30px 20px;
                    border-left: 1px solid #ddd;
                    border-right: 1px solid #ddd;
                }
                .content p {
                    margin: 15px 0;
                }
                .user-info {
                    background-color: white;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 5px;
                    border: 1px solid #e0e0e0;
                }
                .user-info p {
                    margin: 8px 0;
                }
                .button { 
                    background-color: #2196F3; 
                    color: white !important; 
                    padding: 14px 28px; 
                    text-decoration: none; 
                    display: inline-block; 
                    margin: 20px 0; 
                    border-radius: 5px;
                    font-weight: bold;
                }
                .button:hover {
                    background-color: #0b7dda;
                }
                .footer { 
                    text-align: center; 
                    padding: 20px; 
                    color: #777; 
                    font-size: 12px;
                    background-color: #f9f9f9;
                    border-radius: 0 0 5px 5px;
                    border: 1px solid #ddd;
                    border-top: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔔 New User Registration</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$adminName}</strong>,</p>
                    
                    <p>A new user has registered and is awaiting your approval to access the SLSU Boarding House System.</p>
                    
                    <div class='user-info'>
                        <strong>User Details:</strong>
                        <p><strong>Name:</strong> {$newUserName}</p>
                        <p><strong>Email:</strong> {$newUserEmail}</p>
                        <p><strong>Registration Date:</strong> " . date('F d, Y h:i A') . "</p>
                    </div>
                    
                    <p>Please log in to the admin panel to review and approve or reject this registration.</p>
                    
                    <p style='text-align: center;'>
                        <a href='{$reviewUrl}' class='button'>Review Registration</a>
                    </p>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>SLSU Boarding House System</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply directly to this message.</p>
                    <p>&copy; " . date('Y') . " SLSU Boarding House System. All rights reserved.</p>
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
    }
}