<div style="font-family: Arial, sans-serif;">
    <h2 style="color: #28a745;">{{ __('notifications.export_completed_successfully') }}</h2>

    <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3>{{ __('notifications.export_details') }}:</h3>
        <ul>
            <li><strong>{{ __('notifications.export_id') }}:</strong> {{ $exportId }}</li>
            <li><strong>{{ __('notifications.storage_file') }}:</strong> {{ $s3Key }}</li>
            <li><strong>{{ __('notifications.export_time') }}:</strong> {{ $currentTime }}</li>
        </ul>
    </div>

    <div style="background-color: #e2e3e5; border: 1px solid #d6d8db; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3>{{ __('notifications.export_statistics') }}:</h3>
        <ul>
            <li><strong>{{ __('notifications.total_products_exported') }}:</strong> {{ $stats['total_products'] ?? 0 }}</li>
            <li><strong>{{ __('notifications.products_with_images') }}:</strong> {{ $stats['products_with_images'] ?? 0 }}</li>
            <li><strong>{{ __('notifications.products_with_manufacturer') }}:</strong> {{ $stats['products_with_manufacturer'] ?? 0 }}</li>
            <li><strong>{{ __('notifications.products_with_category') }}:</strong> {{ $stats['products_with_category'] ?? 0 }}</li>
        </ul>
    </div>

    <p style="color: #155724;">
        {{ __('notifications.export_success_message') }}
    </p>

    <hr style="margin: 30px 0;">
    <p style="color: #6c757d; font-size: 12px;">
        {{ __('notifications.automatic_notification') }}
        {{ __('notifications.sent_time') }}: {{ $currentTime }}
    </p>
</div> 