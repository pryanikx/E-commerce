<div style="font-family: Arial, sans-serif;">
    <h2 style="color: #dc3545;">{{ __('notifications.catalog_export_error') }}</h2>

    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3>{{ __('notifications.error_details') }}:</h3>
        <ul>
            <li><strong>{{ __('notifications.export_id') }}:</strong> {{ $exportId }}</li>
            <li><strong>{{ __('notifications.error_time') }}:</strong> {{ $currentTime }}</li>
            <li><strong>{{ __('notifications.error_description') }}:</strong> {{ $errorMessage }}</li>
        </ul>
    </div>

    <p style="color: #721c24;">
        {{ __('notifications.export_failure_message') }}
    </p>

    <hr style="margin: 30px 0;">
    <p style="color: #6c757d; font-size: 12px;">
        {{ __('notifications.automatic_notification') }}
        {{ __('notifications.sent_time') }}: {{ $currentTime }}
    </p>
</div> 