<h3>{{__("Buna ziua")}} {{$user->agent}},<br><br>
    {{__("Aveti notificari noi/necitite pentru urmatoarele comenzi din portalul SRM.")}}<br><br>
</h3>
<br>
{!! \App\Materom\Mailservice::CTVNotificationList($user, $items) !!}
<br><br><br>
{{__("Acest mail este generat automat si periodic de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="https://srm.materom.ro/images/logo.png">