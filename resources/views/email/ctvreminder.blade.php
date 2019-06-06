<h3>{{__("Buna ziua")}} {{$user->agent}},<br><br>
    {{__("Urmatoarele comenzi necesita o actiune/un raspuns din partea dv. in portalul SRM.")}}<br><br>
</h3>
<br>
{!! \App\Materom\Mailservice::CTVReminderList($user, $items) !!}
<br><br><br>
{{__("Acest mail este generat automat si periodic de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="http://srm.materom.ro/images/logo.png">