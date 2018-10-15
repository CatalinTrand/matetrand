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
        DB::insert("insert into porders_arch (nof, wtime, ctime, ebeln, ekgrp, lifnr, archdate)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'PA00000001', 'A01', '0000020786', '2018-10-06 00:00:00')");

        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, archdate)" .
            " values ('PA00000001', '00010', 'SA00000001', '00010', 'IDNLF1', 'Descriere piesa 1', 'F', '2018-10-06 00:00:00')");
        DB::insert("insert into pitems_arch (ebeln, ebelp, vbeln, posnr, idnlf, mtext, stage, archdate)" .
            " values ('PA00000001', '00020', 'SA00000001', '00030', 'IDNLF2', 'Descriere piesa 2', 'F', '2018-10-06 00:00:00')");

        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason, archdate)" .
            " values ('PA00000001', '00010', 'Q', '2018-09-01 18:01:00', 'f20786', 'Furnizor 20786', '50 buc', '40 buc', 'Stoc insuficient', '2018-10-06 00:00:00')");
        DB::insert("insert into pitemchg_arch (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason, archdate)" .
            " values ('PA00000001', '00010', 'P', '2018-09-01 18:02:00', 'f20786', 'Furnizor 20786', '25,33 RON/buc', '26,09 RON/buc', 'Crestere pret import', '2018-10-06 00:00:00')");

    }
}
