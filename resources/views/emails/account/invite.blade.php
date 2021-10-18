@component('mail::message')
# Introduction

Hello Dear,

Your being invited to register in our website. Please click the link below<br>

@component('mail::button', ['url' => '/registration/$email'])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
