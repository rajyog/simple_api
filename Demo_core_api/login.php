<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('conn.php');
include('classes/userclass.php');
$user_created = date('Y-m-d H:i:s');
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $emailid = empty($_REQUEST["emailid"]) ? "" : $_REQUEST["emailid"];
    $password = empty($_REQUEST["password"]) ? "" : $_REQUEST["password"];
    $devicetoken = empty($_REQUEST["devicetoken"]) ? "" : $_REQUEST["devicetoken"];
    $devicetype = empty($_REQUEST["devicetype"]) ? "" : $_REQUEST["devicetype"];

    $dataObj = new UserClass();

    if (empty($emailid)) {
        return ResponseClass::ResponseMessage("2", "Please enter email", "False");
    } elseif (empty($password)) {
        return ResponseClass::ResponseMessage("2", "Please enter password", "False");
    } elseif (empty($devicetoken)) {
        return ResponseClass::ResponseMessage("2", "Please enter device token", "False");
    } elseif (empty($devicetype)) {
        return ResponseClass::ResponseMessage("2", "Please enter device type", "False");
    } else {

        $isUserMail = $dataObj->checkuserEmail($emailid);
        $isTruckMail = $dataObj->checktruckEmail($emailid);

        if ($isUserMail == true) {

            $result = mysqli_fetch_array($dataObj->getUserDetail($emailid));
            $get_password = $result['user_password'];
            $user_is_delete = $result['user_is_delete'];
            
            if($user_is_delete == '1'){
                 
                    $enPassword = $dataObj->encryptIt($password);
	            if ($enPassword != $get_password) {
	                return ResponseClass::ResponseMessage("2", "password invalid", "False");
	            }
	            $user_id = $result['user_id'];
	
	            $flag = 'user';
	            $edit_token = $dataObj->edit_devicetoken($result['user_id'], $devicetoken, $devicetype, $flag);
	
	            if ($edit_token == TRUE) {
	                $res = mysqli_fetch_array($dataObj->get_user_profile($user_id));
	
	                $data['user_id'] = $res['user_id'];
	                $data['user_firstname'] = $res['user_firstname'];
	                $data['user_lastname'] = $res['user_lastname'];
	                $data['user_password'] = $enPassword = $dataObj->decryptIt($res['user_password']);
	                $data['user_emailid'] = $res['user_emailid'];
	                $data['user_dateofbirth'] = $res['user_dateofbirth'];
	                $data['user_username'] = $res['user_username'];
	                $data['login_type'] = $res['login_type'];
	                $data['user_devicetoken'] = $res['user_devicetoken'];
	                $data['user_devicetype'] = $res['user_devicetype'];
	                $data['customer_id'] = $res['customer_id'];
	                $data['card_available'] = $res['is_card_available'];
	                $data['user_vault_amount'] = $result['user_vault_amount'];
	
	
	                $get_image = $res['user_image'];
	
	                if (empty($get_image)) {
	                    $data['user_image'] = "";
	                } else {
	                    $image_path = $dataObj->getUserImageFullPath($res['user_image']);
	                    $data['user_image'] = $image_path;
	                }
	            }
	
	
	
	
	//            $image_path = $dataObj->getUserImageFullPath($result['user_image']);
	//            $data['user_image'] = $image_path;
	
	            $user_detail['ResponseCode'] = '1';
	            $user_detail['ResponseMsg'] = 'Login successfully';
	            $user_detail['Result'] = 'True';
	            $user_detail['Data'] = $data;
	
	            echo json_encode($user_detail);
            
            }else{
	    	return ResponseClass::ResponseMessage("2", "You are block by Scavenger Behavior admin.", "False");	            
            }

        } else if ($isTruckMail == true) {
            $result = mysqli_fetch_array($dataObj->getTruckDetail($emailid));
            $get_password = $result['truck_password'];

            $enPassword = $dataObj->encryptIt($password);
            if ($enPassword != $get_password) {
                return ResponseClass::ResponseMessage("2", "password invalid", "False");
            }

            $truck_id = $result['truck_id'];

            $flag = 'truck';
            $edit_token = $dataObj->edit_devicetoken($result['truck_id'], $devicetoken, $devicetype, $flag);

            if ($edit_token == TRUE) {
                $res = mysqli_fetch_array($dataObj->get_truck_profile($truck_id));

                $data['truck_id'] = $res['truck_id'];
                $data['truck_username'] = $res['truck_username'];
                $data['truck_name'] = $res['truck_name'];
                $data['truck_emailid'] = $res['truck_emailid'];
                $data['truck_password'] = $enPassword = $dataObj->decryptIt($res['truck_password']);
                $data['truck_location'] = $res['truck_location'];
                $data['truck_latitude'] = $res['truck_latitude'];
                $data['truck_longitude'] = $res['truck_longitude'];
                $data['truck_devicetoken'] = $res['truck_devicetoken'];
                $data['truck_devicetype'] = $res['truck_devicetype'];
            }


            $user_detail['ResponseCode'] = '1';
            $user_detail['ResponseMsg'] = 'Login successfully';
            $user_detail['Result'] = 'True';
            $user_detail['Data'] = $data;

            echo json_encode($user_detail);
        } else {
            return ResponseClass::ResponseMessage("2", " Incorrect Email. Make sure you have entered registered email.", "False");
        }
    }
} else {
    return ResponseClass::ResponseMessage("2", "Invalid method", "False");
}
?>
