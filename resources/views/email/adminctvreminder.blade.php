<h3>{{__("Buna ziua")}} {{$user->username}},<br><br>
    {{__("Urmatorii CTV au fost notificati de necesitatea unor actiuni pentru comenzile listate mai jos")}}.<br><br>
</h3>
<br>
{!! \App\Materom\Mailservice::AdminCTVReminderList($user, $mails) !!}
<br><br><br>
{{__("Ati primit acest mail deoarece sunteti inregistrat in sistemul SRM ca Administrator CTV")}}.<br>
{{__("Acest mail este generat automat si periodic de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="http://srm.materom.ro/images/logo.png">