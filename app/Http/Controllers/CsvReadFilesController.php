<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Models\csvReadFiles;




class CsvReadFilesController extends Controller
{
    public function profitLossCalculation(){

        // if (!($fp=fopen(public_path() . "/unread/sampl_data.csv", "r"))) {
        //     return "Can't open file...";
        // }
        
        $fileName = 'sample_data.csv';    
        $fp=fopen(public_path() . "/unread"."/".$fileName, "r");

        //FOR STORE FILE NAME INTO DATABASE
        $data = array();
        $data['file_name'] = $fileName;
        $data['read_date_time'] = \Carbon\Carbon::now()->toDateTimeString();
        $isSaved = csvReadFiles::insertGetId($data);
        if(!$isSaved){
            return "not store into DB";
        }

        $key = fgetcsv($fp,"1024",",");
        $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
            $json[] = array_combine($key, $row);
        }
    
        $grouped_array = array();
        foreach ($json as $element) {
            $grouped_array[$element["Month/Year"]][] = $element;
        }

        //FOR SORTING CSV FILE (MONTH AND YEAR WISE)
        uksort($grouped_array, function($a1, $a2) {
            $monthYeara1 = explode('-', $a1);
            $a_montha1 = $monthYeara1[0];
            $a_yeara1 = $monthYeara1[1];
            $dateA1 = date_parse($a_montha1);
            $monthNumberA1 = $dateA1['month'];
            $dA1=mktime(0, 0, 0, $monthNumberA1, 1, $a_yeara1);
    
            $monthYeara2 = explode('-', $a2);
            $a_montha2 = $monthYeara2[0];
            $a_yeara2 = $monthYeara2[1];
            $dateA2 = date_parse($a_montha2);
            $monthNumberA2 = $dateA2['month'];
            $dA2=mktime(0, 0, 0, $monthNumberA2, 1, $a_yeara2);
    
            return $dA1 - $dA2;
        });

        //CALCULATION REMAINING QTY
        //CALULATION PROFIT LOSS
        $stock_history = array();
        $modified_array = array();
        $remaining_stock = 0;
        foreach ($grouped_array as $month_transcation_arr) {
            $temp_arr = array();
            foreach($month_transcation_arr as $data_obj) {
                if ($data_obj["Type"] == "1") {
                    $remaining_stock += $data_obj["Qty"];
                    $temp_arr["buyQty"] = $data_obj["Qty"];
                    $temp_arr["buyTotal"] = $data_obj["Total"];
                    $temp_arr["buyRate"] = $data_obj["Rate"];
                    array_push($stock_history ,array($data_obj["Qty"], $data_obj["Rate"]));
                }
                if ($data_obj["Type"] == "2") {
                    $remaining_stock -= $data_obj["Qty"];
                    $temp_arr["sellQty"] = $data_obj["Qty"];
                    $temp_arr["sellTotal"] = $data_obj["Total"];
                    $temp_arr["sellRate"] = $data_obj["Rate"];
                }
            }
            $temp_arr["Month/Year"] = $month_transcation_arr[0]["Month/Year"];
            $temp_arr["remaining_stock"] = $remaining_stock;
            $sellTotal = $temp_arr["sellTotal"];
            $sellQty = $temp_arr["sellQty"];
            $ptl = 0;
            for($i = 0; $i < count($stock_history); $i++) {
                if ($stock_history[$i][0] == 0) {
                    continue;
                }
                if ($stock_history[$i][0] < $sellQty) {
                    $ptl += ($stock_history[$i][0] * $stock_history[$i][1]);
                    $sellQty -= $stock_history[$i][0];
                    $stock_history[$i][0] = 0;
                    continue;
                }
                if ($stock_history[$i][0] > $sellQty) {
                    $stock_history[$i][0] -= $sellQty;
                    $ptl += ($sellQty * $stock_history[$i][1]);
                    break;
                }
            }
            $temp_arr["PTL"] = $sellTotal - $ptl; 
            array_push($modified_array, $temp_arr);
        }
        fclose($fp);

        //FOR MOVE FILE FROM UNREAD TO READ FOLDER IN PUBLIC DIRECTORY
        File::move(public_path('unread/'.$fileName), public_path('read/'.$fileName));

        return view('profit_loss_defination', compact('modified_array'));
    }   
}