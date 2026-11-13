<div>{{ __('client::emails/password_restore.alert_message_p') }}</div>
<br>
<div>{{ __('client::emails/password_restore.restore_code_p') }} {{ $recoveryCode }}</div>
<br>
<div>{{ __('client::emails/password_restore.email_signature', ['name' =>env('APP_NAME')]) }}</div>
