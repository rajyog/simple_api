<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('conn.php');
include('classes/userclass.php');
$item_created = date('Y-m-d H:i:s');
$item_Detail = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $item_title = mysqli_real_escape_string($con,empty($_REQUEST["item_title"]) ? "" : $_REQUEST["item_title"]);
    $item_description = mysqli_real_escape_string($con,empty($_REQUEST["item_description"]) ? "" : $_REQUEST["item_description"]);
    $item_disclaimer = mysqli_real_escape_string($con,empty($_REQUEST["item_disclaimer"]) ? "" : $_REQUEST["item_disclaimer"]);
    $item_price = empty($_REQUEST["item_price"]) ? "" : $_REQUEST["item_price"];
    $item_category = empty($_REQUEST["item_category"]) ? "" : $_REQUEST["item_category"];
    $item_allergie = empty($_REQUEST["item_allergie"]) ? "" : $_REQUEST["item_allergie"];
    $item_addon = empty($_REQUEST["item_addon"]) ? "" : $_REQUEST["item_addon"];
    
    $dataObj = new UserClass();
    if (empty($truck_id)) {
        return ResponseClass::ResponseMessage("2", "Please enter truck id", "False");
    } elseif (empty($item_title)) {
        return ResponseClass::ResponseMessage("2", "Please enter item title", "False");
    } elseif (empty($item_description)) {
        return ResponseClass::ResponseMessage("2", "Please enter item description", "False");
    } elseif (empty($item_disclaimer)) {
        return ResponseClass::ResponseMessage("2", "Please enter item disclaimer", "False");
    } elseif (empty($item_price)) {
        return ResponseClass::ResponseMessage("2", "Please enter item price", "False");
    } else {

        $result = $dataObj->add_item($item_title, $item_description, $item_disclaimer, $item_price, $item_created);
        $item_id = mysqli_insert_id($con);
        if ($result == true) {

            if (!empty($_FILES["item_image"]["name"])) {
                for ($i = 0; $i < count($_FILES['item_image']['name']); $i++) {
                    $imagePath = CommanClass::mix_item_image_upload($_FILES["item_image"]["name"][$i], $_FILES["item_image"]["tmp_name"][$i]);
                    $dataObj->add_item_image($item_id, $imagePath, $item_created);
                }
            }

            if (!empty($item_category)) {
                $categoryArray = explode(',', $item_category);
                foreach ($categoryArray as $key => $value) {
                    $dataObj->add_item_category($item_id, $value);
                }
            }

            $schedule_Detail['ResponseCode'] = '1';
            $schedule_Detail['ResponseMsg'] = 'Item added successfully.';
            $schedule_Detail['Result'] = 'True';

            echo json_encode($schedule_Detail);
        } else {
            return ResponseClass::ResponseMessage("3", "Error while schedule added", "False");
        }
    }
} else {
    return ResponseClass::ResponseMessage("2", "Invalid method", "False");
}
?>
