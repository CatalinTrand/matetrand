<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 11:26
 */

namespace App\Materom\Orders;


class VOrder
{

    public $ebeln;     // Purchase order number
    public $vbeln;     // External ssales order number (CCCCCCCCCC, SALESORDER or !REPLENISH)
    public $vbeln_int; // Internal sales order number array (SALESORDER)
    public $items;     // purchase order items linked with internal sales order



}