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

    $item_id = empty($_REQUEST["item_id"]) ? "" : $_REQUEST["item_id"];
    $item_title = empty($_REQUEST["item_title"]) ? "" : $_REQUEST["item_title"];
    $item_description = empty($_REQUEST["item_description"]) ? "" : $_REQUEST["item_description"];
    $item_disclaimer = empty($_REQUEST["item_disclaimer"]) ? "" : $_REQUEST["item_disclaimer"];
    $item_price = empty($_REQUEST["item_price"]) ? "" : $_REQUEST["item_price"];
    $item_category = empty($_REQUEST["item_category"]) ? "" : $_REQUEST["item_category"];
    $item_allergie = empty($_REQUEST["item_allergie"]) ? "" : $_REQUEST["item_allergie"];
    $item_addon = empty($_REQUEST["item_addon"]) ? "" : $_REQUEST["item_addon"];

    $dataObj = new UserClass();

    if (empty($item_id)) {
        return ResponseClass::ResponseMessage("2", "Please enter item id", "False");
    } else {
        $wr = '';
        if (!empty($item_title)) {
            $wr .= "item_title='$item_title',";
        }
        if (!empty($item_description)) {
            $wr .= "item_description='$item_description',";
        }
        if (!empty($item_disclaimer)) {
            $wr .= "item_disclaimer='$item_disclaimer',";
        }
        if (!empty($item_price)) {
            $wr .= "item_price='$item_price',";
        }

        $wr = rtrim($wr, ',');

        $result = $dataObj->edit_item($item_id, $wr);

        if ($result == true) {
            if (!empty($_FILES["item_image"]["name"])) {
                $image = $dataObj->delete_i_image($item_id);
//                print_r($image);
//                die;
                if ($image == true) {
                    for ($i = 0; $i < count($_FILES['item_image']['name']); $i++) {
                        $imagePath = CommanClass::mix_item_image_upload($_FILES["item_image"]["name"][$i], $_FILES["item_image"]["tmp_name"][$i]);
                        $dataObj->add_item_image($item_id, $imagePath, $item_created);
                    }
                }
            }

            if (!empty($item_category)) {
                $category = $dataObj->delete_item_category($item_id);
                if ($category == true) {
                    $categoryArray = explode(',', $item_category);
                    foreach ($categoryArray as $key => $value) {
                        $dataObj->add_item_category($item_id, $value);
                    }
                }
            }

            if (!empty($item_allergie)) {

                if ($item_allergie == 'nil') {
                    $allergie = $dataObj->delete_item_allergie($item_id);
                } else {
                    $allergie = $dataObj->delete_item_allergie($item_id);
                    if ($allergie == true) {
                        $allergieArray = explode(',', $item_allergie);
                        foreach ($allergieArray as $key => $value) {
                            $dataObj->add_item_allergie($item_id, $value);
                        }
                    }
                }
            }

            if (!empty($item_addon)) {

                if ($item_addon == 'nil') {
                     $addon = $dataObj->delete_item_addon($item_id);
                } else {
                    $addon = $dataObj->delete_item_addon($item_id);
                    if ($addon == true) {
                        $addonArray = explode(',', $item_addon);
                        foreach ($addonArray as $key => $value) {
                            $dataObj->add_item_addon($item_id, $value);
                        }
                    }
                }
            }

            $item_Detail['ResponseCode'] = '1';
            $item_Detail['ResponseMsg'] = 'Item edited successfully.';
            $item_Detail['Result'] = 'True';

            echo json_encode($item_Detail);
        } else {
            return ResponseClass::ResponseMessage("3", "Error while item edited", "False");
        }
    }
} else {
    return ResponseClass::ResponseMessage("2", "Invalid method", "False");
}
?>
