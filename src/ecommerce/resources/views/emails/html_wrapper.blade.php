<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .email-header { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .email-content { line-height: 1.6; }
    </style>
</head>
<body>
    <div class="email-header">
        <strong>{{ __('notifications.from') }}:</strong> {{ $fromEmail }}<br>
        <strong>{{ __('notifications.to') }}:</strong> {{ $to }}<br>
        <strong>{{ __('notifications.subject') }}:</strong> {{ $subject }}<br>
        <strong>{{ __('notifications.time') }}:</strong> {{ now()->format('d.m.Y H:i:s') }}
    </div>
    <div class="email-content">
        {!! $content !!}
    </div>
</body>
</html> 