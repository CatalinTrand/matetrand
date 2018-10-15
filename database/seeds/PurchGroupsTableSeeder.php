<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete("delete from sap_t024");

        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('001', 'Einkäufer 1', '06227/341285', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('002', 'Einkäufer 2', '06227/341285', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('003', 'Einkäufer 3', '06227/341285', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A01', 'ION MATEICIUC', '', 'ion.mateiciuc@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A02', 'Corneliu Zegrean', '', 'corneliu.zegrean@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A03', 'Albert Timot', '', 'albert.timot@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A04', 'Flaviu Vas', '', 'flaviu.vas@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A05', 'Ionut Hancu', '', 'ionut.hancu@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A06', 'Pavel Magdici', '', 'pavelmagdici@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A07', 'Sergiu Cirjeleanu', '', 'sergiu.cirjeleanu@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A08', 'Bogdan Gabor', '', 'bogdan.gabor@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A09', 'Valentin Scridon', '', 'valentin.scridon@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A10', 'SEPTIMIU FARAGO', '', 'septimiu.farago@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A11', 'VASILE SARBU', '', 'vasile.sarbu@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('A12', 'AM12', '', 'am12@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('B01', 'M 1', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('B02', 'M 2', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('B03', 'M 3', '', 'Ionut.Muntean@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('B04', 'M 4', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('B05', 'M 5', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('B06', 'Baciu Mirel', '', 'mirel@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C01', 'LUCIAN FODOR', '', 'lucian.fodor@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C02', 'Boer Sergiu', '', 'Sergiu.Boer@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C03', 'Bogdan Racz', '', 'Bogdan.Racz@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C04', '', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C05', 'Camelia Sarmasan', '', 'camelia.sarmasan@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C06', 'Bunau Octavia', '', 'octavia.bunau@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C07', 'Alin Laurentiu', '', 'Alin.Laurentiu@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C08', 'C08', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C09', 'c09', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('C10', 'c10', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('LOG', 'Logistica', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('MK1', 'MK1', '', 'claudiu.boanta@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('MK2', 'MK2', '', 'claudiu.boanta@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('OE1', 'Cosmin Bucur', '', 'cosminbucur@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('OE2', 'Szabolcs Szekely', '', 'szabolcs.szekely@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('OE3', '', '', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('PN', 'Piese Noi', '0365-401030', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('SH', 'Piese Second', '0365-401030', '');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('UES', 'ADRIAN TRIPON', '', 'adrian.tripon@materom.ro');");
        DB::insert("INSERT INTO sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ('Z1', 'Aprov. Diverse', '', '');");

    }
}
