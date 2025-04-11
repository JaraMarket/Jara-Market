
<h3>Customer Registered Successfully</h3>

<p>Dear {{ $customerData->firstname }},</p>

<p>Thank you for registering with us. Your account has been created successfully.</p>
<p>Default password is <strong>{{$customer->password }}</strong></p>
<p>Please use this password to log in and change it immediately for security reasons.</p>

Best regards,
<p>{{ config('app.name') }}</p>
