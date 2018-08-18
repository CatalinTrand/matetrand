<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::delete("delete from pitems");
        DB::delete("delete from porders");
        DB::insert("insert into porders (id, nof, wtime, ctime, vbeln, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values ('f1', FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'S000000001', 'P000000001', 'A01', 'L000000001', 'Furnizor 1')");
        DB::insert("insert into porders (id, nof, wtime, ctime, vbeln, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values ('f1', FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'S000000001', 'P000000002', 'A01', 'L000000002', 'Furnizor 2')");
        DB::insert("insert into porders (id, nof, wtime, ctime, vbeln, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values ('f1', FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'S000000002', 'P000000003', 'A01', 'L000000002', 'Furnizor 2')");

        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000001', '00010', '00010', 'IDNLF1')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000001', '00020', '00030', 'IDNLF2')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000002', '00030', '00050', 'IDNLF3')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000002', '00040', '00070', 'IDNLF4')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000003', '00050', '00090', 'IDNLF5')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000003', '00060', '00100', 'IDNLF6')");

    }
}
