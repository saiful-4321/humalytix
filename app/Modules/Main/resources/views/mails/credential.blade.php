<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Credentials</title>
    <style>
        /* Reset CSS */
        body, h1, h2, h3, h4, h5, h6, p, ul, ol, li {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        /* Container */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        /* Header */
        .header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        /* Footer */
        .footer {
            background-color: #f5f5f5;
            text-align: center;
            padding: 10px 0;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
        /* Content */
        .content {
            padding: 20px 0;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('common.cms.short_title') }}</h1>
        </div>
        <div class="content">
            <h2>Your Account Credentials</h2>
            <p>Dear {{ $user->name ?? ""}}</p>
            <p>We are excited to inform you that your account has been successfully created on Online BO Account Portal! Below are your login credentials:</p>
            <p><strong>Email Address:</strong> {{ $email ?? ""}}</p>
            <p><strong>Password:</strong> {{ $password ?? ""}}</p>
            <p>For security reasons, we recommend that you change your password after logging in for the first time. You can do this by navigating to your account settings.</p>
            <p>Please use the following link to access our website:</p>
            <p><a href="{{ url('/') }}" class="btn">Go to Online BO Account Portal</a></p>
            <p>If you have any questions or need further assistance, please don't hesitate to contact our support team at {{ config('common.support.email') }} or by phone at {{ config('common.support.phone') }}.</p>
            <p>Thank you for choosing Online BO Account Portal! We look forward to serving you.</p>
        </div>
        @include("Main::includes.footer")
    </div>
</body>
</html>
