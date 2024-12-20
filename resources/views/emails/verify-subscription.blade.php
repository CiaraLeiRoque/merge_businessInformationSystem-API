<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Subscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .email-header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        .email-footer {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
        .verify-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }
        .verify-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            Verify Your Subscription
        </div>
        <div class="email-body">
            <p>Thank you for subscribing to our service! We're excited to have you on board.</p>
            <p>To complete your subscription, please verify your email address by clicking the button below:</p>
            <a href="{{ $url }}" class="verify-button">Verify Email</a>
        </div>
        <div class="email-footer">
            <p>If you did not subscribe to this service, please ignore this email.</p>
        </div>
    </div>
</body>
</html>
