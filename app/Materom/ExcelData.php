<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 30.06.2019
 * Time: 18:32
 */

namespace App\Materom;

use App\Materom\SAP\MasterData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelData
{

    public static function setDefaultXLSFields($file)
    {

        if (!DB::table("user_excel_fields_def")->where("file", $file)->exists()) {
            $sql = "insert into user_excel_fields_def (file, field, pos, name, link, type) values ";
            if (Auth::user()->role == "Furnizor") {

                $sql .= "('$file', 'EBELN', 1, 'Purchase order', 'item/ebeln', 'A'),";
                $sql .= "('$file', 'EBELP', 2, 'Purchase order item', 'item/ebelp', 'A'),";
                $sql .= "('$file', 'IDNLF', 3, 'Vendor material', 'item/idnlf', 'C'),";
                $sql .= "('$file', 'MTEXT', 4, 'Description', 'item/mtext', 'C')";
                $sql .= "('$file', 'MANUF', 5, 'Manufacturer', 'item/mfrnr', 'R'),";
                $sql .= "('$file', 'PURCH_QTY', 6, 'Quantity', 'item/qty', 'Q'),";
                $sql .= "('$file', 'PURCH_PRICE', 7, 'Price', 'item/purch_price', 'P'),";
                $sql .= "('$file', 'LFDAT', 8, 'Delivery date', 'item/lfdat', 'D'),";
                $sql .= "('$file', 'DELQTY', 9, 'Delivered quantity', 'item/delqty', 'q'),";
                $sql .= "('$file', 'GRDATE', 10, 'Goods receipt date', 'item/grdate', 'D')";

            } else {

                $sql .= "('$file', 'EBELN', 1, 'Purchase order', 'item/ebeln', 'A'),";
                $sql .= "('$file', 'EBELP', 2, 'Purchase order item', 'item/ebelp', 'A'),";
                $sql .= "('$file', 'IDNLF', 3, 'Vendor material', 'item/idnlf', 'C'),";
                $sql .= "('$file', 'MTEXT', 4, 'Description', 'item/mtext', 'C'),";
                $sql .= "('$file', 'LIFNR', 5, 'Supplier', 'hdr/lifnr', 'A'),";
                $sql .= "('$file', 'LIFNR_NAME', 6, 'Supplier name', 'hdr/lifnr', 'R'),";
                $sql .= "('$file', 'MANUF', 7, 'Manufacturer', 'item/mfrnr', 'R'),";
                $sql .= "('$file', 'EKGRP', 8, 'Reference', 'hdr/ekgrp', 'C'),";
                $sql .= "('$file', 'EKGRP_NAME', 9, 'Reference name', 'hdr/ekgrp', 'R'),";
                $sql .= "('$file', 'PURCH_QTY', 10, 'Quantity', 'item/qty', 'Q'),";
                $sql .= "('$file', 'PURCH_PRICE', 11, 'Purchase price', 'item/purch_price', 'P'),";
                $sql .= "('$file', 'SALES_PRICE', 12, 'Sales price', 'item/sales_price', 'P'),";
                $sql .= "('$file', 'LFDAT', 13, 'Delivery date', 'item/lfdat', 'D'),";
                $sql .= "('$file', 'ETADT', 14, 'ETA', 'item/etadt', 'D'),";
                $sql .= "('$file', 'BACKORDER', 15, 'Backorder', 'item/backorder', 'B'),";
                $sql .= "('$file', 'DELQTY', 16, 'Delivered quantity', 'item/delqty', 'q'),";
                $sql .= "('$file', 'GRDATE', 17, 'Goods receipt date', 'item/grdate', 'D'),";
                $sql .= "('$file', 'INB_DLV', 18, 'Inbound delivery', 'item/inb_dlv', 'C'),";
                $sql .= "('$file', 'INB_DLV_POSNR', 19, 'Inbound dlv. item', 'item/inb_dlv_posnr', 'C'),";
                $sql .= "('$file', 'INB_INV', 20, 'Inbound invoice', 'item/inb_inv', 'C'),";
                $sql .= "('$file', 'INB_INV_DATE', 21, 'Inbound inv. date', 'item/inb_inv_date', 'C'),";
                $sql .= "('$file', 'ETA_DELAYED_CHECK', 22, 'Delay dlv/ETA check', 'item/eta_delayed_check', 'B'),";
                $sql .= "('$file', 'ETA_DELAYED_DATE', 23, 'Delay ETA checks until', 'item/eta_delayed_date', 'D')";
            }

            DB::insert($sql);
        }
        $fields = DB::table("user_excel_fields_def")->where("file", $file)->get();
        if (!empty($fields) && !DB::table("user_excel_fields")->where(["id" => Auth::user()->id, "file" => $file])->exists()) {
            $sql = "";
            foreach ($fields as $field) {
                if (!empty($sql)) $sql .= ",";
                $sql .= "('".Auth::user()->id."', '$file', $field->pos, 1, '$field->field')";
            }
            if (!empty($sql)) {
                $sql = "insert into user_excel_fields (id, file, pos, checked, field) values " . $sql;
                DB::insert($sql);
            }
        }
    }

    public static function downloadXLSReport($lifnr, $orders)
    {
        $aorders = [];
        foreach ($orders as $order) $aorders[$order] = $order;
        $order_list = Orders::loadFromCache(null, null, false);

        $itemsArray = [];

        if (Auth::user()->role == "Furnizor") {
            array_push($itemsArray, [
                __("Purchase order"),
                __("Item"),
                __("Vendor mat."),
                __("Description"),
                __("Fabricant"),
                __("Quantity"), '',
                __("Price"), '',
                __("Delivery date"),
                __("Delivered quantity"),
                __("Goods receipt date"),
            ]);
        } else {
            array_push($itemsArray, [
                __("Purchase order"),
                __("Item"),
                __("Vendor mat."),
                __("Description"),
                __("Supplier"),
                __("Supplier name"),
                __("Refferal"),
                __("Refferal Name"),
                __("Fabricant"),
                __("Quantity"), '',
                __("Price"), '',
                __("Delivery date"),
                __("Delivered quantity"),
                __("Goods receipt date"),
            ]);
        }

        foreach ($order_list as $order) {
            if (!isset($aorders[$order->ebeln])) continue;
            foreach ($order->items as $item) {
                if (Auth::user()->role == "Furnizor") {
                    array_push($itemsArray, [
                        SAP::alpha_output($item->ebeln),
                        SAP::alpha_output($item->ebelp),
                        $item->idnlf,
                        $item->mtext,
                        ucfirst(strtolower(MasterData::getLifnrName($item->mfrnr))),
                        $item->qty,
                        $item->qty_uom,
                        $item->purch_price,
                        $item->purch_curr,
                        substr($item->lfdat, 0, 10),
                        explode(" ", $item->delqty)[0],
                        ($item->grdate == null ? "" : substr($item->grdate, 0, 10))
                    ]);
                } else {
                    array_push($itemsArray, [
                        SAP::alpha_output($item->ebeln),
                        SAP::alpha_output($item->ebelp),
                        $item->idnlf,
                        $item->mtext,
                        SAP::alpha_output($order->lifnr),
                        MasterData::getLifnrName($order->lifnr),
                        $order->ekgrp,
                        MasterData::getEkgrpName($order->ekgrp),
                        ucfirst(strtolower(MasterData::getLifnrName($item->mfrnr))),
                        $item->qty,
                        $item->qty_uom,
                        $item->purch_price,
                        $item->purch_curr,
                        substr($item->lfdat, 0, 10),
                        explode(" ", $item->delqty)[0],
                        ($item->grdate == null ? "" : substr($item->grdate, 0, 10))
                    ]);
                }
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($itemsArray, NULL, "A1");
        for ($i = 0; $i < count($itemsArray); ++$i)
            $sheet->getCellByColumnAndRow(1, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        // xls = application/vnd.ms-excel
        // xlsx = application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "Materom SRM report " . substr(now(), 0, 10) . " " . __("Supplier") . " " . SAP::alpha_output($lifnr);
        if (count($orders) == 1) $filename .= " PO " . $orders[0];
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;

    }


    public static function downloadXLSReport2($lifnr, $orders)
    {
        $filefielddefs = DB::table("user_excel_fields_def")->where(["file" => "xls01"])
            ->orderBy("pos")->get(["field", "link", "name", "type"]);
        $fieldlinks = [];
        $fieldnames = [];
        $fieldtypes = [];
        foreach($filefielddefs as $ffd) {
            $fieldnames[$ffd->field] = $ffd->name;
            $fieldlinks[$ffd->field] = $ffd->link;
            $fieldtypes[$ffd->field] = $ffd->type;
        }

        $aorders = [];
        foreach ($orders as $order) $aorders[$order] = $order;
        $order_list = Orders::loadFromCache(null, null, false);

        $itemsArray = [];

        $fields = DB::table("user_excel_fields")
            ->where(["id" => Auth::user()->id, "file" => "xls01", "checked" => 1])
            ->orderBy("pos")->get(["field"]);
        $row = [];
        foreach ($fields as $field) {
            array_push($row, __($fieldnames[$field->field]));
            if ($fieldtypes[$field->field] == 'P')
                array_push($row, ""); // currency
            if ($fieldtypes[$field->field] == 'Q')
                array_push($row, ""); // unit of measure
        }
        array_push($itemsArray, $row);

        foreach ($order_list as $order) {
            if (!isset($aorders[$order->ebeln])) continue;
            foreach ($order->items as $item) {
                $row = [];
                foreach ($fields as $field) {
                    $fld = explode("/", $fieldlinks[$field->field]);
                    switch (strtolower($fld[0])) {
                        case "item":
                            switch(strtolower($fld[1])) {
                                case "ebeln":
                                    array_push($row, SAP::alpha_output($item->ebeln));
                                    break;
                                case "ebelp":
                                    array_push($row, SAP::alpha_output($item->ebelp));
                                    break;
                                case "idnlf":
                                    array_push($row, $item->idnlf);
                                    break;
                                case "matnr":
                                    array_push($row, $item->matnr);
                                    break;
                                case "mtext":
                                    array_push($row, $item->mtext);
                                    break;
                                case "mfrnr":
                                    array_push($row, ucfirst(strtolower(MasterData::getLifnrName($item->mfrnr))));
                                    break;
                                case "qty":
                                    array_push($row, $item->qty);
                                    array_push($row, $item->qty_uom);
                                    break;
                                case "purch_price":
                                    array_push($row, $item->purch_price);
                                    array_push($row, $item->purch_curr);
                                    break;
                                case "sales_price":
                                    array_push($row, $item->sales_price);
                                    array_push($row, $item->sales_curr);
                                    break;
                                case "lfdat":
                                    array_push($row, substr($item->lfdat, 0, 10));
                                    break;
                                case "etadt":
                                    array_push($row, substr($item->etadt, 0, 10));
                                    break;
                                case "backorder":
                                    array_push($row, $item->backorder);
                                    break;
                                case "delqty":
                                    array_push($row, explode(" ", $item->delqty)[0]);
                                    break;
                                case "grdate":
                                    array_push($row,  ($item->grdate == null ? "" : substr($item->grdate, 0, 10)));
                                    break;
                                case "inb_dlv":
                                    array_push($row, SAP::alpha_output($item->inb_dlv));
                                    break;
                                case "inb_dlv_posnr":
                                    array_push($row, SAP::alpha_output($item->inb_dlv_posnr));
                                    break;
                                case "inb_inv":
                                    array_push($row, SAP::alpha_output($item->inb_inv));
                                    break;
                                case "inb_inv_date":
                                    array_push($row, ($item->inb_inv_date == null ? "" : substr($item->inb_inv_date, 0, 10)));
                                    break;
                                case "eta_delayed_check":
                                    array_push($row,  ($item->eta_delayed_check ? "Y" : "N" ));
                                    break;
                                case "eta_delayed_date":
                                    array_push($row, $item->eta_delayed_date_out);
                            }
                            break;
                        case "hdr":
                            switch(strtolower($fld[1])) {
                                case "lifnr": // type
                                    array_push($row,
                                        $fieldtypes[$field->field] != "R" ?
                                            SAP::alpha_output($order->lifnr) :
                                            MasterData::getLifnrName($order->lifnr));
                                    break;
                                case "ekgrp": // type
                                    array_push($row,
                                        $fieldtypes[$field->field] != "R" ?
                                            SAP::alpha_output($order->ekgrp) :
                                            MasterData::getEkgrpName($order->ekgrp));
                                    break;
                                case "bedat":
                                    array_push($row, substr($order->bedat, 0, 10));
                                    break;
                            }
                            break;
                    }
                }
                array_push($itemsArray, $row);
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($itemsArray, NULL, "A1");
        for ($i = 0; $i < count($itemsArray); ++$i)
            $sheet->getCellByColumnAndRow(1, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        // xls = application/vnd.ms-excel
        // xlsx = application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "Materom SRM report " . substr(now(), 0, 10) . " " . __("Supplier") . " " . SAP::alpha_output($lifnr);
        if (count($orders) == 1) $filename .= " PO " . $orders[0];
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;

    }


    public static function downloadXLSMassChange($lifnr, $orders)
    {

        $aorders = [];
        foreach ($orders as $order) $aorders[$order] = $order;
        $order_list = Orders::loadFromCache(null, null, false);

        $itemsArray = [];

        array_push($itemsArray, [
            __("Purchase order"),
            __("Item"),
            __("Current vendor mat."),
            __("Description"),
            __("New vendor mat."),
            __("Current quantity"), '',
            __("New quantity"),
            __("Current price"), '',
            __("New price"),
            __("Current delivery date"),
            __("New delivery date"),
        ]);

        foreach ($order_list as $order) {
            if (!isset($aorders[$order->ebeln])) continue;
            foreach ($order->items as $item) {
                array_push($itemsArray, [
                    SAP::alpha_output($item->ebeln),
                    SAP::alpha_output($item->ebelp),
                    $item->idnlf,
                    $item->mtext,
                    null,
                    $item->qty,
                    $item->qty_uom,
                    null,
                    $item->purch_price,
                    $item->purch_curr,
                    null,
                    substr($item->lfdat, 0, 10),
                    null
                ]);
            }
        }

        $nrows = count($itemsArray);
        $nrows1 = $nrows;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__("Mass changes"));
        $sheet->fromArray($itemsArray, NULL, "A1");
        $sheet->getStyle("A1:M1")->getFont()->setBold(true);
        $sheet->getStyle("A1:M1")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00C0E0F8');

        $sheet->getColumnDimension('A')->setWidth(17);
        for ($i = 0; $i < count($itemsArray); ++$i)
            $sheet->getCellByColumnAndRow(1, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->getStyle("A2:B" . $nrows1)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F0F0F0');

        // idnlf
        $sheet->getColumnDimension('C')->setWidth(19);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(19);
        $sheet->getStyle("C2:D" . $nrows1)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F0F0F0');
        $sheet->getStyle('D2:D' . $nrows1)->getAlignment()->setWrapText(true);
        $sheet->getStyle('D2:D' . $nrows1)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        for ($i = 0; $i < $nrows1; ++$i) {
            $sheet->getCellByColumnAndRow(3, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->getCellByColumnAndRow(5, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->getRowDimension($i + 2)->setRowHeight(16);
        }
        $sheet->getStyle('E2:E' . $nrows1)
            ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

        // qty
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(5);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getStyle("F2:G" . $nrows1)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F0F0F0');
        for ($i = 0; $i < $nrows1; ++$i) {
            $sheet->getCellByColumnAndRow(6, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->getCellByColumnAndRow(8, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        }
        $sheet->getStyle('F2:F' . $nrows1)->getNumberFormat()->setFormatCode("0");
        $sheet->getStyle('H2:H' . $nrows1)->getNumberFormat()->setFormatCode("0");
        $sheet->getStyle('H2:H' . $nrows1)
            ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

        // price
        $sheet->getColumnDimension('I')->setWidth(14);
        $sheet->getColumnDimension('J')->setWidth(5);
        $sheet->getColumnDimension('K')->setWidth(14);
        $sheet->getStyle("I2:J" . $nrows1)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F0F0F0');
        for ($i = 0; $i < $nrows1; ++$i) {
            $sheet->getCellByColumnAndRow(9, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->getCellByColumnAndRow(11, $i + 2)->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        }
        $sheet->getStyle('I2:I' . $nrows1)->getNumberFormat()->setFormatCode("0.00");
        $sheet->getStyle('K2:K' . $nrows1)->getNumberFormat()->setFormatCode("0.00");
        $sheet->getStyle('K2:K' . $nrows1)
            ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

        // date
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getStyle("L2:L" . $nrows1)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F0F0F0');
        $sheet->getStyle('L2:L' . $nrows1)->getNumberFormat()->setFormatCode("yyyy-mm-dd;@");
        $sheet->getStyle('M2:M' . $nrows1)->getNumberFormat()->setFormatCode("yyyy-mm-dd;@");
        $sheet->getStyle('M2:M' . $nrows1)
            ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

        $sheet->getStyle("A1:M" . $nrows1)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet_protection = $sheet->getProtection();
        $sheet_protection->setPassword('Materom2019');
        $sheet_protection->setSheet(true);
        $sheet_protection->setSort(true);
        $sheet_protection->setInsertRows(true);
//      $sheet_protection->setDeleteRows(true);
        $sheet_protection->setInsertColumns(true);
        $sheet_protection->setDeleteColumns(true);
        $sheet_protection->setFormatCells(true);

        $sheet->getStyle('A1:M' . $nrows1)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

        $sheet->getStyle('E2:E' . $nrows1)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        $sheet->getStyle('H2:H' . $nrows1)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $validation = $sheet->getCell('H2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
        $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_GREATERTHAN);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle(__("Wrong order item quantity"));
        $validation->setError(__("Entered data is not a valid quantity for a MATEROM purchase order item"));
        $validation->setPromptTitle(__("Acceptable quantities"));
        $validation->setPrompt(__("Only integer quantities > 0 are accepted"));
        $validation->setFormula1(0);
        for ($i = 1; $i < $nrows1; ++$i) {
            $sheet->getCellByColumnAndRow(8, $i + 2)->setDataValidation(clone $validation);
        }

        $sheet->getStyle('K2:K' . $nrows1)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $validation = $sheet->getCell('K2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DECIMAL);
        $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_GREATERTHAN);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle(__("Wrong order item price"));
        $validation->setError(__("Entered data is not a valid price for a MATEROM purchase order item"));
        $validation->setPromptTitle(__("Acceptable prices"));
        $validation->setPrompt(__("Only prices > 0.00 are accepted"));
        $validation->setFormula1(0);
        for ($i = 1; $i < $nrows1; ++$i) {
            $sheet->getCellByColumnAndRow(11, $i + 2)->setDataValidation(clone $validation);
        }

        $sheet->getStyle('M2:M' . $nrows1)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $validation = $sheet->getCell('M2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DATE);
        $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_GREATERTHAN);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle(__("Wrong order item delivery date"));
        $validation->setError(__("Entered data is not a valid delivery date for a MATEROM purchase order item"));
        $validation->setPromptTitle(__("Acceptable dates"));
        $validation->setPrompt(__("Only dates after 2019-07-01 are accepted"));
        $validation->setFormula1('=DATEVALUE("2019-07-01")');
        for ($i = 1; $i < $nrows1; ++$i) {
            $sheet->getCellByColumnAndRow(13, $i + 2)->setDataValidation(clone $validation);
        }


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        // xls = application/vnd.ms-excel
        // xlsx = application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "Materom SRM mass changes " . substr(now(), 0, 10) . " " . __("Supplier") . " " . SAP::alpha_output($lifnr);
        if (count($orders) == 1) $filename .= " PO " . $orders[0];
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;

    }

    public static function uploadXLSMassChange($spreadsheet)
    {
        $data = [];
        $row = 2;
        for ($i = 0; $i < 100; ++$i) {
            $fromrow = $row;
            $torow = $row + 99;
            $row += 100;
            $array = $spreadsheet->getActiveSheet()->rangeToArray(
                'A'.$fromrow.':M'.$torow,  // The worksheet range that we want to retrieve
                null,        // Value that should be returned for empty cells
                true,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                true,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                true         // Should the array be indexed by cell row and cell column
            );

            foreach($array as $row => $line) {
                if (!isset($line["A"]) || is_null($line["A"])) {
                    $i = 100;    // exit sheet
                    break;
                }
                $item = new \stdClass();
                $item->ebeln = $line["A"];
                if (strlen($item->ebeln) != 10 || !ctype_digit($item->ebeln))
                    return "Cell A".$row." ".__("is not a valid purchase order number");
                $porder = DB::table(System::$table_porders)->where("ebeln", $item->ebeln)->first();
                if ($porder == null)
                    return "Cell A".$row.": ".__("purchase order")." $item->ebeln"." ".__("does not exist");
                $item->ebelp = strval($line["B"]);
                if (is_null($item->ebelp) || !ctype_digit($item->ebelp) || strlen($item->ebelp) > 5)
                    return "Cell B".$row." ".__("is not a valid purchase order item number");
                $item->ebelp = str_pad($item->ebelp, "5", "0", STR_PAD_LEFT);
                $pitem = DB::table(System::$table_pitems)->where("ebeln", $item->ebeln)->where("ebelp", $item->ebelp)->first();
                if ($pitem == null)
                    return "Cell B".$row.": ".__("purchase order item")." $item->ebeln/".SAP::alpha_output($item->ebelp)." ".__("does not exist");
                $item->idnlf = $line["E"];
                $item->qty = $line["H"];
                if ($item->qty != null) {
                    if (!is_numeric($item->qty) || $item->qty < 0)
                        return "Cell H".$row." ".__("does not contain a correct quantity figure");
                }
                $item->price = $line["K"];
                if ($item->price != null) {
                    if (!is_numeric($item->price) || $item->price < 0)
                        return "Cell K".$row." does not contain a correct price";
                }
                $item->deldate = $line["M"];
                if ($item->deldate != null) {
//                    if (!\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($spreadsheet->getActiveSheet()->getCell("M".$row)))
//                        return "Error loading file: cell M".$row." does not contain a valid date";
                    try {
                        $item->deldate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item->deldate);
                    } catch (Exception $e) {
                        return __("Cell M").$row." "."does not contain a valid date";
                    }
                }
                if ($item->idnlf != null || $item->qty != null || $item->price != null || $item->deldate != null)
                    array_push($data, $item);
            }
        }

        if (empty($data)) return __("No changes requested in the file, no operation performed");

        $porders = [];
        $log = "";
        foreach($data as $item) {
            if (!isset($porders[$item->ebeln]))
                $porders[$item->ebeln] = Orders::readPOrder($item->ebeln);
            $porder = $porders[$item->ebeln];
            $pitem = $porder->items[$item->ebelp];
            $seconds = 0;
            if ((Auth::user()->role != "Administrator") && $pitem->owner == 0) {
                $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                    __("you are not allowed to change this order item");
                $log .= "\r\n".$line;
                continue;
            }
            if (!is_null($item->idnlf)) {
                if ($pitem->matnr_changeable == 1) {
                    Webservice::doChangeItem("idnlf", substr($item->idnlf, 0, 35), "",
                        $pitem->idnlf, $pitem->ebeln, $pitem->ebelp, $pitem->backorder, $seconds);
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("material code changed from")." ".$pitem->idnlf." ".__("to")." ".$item->idnlf;
                    $seconds++;
                } else {
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("material code is NOT changeable to")." ".$item->idnlf;
                    $log .= "\r\n".$line;
                }
            }
            if (!is_null($item->qty)) {
                if ($pitem->quantity_changeable == 1) {
                    Webservice::doChangeItem("qty", strval($item->qty), $pitem->qty_uom,
                        $pitem->qty, $pitem->ebeln, $pitem->ebelp, $pitem->backorder, $seconds);
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("quantity changed from")." $pitem->qty $pitem->qty_uom ".
                        __("to")." $item->qty $pitem->qty_uom";
                    $seconds++;
                } else {
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("quantity is NOT changeable to")." $item->qty $pitem->qty_uom";
                    $log .= "\r\n".$line;
                }
            }
            if (!is_null($item->price)) {
                if ($pitem->price_changeable == 1) {
                    Webservice::doChangeItem("purch_price", strval($item->price), $pitem->purch_curr,
                        $pitem->purch_price, $pitem->ebeln, $pitem->ebelp, $pitem->backorder, $seconds);
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("purchase price changed from")." $pitem->purch_price $pitem->purch_curr ".
                        __("to")." $item->price $pitem->purch_curr";
                    $seconds++;
                } else {
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("price is NOT changeable to")." $item->price $pitem->purch_curr";
                    $log .= "\r\n".$line;
                }
            }
            if (!is_null($item->deldate)) {
                $deldate = $item->deldate->format("Y-m-d");
                if ($pitem->delivery_date_changeable == 1) {
                    Webservice::doChangeItem("lfdat", $deldate, "",
                        $pitem->lfdat, $pitem->ebeln, $pitem->ebelp, $pitem->backorder, $seconds);
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("delivery date changed from")." ".substr($pitem->lfdat, 0, 10)." ".__("to")." ".$deldate;
                    $seconds += 2;
                } else {
                    $line = $pitem->ebeln."/".SAP::alpha_output($pitem->ebelp).": ".
                        __("delivery date is NOT changeable to")." ".$deldate;
                    $log .= "\r\n".$line;
                }
            }
        }

        if (!empty($log)) return substr($log, 2);
        return "OK";

    }

    public static function saveFieldSelection($file, $fieldlist)
    {
        DB::delete("delete from user_excel_fields where id = '".Auth::user()->id."' and file = '$file'");
        $pos = 0;
        foreach($fieldlist as $field) {
            if (empty($field)) continue;
            $pos++;
            $checked = substr($field, strlen($field) - 1, 1);
            $field = substr($field, 0, strlen($field) - 1);
            DB::insert("insert into user_excel_fields (id, file, pos, checked, field) values (" .
                "'".Auth::user()->id."', '$file', $pos, $checked, '$field')");
        }

    }

    public static function getFieldSelection($file)
    {
        $field_list = array();
        $fields = DB::table("user_excel_fields")->where(["id" => Auth::user()->id, "file" => $file])->orderBy("pos")->get();
        foreach($fields as $field) {
            unset($field->id);
            unset($field->file);
            unset($field->pos);
            $field->name = __(DB::table("user_excel_fields_def")->where(["file" => $file, "field" => $field->field])->value("name"));
            $field_list[] = $field;
        }
        $result = new \stdClass();
        $result->fields = $field_list;
        return json_encode($result);
    }

}