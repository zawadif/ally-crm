{{-- @component('mail::message')
# Welcome Tennis Fights 

Tennis Fights team member invited you to become the team partner to play proposal and challenge etc, 

Click the invite button to register in our application.

@component('mail::button', ['url' => $url])
Accept Request for payment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
 --}}

@component('mail::message')

Hi,<br>
Tennis Fights team member has invited you as his/her team Member against the ladder {{$url['ladder']}}. You have to pay the ladder charges to become his team member.<br>
Kindly create your Tennis Fights account through App and click on the Team Member invitation from Notification section of the App to make the payment or click the invite button below to register your account and pay through web portal.
Please contact us for any query, we are always happy to help you.

@component('mail::button', ['url' => $url['url']])
Accept Request for payment
@endcomponent

Kind regards<br>
{{ config('app.name') }}
@endcomponent
