<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeederArchive extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete("delete from pitemchg_arch");
        DB::delete("delete from pitems_arch");
        DB::delete("delete from porders_arch");
        DB::insert("insert into porders_arch (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name, creation)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'PA00000001', 'A01', '0000020786', 'Furnizor 20786', '2018-10-06 00:00:00')");
        DB::insert("insert into porders_arch (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name, creation)" .
            " values (TRUE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'PA00000002', 'A01', '0000020786', 'Furnizor 20786', '2018-10-07 00:00:00')");
        DB::insert("insert into porders_arch (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name, creation)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'PA00000003', 'A02', '0000020786', 'Furnizor 20786', '2018-10-05 00:00:00')");
        DB::insert("insert into porders_arch (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name, creation)" .
            " values (TRUE, '2018-09-28 10:00:00', '2018-09-02 00:00:00', " .
            "         'PA00000014', 'A01', '0000016098', 'TENET SRL', '2018-10-01 00:00:00')");
        DB::insert("insert into porders_arch (nof, wtime, ctime, ebeln, ekgrp, lifnr, lifnr_name, creation)" .
            " values (FALSE, '2018-09-29 10:00:00', '2018-09-02 00:00:00', " .
            "         'PA00000015', 'A02', '0000020019', 'STAHLGRUBER GmbH', '2018-10-02 00:00:00')");

        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, creation)" .
            " values ('PA00000001', '00010', 'SA00000001', '00010', 'IDNLF1', 'Descriere piesa 1', 'F', '2018-10-06 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, creation)" .
            " values ('PA00000001', '00020', 'SA00000001', '00030', 'IDNLF2', 'Descriere piesa 2', 'F', '2018-10-06 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, creation)" .
            " values ('PA00000002', '00030', 'SA00000002', '00050', 'IDNLF3', 'Descriere piesa 3', 'R', '2018-10-07 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, creation)" .
            " values ('PA00000002', '00040', 'SA00000003', '00070', 'IDNLF4', 'Descriere piesa 4', 'R', '2018-10-07 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, creation)" .
            " values ('PA00000003', '00050', 'SA00000001', '00090', 'IDNLF5', 'Descriere piesa 5',  'F', '2018-10-05 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, creation)" .
            " values ('PA00000003', '00060', 'SA00000003', '00100', 'IDNLF6', 'Descriere piesa 6',  'F', '2018-10-05 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000014', '00110', 'REPLENISH', '', 'IDNLF5', 'Descriere piesa 5d',  10, 'buc', 'F', '2018-10-01 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000014', '00120', 'REPLENISH', '', 'IDNLF6', 'Descriere piesa 6c',  15, 'buc', 'F', '2018-10-01 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000014', '00130', 'REPLENISH', '', 'IDNLF5', 'Descriere piesa 5b',  20, 'buc', 'F', '2018-10-01 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000014', '00140', 'REPLENISH', '', 'IDNLF6', 'Descriere piesa 6b',  25, 'buc', 'F', '2018-10-01 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000015', '00200', 'SA00000010', '000100', 'IDNLF6',  'Descriere piesa 4', 150, 'buc', 'F', '2018-10-02 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000015', '00250', 'SA00000011', '000100', 'IDNLF5',  'Descriere piesa 5', 200, 'buc', 'R', '2018-10-02 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, qty, qty_uom, stage, creation)" .
            " values ('PA00000015', '00300', 'SA00000012', '000100', 'IDNLF6',  'Descriere piesa 6', 250, 'buc', 'R', '2018-10-02 00:00:00')");

        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason, creation)" .
            " values ('PA00000001', '00010', 'Q', '2018-09-01 18:01:00', 'f20786', 'Furnizor 20786', '50 buc', '40 buc', 'Stoc insuficient', '2018-10-06 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason, creation)" .
            " values ('PA00000001', '00010', 'P', '2018-09-01 18:02:00', 'f20786', 'Furnizor 20786', '25,33 RON/buc', '26,09 RON/buc', 'Crestere pret import', '2018-10-06 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, creation)" .
            " values ('PA00000002', '00040', 'D', '2018-09-01 18:03:00', 'Radu', 'Trandafir', '24.12.2018', '07.01.2019', '2018-10-07 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason, creation)" .
            " values ('PA00000014', '00040', 'X', '2018-09-01 18:03:00', 'Radu', 'Trandafir', '', '', 'Nu exista inca pret', '2018-10-01 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, creation)" .
            " values ('PA00000014', '00040', 'A', '2018-09-01 18:03:00', 'Radu', 'Trandafir', '24.12.2018', '07.01.2019', '2018-10-01 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason, creation)" .
            " values ('PA00000015', '00040', 'X', '2018-09-01 18:03:00', 'Radu', 'Trandafir', '', '', 'Nu se mai produce', '2018-10-02 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, creation)" .
            " values ('PA00000015', '00040', 'A', '2018-09-01 18:03:00', 'Radu', 'Trandafir', '', '', '2018-10-02 00:00:00')");

    }
}
