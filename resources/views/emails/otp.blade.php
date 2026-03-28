<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto;">
        <h2 style="color: #333;">Password Reset Request</h2>
        <p>Hello,</p>
        <p>You requested to reset your password. Use the code below to proceed:</p>

        <div style="background-color: #e0f2fe; color: #0284c7; font-size: 24px; font-weight: bold; text-align: center; padding: 15px; margin: 20px 0; border-radius: 5px;">
            {{ $otp }}
        </div>

        <p>This code will expire in 15 minutes.</p>
        <p style="color: #888; font-size: 12px;">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>
