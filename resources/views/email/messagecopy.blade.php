<h3>{{__("Buna ziua")}} {{$user->username}},<br><br>
    <b>{{$from}}</b> {{__("v-a transmis urmatorul mesaj pe portalul Materom SRM referitor la")." ".
    (empty($sorder) ? __("comanda")." ".$order : __("comanda client")." ".$sorder).
    ":"}}<br>
</h3>
<h4>{{$content}}</h4><br><br>
<h4>{{__("Va rugam faceti click")}} <a href="http://srm.materom.ro/orders">{{__("aici")}}</a> {{__("pentru a raspunde sau a procesa comanda.")}}.</h4>
<br><br><br>
{{__("Acest mail este generat automat de catre portalul SRM, va rugam nu raspundeti.")}}<br>
<img src="http://srm.materom.ro/images/logo.png">
