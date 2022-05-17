@component('mail::message')
# Hi {{$firstname}},


@component('mail::panel')
Click the button below to verify your email
@endcomponent


@component('mail::button', ['url' => $url])
Verify Email
@endcomponent

<div class="text-red-500">This link expires in 5 minutes</div>



Alternatively you can use this link
<div class="text-info">{{$url}} </div>
Thanks,<br>
{{ config('app.name') }}
@endcomponent