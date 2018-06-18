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

    $user_firstname = mysqli_real_escape_string($con,empty($_REQUEST["user_firstname"]) ? "" : $_REQUEST["user_firstname"]);
    $user_lastname = mysqli_real_escape_string($con,empty($_REQUEST["user_lastname"]) ? "" : $_REQUEST["user_lastname"]);
    $user_username = mysqli_real_escape_string($con,empty($_REQUEST["user_username"]) ? "" : $_REQUEST["user_username"]);
    $user_dateofbirth = empty($_REQUEST["user_dateofbirth"]) ? "" : $_REQUEST["user_dateofbirth"];
    $emailid = mysqli_real_escape_string($con,empty($_REQUEST["user_emailid"]) ? "" : $_REQUEST["user_emailid"]);
    $user_password = mysqli_real_escape_string($con,empty($_REQUEST["user_password"]) ? "" : $_REQUEST["user_password"]);
    $user_social_id = empty($_REQUEST["user_social_id"]) ? "" : $_REQUEST["user_social_id"];
    $entity_id = empty($_REQUEST["entity_id"]) ? "" : $_REQUEST["entity_id"];
    $user_devicetoken = empty($_REQUEST["user_devicetoken"]) ? "" : $_REQUEST["user_devicetoken"];
    $devicetype = empty($_REQUEST["devicetype"]) ? "" : $_REQUEST["devicetype"];

    $dataObj = new UserClass();

    if (empty($user_social_id)) {
        if (empty($user_firstname)) {
            return ResponseClass::ResponseMessage("2", "Please enter first name", "False");
        } elseif (empty($user_lastname)) {
            return ResponseClass::ResponseMessage("2", "Please enter last name", "False");
        } elseif (empty($user_dateofbirth)) {
            return ResponseClass::ResponseMessage("2", "Please enter birth date", "False");
        } elseif (empty($user_username)) {
            return ResponseClass::ResponseMessage("2", "Please enter username", "False");
        } elseif (empty($emailid)) {
            return ResponseClass::ResponseMessage("2", "Please enter email", "False");
        } elseif (empty($user_password)) {
            return ResponseClass::ResponseMessage("2", "Please enter password", "False");
        } elseif (empty($user_devicetoken)) {
            return ResponseClass::ResponseMessage("2", "Please enter device token", "False");
        } elseif (empty($devicetype)) {
            return ResponseClass::ResponseMessage("2", "Please enter device type", "False");
        } else {
            $isUserMail = $dataObj->checkuserEmail($emailid);
            $isTruckMail = $dataObj->checktruckEmail($emailid);
            if ($isUserMail == true || $isTruckMail == true) {
                return ResponseClass::ResponseMessage("2", "Email already exist", "False");
            }

            $enPassword = $dataObj->encryptIt($user_password);
            $login_type = '1';

            $result = $dataObj->register_user($user_firstname, $user_lastname, $user_username, $user_dateofbirth, $emailid, $enPassword,$entity_id, $user_devicetoken, $devicetype, $login_type, $user_created);
            $user_id = mysqli_insert_id($con);
            if ($result == true) {
                        
                if (!empty($user_id) && !empty($emailid)) {
                    require_once('stripe/init.php');
                    \Stripe\Stripe::setApiKey($Secret);
//                    \Stripe\Stripe::setApiKey("sk_test_1NMeHQN0snKsW2On71GR2k32");
                    $customer = \Stripe\Customer::create(array(
                                "email" => $emailid,
                                "metadata" => array("user_id" => $user_id)
                    ));
                    $customer_json = $customer->__toJSON();
                    $customer_json = json_decode($customer_json, TRUE);
                    $customer_id = $customer_json['id'];
                    $dataObj->edit_user($user_id, $customer_id);
                }

            
                $result = mysqli_fetch_array($dataObj->get_user_profile($user_id));
                $data['user_id'] = $result['user_id'];
                $data['user_firstname'] = $result['user_firstname'];
                $data['user_lastname'] = $result['user_lastname'];
                $data['user_username'] = $result['user_username'];
                $data['user_dateofbirth'] = $result['user_dateofbirth'];
                $data['user_emailid'] = $result['user_emailid'];
                $enPassword = $dataObj->decryptIt($result['user_password']);
                $data['user_password'] = $enPassword;
                $get_image = $result['user_image'];
                $data['login_type'] = $result['login_type'];
                $data['user_devicetype'] = $result['user_devicetype'];
                $data['customer_id'] = $result['customer_id'];
                $data['card_available'] = $result['is_card_available'];
                $data['user_vault_amount'] = $result['user_vault_amount'];


                if (empty($get_image)) {
                    $data['user_image'] = "";
                } else {
                    $image_path = $dataObj->getUserImageFullPath($result['user_image']);
                    $data['user_image'] = $image_path;
                }

                $user_detail['ResponseCode'] = '1';
                $user_detail['ResponseMsg'] = 'Registration is successfully';
                $user_detail['Result'] = 'True';
                $user_detail['Data'] = $data;

                echo json_encode($user_detail);
            } else {
                return ResponseClass::ResponseMessage("2", "Registration has been completed successfully", "False");
            }
        }
    } else {
        if (empty($user_social_id)) {
            return ResponseClass::ResponseMessage("2", "Please enter facebook id", "False");
        } else {
            $isSocialIdFound = $dataObj->check_socialid($user_social_id);
            if ($isSocialIdFound == true) {
                $result = mysqli_fetch_array($dataObj->get_user_detail_on_social_id($user_social_id));

                $data['user_id'] = $result['user_id'];
                $data['user_firstname'] = $result['user_firstname'];
                $data['user_lastname'] = $result['user_lastname'];
                $data['user_password'] = $result['user_password'];
                $data['user_username'] = $result['user_username'];
                $data['user_emailid'] = $result['user_emailid'];
                $data['user_devicetoken'] = $result['user_devicetoken'];
                $data['user_devicetype'] = $result['user_devicetype'];
                $data['customer_id'] = $result['customer_id'];
                $data['card_available'] = $result['is_card_available'];
                $data['user_vault_amount'] = $result['user_vault_amount'];

                $get_image = $result['user_image'];

                if (empty($get_image)) {
                    $data['user_image'] = "";
                } else {
                    $image_path = $dataObj->getUserImageFullPath($result['user_image']);
                    $data['user_image'] = $image_path;
                }

                $data['login_type'] = $result['login_type'];
                $data['is_new_user'] = 'no';

                $user_detail['ResponseCode'] = '1';
                $user_detail['ResponseMsg'] = 'Login successfully';
                $user_detail['Result'] = 'True';
                $user_detail['Data'] = $data;
                echo json_encode($user_detail);
            } else {
                $login_type = '2';
                if(empty($emailid))
                {
                	$result = $dataObj->register_fb_user($user_firstname, $user_lastname, $user_username, $user_social_id,$entity_id, $user_devicetoken, $devicetype, $login_type, $user_created);
                	$user_id = mysqli_insert_id($con);
                }else{
                        $result = $dataObj->register_fb_user_with_email($user_firstname, $user_lastname, $user_username,$emailid, $user_social_id,$entity_id, $user_devicetoken, $devicetype, $login_type, $user_created);
                        $user_id = mysqli_insert_id($con);
                }
                
                

                if ($result == true) {
                
                    if (!empty($user_id) && !empty($emailid)) {
                         require_once('stripe/init.php');
                         \Stripe\Stripe::setApiKey($Secret);
//                        \Stripe\Stripe::setApiKey("sk_test_1NMeHQN0snKsW2On71GR2k32");
                        $customer = \Stripe\Customer::create(array(
                                    "email" => $emailid,
                                    "metadata" => array("user_id" => $user_id)
                        ));
                        $customer_json = $customer->__toJSON();
                        $customer_json = json_decode($customer_json, TRUE);
                        $customer_id = $customer_json['id'];
                        $card_available = '1';
                        $dataObj->edit_user($user_id, $customer_id);
                    }


                    if (!empty($_FILES["user_image"]["name"])) {
                        $imagePath = CommanClass::user_profile_image_upload();
                        $result = $dataObj->updateProfileImage($user_id, $imagePath);
                    }

                    $result = mysqli_fetch_array($dataObj->get_user_profile($user_id));

                    $data['user_id'] = $result['user_id'];
                    $data['user_firstname'] = $result['user_firstname'];
                    $data['user_lastname'] = $result['user_lastname'];
                    $data['user_username'] = $result['user_username'];
                    $data['user_password'] = $result['user_password'];
                    $data['user_emailid'] = $result['user_emailid'];
                    $data['user_devicetoken'] = $result['user_devicetoken'];
                    $data['user_devicetype'] = $result['user_devicetype'];
                    $data['customer_id'] = $result['customer_id'];
                    $data['card_available'] = $result['is_card_available'];
                    $data['user_vault_amount'] = $result['user_vault_amount'];


                    $get_image = $result['user_image'];

                    if (empty($get_image)) {
                        $data['user_image'] = "";
                    } else {
                        $image_path = $dataObj->getUserImageFullPath($result['user_image']);
                        $data['user_image'] = $image_path;
                    }

                    $data['login_type'] = $result['login_type'];
                    $data['is_new_user'] = 'yes';

                    $user_detail['ResponseCode'] = $user_detail['ResponseCode'] = '1';
                    $user_detail['ResponseMsg'] = 'Registration is successfully';
                    $user_detail['Result'] = 'True';
                    $user_detail['Data'] = $data;

                    echo json_encode($user_detail);
                } else {
                    return ResponseClass::ResponseMessage("2", "Registration has been completed successfully", "False");
                }
            }
        }
    }
} else {
    return ResponseClass::ResponseMessage("2", "Invalid method", "False");
}
?>
