<h3>{{__("Buna ziua")}} {{$admin->username}},<br><br></h3>
{{__("Lista furnizorilor/referentilor")}} {{__("din sistemul")}} {{\App\Materom\System::$system_name}} {{__("avand comenzi/pozitii deschise la data trimiterii acestui mail:")}}<br><br>
{!! \App\Materom\Mailservice::AdminReminderMessage($admin, $reminders) !!}
<br><br><br>
{{__("Ati primit acest mail deoarece sunteti inregistrat in sistemul SRM ca Administrator.")}}<br>
{{__("Acest mail este generat automat si periodic de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="http://srm.materom.ro/images/logo.png">