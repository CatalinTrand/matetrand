<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete("delete from pitemchg");
        DB::delete("delete from pitems");
        DB::delete("delete from porders");
        DB::insert("insert into porders (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'P000000001', 'A01', '0000020786', 'Furnizor 20786')");
        DB::insert("insert into porders (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (TRUE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'P000000002', 'A01', '0000020786', 'Furnizor 20786')");
        DB::insert("insert into porders (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'P000000003', 'A02', '0000020786', 'Furnizor 20786')");
        DB::insert("insert into porders (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (TRUE, '2018-09-28 10:00:00', '2018-09-02 00:00:00', " .
            "         'P000000014', 'A01', '0000016098', 'TENET SRL')");
        DB::insert("insert into porders (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (FALSE, '2018-09-29 10:00:00', '2018-09-02 00:00:00', " .
            "         'P000000015', 'A02', '0000020019', 'STAHLGRUBER GmbH')");

        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage)" .
            " values ('P000000001', '00010', 'S000000001', '00010', 'IDNLF1', 'Descriere piesa 1', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage)" .
            " values ('P000000001', '00020', 'S000000001', '00030', 'IDNLF2', 'Descriere piesa 2', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage)" .
            " values ('P000000002', '00030', 'S000000002', '00050', 'IDNLF3', 'Descriere piesa 3', 'R')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage)" .
            " values ('P000000002', '00040', 'S000000003', '00070', 'IDNLF4', 'Descriere piesa 4', 'R')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage)" .
            " values ('P000000003', '00050', 'S000000001', '00090', 'IDNLF5', 'Descriere piesa 5',  'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage)" .
            " values ('P000000003', '00060', 'S000000003', '00100', 'IDNLF6', 'Descriere piesa 6',  'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000014', '00110', 'REPLENISH', '', 'IDNLF5', 'Descriere piesa 5d',  10, 'buc', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000014', '00120', 'REPLENISH', '', 'IDNLF6', 'Descriere piesa 6c',  15, 'buc', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000014', '00130', 'REPLENISH', '', 'IDNLF5', 'Descriere piesa 5b',  20, 'buc', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000014', '00140', 'REPLENISH', '', 'IDNLF6', 'Descriere piesa 6b',  25, 'buc', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000015', '00200', 'S000000010', '000100', 'IDNLF6',  'Descriere piesa 4', 150, 'buc', 'F')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000015', '00250', 'S000000011', '000100', 'IDNLF5',  'Descriere piesa 5', 200, 'buc', 'R')");
        DB::insert("insert into pitems (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage)" .
            " values ('P000000015', '00300', 'S000000012', '000100', 'IDNLF6',  'Descriere piesa 6', 250, 'buc', 'R')");

        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason)" .
            " values ('P000000001', '00010', 'Q', '2018-09-01 18:01:00', 'f20786', 'Furnizor 20786', '50 buc', '40 buc', 'Stoc insuficient')");
        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason)" .
            " values ('P000000001', '00010', 'P', '2018-09-01 18:02:00', 'f20786', 'Furnizor 20786', '25,33 RON/buc', '26,09 RON/buc', 'Crestere pret import')");
        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval)" .
            " values ('P000000002', '00040', 'D', '2018-09-01 18:03:00', 'Radu', 'Trandafir', '24.12.2018', '07.01.2019')");

    }
}
