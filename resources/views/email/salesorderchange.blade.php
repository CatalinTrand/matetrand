<h3>{{__("Buna ziua")}} {{$user->agent}},<br><br>
    {{__("Comanda de vanzare")}} <b>{{$vbeln}}/{{$posnr}}</b> {{__("a fost inlocuita cu")}} <b>{{$newposnr}}.</b><br>
</h3>
<h4>{{__("Va rugam faceti click")}} <a href="http://srm.materom.ro/orders">{{__("aici")}}</a>&nbsp;
    {{ __("pentru a o procesa in continuare")}}&nbsp;
    {{ __("in sistemul")}}&nbsp;
    {{ \App\Materom\System::$system_name }}.&nbsp;
</h4>
{!! \App\Materom\Mailservice::orderHistory($user, $vbeln, $posnr) !!}
<br><br><br>
{{__("Acest mail este generat automat de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="http://srm.materom.ro/images/logo.png">
