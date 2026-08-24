<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Response to your message</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 24px; color: #1e293b;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 28px; border: 1px solid #e2e8f0;">
        <h2 style="color: #4f46e5; margin-top: 0;">LeadDesk Support</h2>
        <p>Hi <strong>{{ $customerName }}</strong>,</p>
        <div style="background-color: #f1f5f9; padding: 16px; border-radius: 8px; font-size: 15px; line-height: 1.6; margin: 20px 0;">
            {!! nl2br(e($replyMessage)) !!}
        </div>
        <p style="font-size: 13px; color: #64748b;">This email was sent by the admin via LeadDesk.</p>
    </div>
</body>
</html>