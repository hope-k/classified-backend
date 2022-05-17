@component('mail::message')
# Hi There,

Click the button below to reset your password

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

<div class="text-red-500">This link expires in 5 minutes</div>



Alternatively you can use this link
<a class="text-info">{{$url}} </a>

Thanks,<br>
{{ config('app.name') }}
@endcomponent