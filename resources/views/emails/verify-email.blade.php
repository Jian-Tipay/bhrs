{{-- resources/views/emails/verify-email.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: 'Public Sans', sans-serif;
            line-height: 1.5; 
            color: #697a8d; 
            margin: 0;
            padding: 20px;
            background-color: #f5f5f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(67, 89, 113, 0.12);
        }
        .header {
            padding: 24px 24px 16px;
            text-align: center;
            border-bottom: 1px solid #d9dee3;
        }
        .logo {
            font-size: 20px;
            font-weight: 600;
            color: #566a7f;
        }
        .content {
            padding: 32px 24px;
        }
        h1 {
            font-size: 20px;
            font-weight: 600;
            color: #566a7f;
            text-align: center;
            margin: 0 0 8px;
        }
        p {
            margin: 0 0 16px;
            font-size: 14px;
            line-height: 1.6;
        }
        .text-center {
            text-align: center;
        }
        .icon-box {
            text-align: center;
            margin: 24px 0;
        }
        .icon {
            display: inline-flex;
            width: 72px;
            height: 72px;
            background: #e7e7ff;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }
        .email-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: #696cff;
            color: #fff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
            margin: 24px 0 8px;
        }
        .btn:hover {
            background: #5f61e6;
        }
        .small {
            font-size: 13px;
            color: #a1acb8;
        }
        .list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .list p {
            margin-bottom: 12px;
            font-weight: 500;
        }
        ul {
            margin: 0;
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
            font-size: 14px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin: 16px 0;
            font-size: 13px;
        }
        .alert-warning {
            background: #fff3cd;
            border-left: 3px solid #ffab00;
        }
        .alert-info {
            background: #e7f3ff;
            border-left: 3px solid #03a9f4;
        }
        .link-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            word-break: break-all;
            margin: 16px 0;
        }
        .link-box a {
            color: #696cff;
            font-size: 13px;
        }
        hr {
            border: none;
            border-top: 1px solid #d9dee3;
            margin: 24px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #d9dee3;
        }
        .footer p {
            font-size: 12px;
            color: #a1acb8;
            margin: 8px 0;
        }
        .footer a {
            color: #696cff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('variables.templateName') ?? 'SLSU Boarding House' }}</div>
        </div>

        <div class="content">
            <h1>Verify Your Email Address 📧</h1>
            <p class="text-center">Thanks for registering! Please verify your email address by clicking the button below.</p>

            <div class="icon-box">
                <div class="icon">✉️</div>
            </div>

            <div class="email-box">
                <p class="small" style="margin-bottom: 4px;">Verification email sent to:</p>
                <p style="margin: 0; font-weight: 500; color: #566a7f;">{{ $userName }}</p>
            </div>

            <div class="text-center">
                <a href="{{ $verificationUrl }}" class="btn">Verify Email Address</a>
                <p class="small">This link expires in 60 minutes</p>
            </div>

            <div class="list">
                <p>After verification, you can:</p>
                <ul>
                    <li>Browse boarding houses near campus</li>
                    <li>View property details and photos</li>
                    <li>Make booking requests</li>
                    <li>Contact landlords</li>
                    <li>Leave reviews</li>
                </ul>
            </div>

            <hr>

            <div class="alert alert-warning">
                <strong>Didn't request this?</strong><br>
                If you didn't create an account, you can ignore this email.
            </div>

            <div class="alert alert-info">
                <strong>Security reminder:</strong><br>
                Never share this link with anyone. We'll never ask for your password via email.
            </div>

            <hr>

            <p style="font-size: 13px; font-weight: 500;">Having trouble with the button?</p>
            <div class="link-box">
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <p style="margin-top: 24px;">If you need help, contact our support team.</p>
            
            <p style="margin-top: 24px;">
                Best regards,<br>
                <strong>SLSU Boarding House Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please don't reply to this email.</p>
            <p><a href="{{ url('/') }}">Visit SLSU Boarding House System</a></p>
            <p>&copy; {{ date('Y') }} SLSU Boarding House System</p>
        </div>
    </div>
</body>
</html>