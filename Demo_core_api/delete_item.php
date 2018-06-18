<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('conn.php');
include('classes/userclass.php');

$item_Detail = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $item_id = empty($_REQUEST["item_id"]) ? "" : $_REQUEST["item_id"];
    $dataObj = new UserClass();

    if (empty($item_id)) {
        return ResponseClass::ResponseMessage("2", "Please enter item id", "False");
    } else {
        $get_item = mysqli_fetch_array($dataObj->get_item_on_id($item_id));

        if (!empty($get_item)) {
            $result = $dataObj->delete_item($item_id);
            if ($result == true) {
                return ResponseClass::ResponseMessage("1", "Item deleted success", "True");
            } else {
                return ResponseClass::ResponseMessage("2", "Error while Item delete", "False");
            }
        } else {
            return ResponseClass::ResponseMessage("2", "Item not exist", "False");
        }
    }
} else {
    return ResponseClass::ResponseMessage("2", "Invalid method", "False");
}
?>
