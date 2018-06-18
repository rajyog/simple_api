<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('conn.php');
include('classes/userclass.php');
$user_created = date('Y-m-d H:i:s');

$item_detail = array();
$dataObj = new UserClass();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $truck_id = empty($_REQUEST["truck_id"]) ? "" : $_REQUEST["truck_id"];
    if (empty($truck_id)) {
        return ResponseClass::ResponseMessage("2", "Please enter truck id", "False");
    } else {
        $result = $dataObj->get_category(); 
              
        $result_count = mysqli_num_rows($result);
        if ($result_count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {     
                $get_item = mysqli_fetch_array($dataObj->get_item_on_category($row['category_id']), MYSQLI_ASSOC);    
                
                $item_ids = ($get_item['item_ids'] != '') ? $get_item['item_ids'] : 0;               
                $item_details = array();
                $item = $dataObj->get_item($item_ids, $truck_id);
                while ($row1 = mysqli_fetch_assoc($item)) {
                
               	    $truck_id = $row1['truck_id'];
                    $get_price_increase = mysqli_fetch_assoc($dataObj->get_item_price_increase($truck_id, $user_created));
                    $percentage = $get_price_increase['percentage'];
                    
                    if (!empty($percentage)) {
                        $increase = ($row1['item_price'] * $percentage / 100);
                        $total_price = ( $row1['item_price'] + $increase);
                        $row1['item_price'] = $total_price;
                    } else {
                        $row1['item_price'] = $row1['item_price'];
                    }
                
                    $get_item_image = mysqli_fetch_assoc($dataObj->get_item_single_image($row1['item_id']));
                    if ($get_item_image['item_image'] == NULL) {
                        $row1['item_image'] = "";
                    } else {
                        $image_path = $dataObj->getItemsImageFullPath($get_item_image['item_image']);
                        $row1['item_image'] = $image_path;
                    }
                    
                    $item_details[] = $row1;
                }
                              
                if ($item_details == NULL) {
                    continue;
                }
                $row['items_info'] = $item_details;
                $item_detail[] = $row;
            }
            
           
        }
        if (empty($item_detail)) {
            return ResponseClass::ResponseMessage("2", "No any item available", "False");
        } else {
            return ResponseClass::successResponseInArray("item_detail", $item_detail, "1", "success", "True");
        }
    }
} else {
    return ResponseClass::ResponseMessage("2", "Invalid method", "False");
}
?>