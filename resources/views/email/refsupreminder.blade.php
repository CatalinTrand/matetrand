<h3>{{__("Buna ziua")}} {{$user->username}},<br><br></h3>
<br>
{!! \App\Materom\Mailservice::RefSupReminderMessage($user, $norders, $nitems) !!}
<br><br><br>
{{__("Acest mail este generat automat si periodic de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="http://srm.materom.ro/images/logo.png">