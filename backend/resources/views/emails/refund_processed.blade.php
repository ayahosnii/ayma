<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Processed</title>
</head>
<body>
<h1>Refund Status: {{ $status }}</h1>

<p>Dear Customer,</p>

<p>We wanted to inform you that your refund for the order #{{ $refund->order_id }} has been processed.</p>

<p>Refund Amount: ${{ number_format($refund->amount, 2) }}</p>

<p>If you have any questions or concerns, please contact our support team.</p>

<p>Thank you for your patience!</p>

<p>Best regards,<br>Your Company</p>
</body>
</html>
