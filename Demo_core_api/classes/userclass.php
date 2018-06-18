<?php

include_once 'conn.php';
include_once 'publicClass/commanClass.php';
include_once 'publicClass/responseClass.php';

$http = 'http://';
$SERVER_NAME = $_SERVER['SERVER_NAME'];
$project_folder = '/demo';
$file_folder = '/upload/';

$user_Image_Url = $http . $SERVER_NAME . $project_folder . $file_folder . 'user/';
$items_Image_Url = $http . $SERVER_NAME . $project_folder . $file_folder . 'items/';
$truck_Image_Url = $http . $SERVER_NAME . $project_folder . $file_folder . 'truck/';


$per_page = '10';

$is_open = FALSE;
if ($is_open == TRUE) {

    $http = 'http://';
    $SERVER_NAME = $_SERVER['SERVER_NAME'];
    $length = strlen($needle = '192.168');
    $exi = substr($SERVER_NAME, 0, $length) === $needle;
    if ($SERVER_NAME == 'localhost' || $exi == TRUE) {
        $server_domain = 'local';
        $folder = '';
    } else {
        $server_domain = 'live';
        $folder = '/~demo';
    }
    $project_folder = '/demo';
    $file_folder = '/upload/';

    $user_Image_Url = $http . $SERVER_NAME . $folder . $project_folder . $file_folder . 'user/';
}

class UserClass {

    public function checkuserEmail($emailid) {
        global $con;
        $sql = "select * from tbl_user where BINARY user_emailid= '$emailid' ";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));

        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function add_item($truck_id, $item_title, $item_description, $item_disclaimer, $item_price, $item_created) {
        global $con;
        $sql = "INSERT INTO tbl_item(truck_id,item_title,item_description,item_disclaimer,item_price,item_created) VALUES ('$truck_id','$item_title','$item_description','$item_disclaimer','$item_price','$item_created')";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function add_item_category($item_id, $value) {
        global $con;
        $sql = "INSERT INTO tbl_item_category(item_id,category_id) VALUES ('$item_id','$value')";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    public function add_item_image($item_id, $imagePath, $item_created) {
        global $con;
        $sql = "INSERT INTO tbl_item_images(item_id,item_image,item_created) VALUES ('$item_id','$imagePath','$item_created')";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    public function get_item_on_id($item_id) {
        global $con;
        $sql = "select * from tbl_item where item_id = '$item_id'";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function delete_item($item_id) {
        global $con;
        $sql = "DELETE from tbl_item where item_id='$item_id'";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function edit_item($item_id, $wr) {
        global $con;
        $sql = "UPDATE tbl_item set $wr WHERE item_id='$item_id'";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_i_image($item_id) {
        global $con;
        $sql = "DELETE from tbl_item_images where item_id='$item_id'";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function get_category() {
        global $con;
        $sql = "select category_id,category_name from tbl_category";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_item_on_category($category_id) {
        global $con;
        $sql = "select GROUP_CONCAT(item_id) as item_ids from tbl_item_category where category_id='$category_id'";
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }




    public function get_promostion_truck_search_list($latitude, $longitude, $search_text) {
        global $con;
        $today_date = date('Y-m-d');

        $sql = "SELECT P.priority,T.truck_id,T.truck_name,T.truck_profile_image,T.truck_location,T.truck_latitude,T.truck_longitude,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) * cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck AS T JOIN tbl_promotion_truck as P ON P.truck_id =T.truck_id AND ('" . $today_date . "' BETWEEN P.promotion_start_date AND P.promotion_end_date) AND P.promotion_status='confirm' where P.promotion_keyword LIKE '%$search_text%' OR T.truck_name like '%$search_text%' OR T.truck_username like '%$search_text%' OR T.truck_emailid like '%$search_text%' OR T.truck_location like '%$search_text%' OR T.truck_detail like '%$search_text%' OR T.truck_disclimer like '%$search_text%' OR T.truck_speciality like '%$search_text%' OR T.truck_phoneno like '%$search_text%' HAVING distance <= '31.0686' ORDER BY P.priority ASC";

        $result = mysqli_query($con, $sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_truck_search_list($latitude, $longitude, $search_text, $start_from, $per_page) {
        global $con;

       /* $sql = "SELECT truck_id,truck_name,truck_profile_image,truck_location,truck_latitude,truck_longitude,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck where truck_name like '%$search_text%' OR truck_username like '%$search_text%' OR truck_emailid like '%$search_text%' OR truck_location like '%$search_text%' OR truck_detail like '%$search_text%' OR truck_disclimer like '%$search_text%' OR truck_speciality like '%$search_text%' OR truck_phoneno like '%$search_text%' ORDER BY distance ASC LIMIT $start_from,$per_page";*/



       $sql = "SELECT T.truck_id,T.truck_name,T.truck_profile_image,T.truck_location,T.truck_latitude,T.truck_longitude,(((acos(sin((T.truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((T.truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((T.truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck AS T LEFT JOIN tbl_item AS I ON T.truck_id=I.truck_id where T.truck_name like '%$search_text%' OR T.truck_username like '%$search_text%' OR T.truck_emailid like '%$search_text%' OR T.truck_location like '%$search_text%' OR T.truck_detail like '%$search_text%' OR T.truck_disclimer like '%$search_text%' OR T.truck_speciality like '%$search_text%' OR T.truck_phoneno like '%$search_text%' OR I.item_title like '%$search_text%' GROUP BY T.truck_id HAVING distance <= '31.0686' ORDER BY distance ASC LIMIT $start_from,$per_page";

      
//         $today_date = date('Y-m-d');
//         $sql = "SELECT  T.truck_id,T.truck_name,T.truck_profile_image,T.truck_location,T.truck_latitude,T.truck_longitude,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
//      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck AS T JOIN tbl_promotion_truck as P ON P.truck_id =T.truck_id where (truck_name like '%$search_text%' OR truck_username like '%$search_text%' OR truck_emailid like '%$search_text%' OR truck_location like '%$search_text%' OR truck_detail like '%$search_text%' OR truck_disclimer like '%$search_text%' OR truck_speciality like '%$search_text%' OR truck_phoneno like '%$search_text%'  OR P.promotion_keyword LIKE '%$search_text%') AND ('" . $today_date . "' BETWEEN P.promotion_start_date AND P.promotion_end_date) AND P.promotion_status='confirm' ORDER BY distance ASC LIMIT $start_from,$per_page";



        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_truck_list($latitude, $longitude, $wr) {
        global $con;
        $sql = "SELECT T.truck_id,T.truck_name,T.truck_profile_image,T.truck_location,T.truck_latitude,T.truck_longitude,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance,(SELECT COUNT(*) FROM tbl_review WHERE truck_id = T.truck_id) AS reviewcount,(SELECT ROUND(AVG(review_rating)) FROM tbl_review WHERE truck_id = T.truck_id) AS rating from tbl_truck AS T LEFT JOIN tbl_truck_category AS TC on T.truck_id = TC.truck_id $wr";
   
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_test_list($latitude, $longitude, $search_text, $start_from, $per_page) {
        global $con;
        $sql = "SELECT truck_id,truck_name,truck_location,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck where truck_name like '%$search_text%' OR truck_detail like '%$search_text%' OR truck_disclimer like '%$search_text%' OR truck_speciality like '$search_text' LIMIT $start_from,$per_page ";
//        echo $sql;
//        die;
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_nearby_truck_list($latitude, $longitude) {
        global $con;
        $sql = "SELECT truck_id,truck_name,truck_location,truck_latitude,truck_longitude,truck_profile_image,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck where (((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) <= '31.0686'";
//        echo $sql;
//        die;
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_nearby_truck($truck_id, $today_date, $today_time) {
        global $con;
        //$sql = "SELECT * from tbl_truck_schedule  where truck_id='$truck_id' AND schedule_date = '$today_date' AND '$today_time' BETWEEN schedule_start_time AND schedule_end_time";
        //$sql = "SELECT * from tbl_truck_schedule where truck_id ='$truck_id' AND schedule_date = '$today_date' AND ('$today_time' BETWEEN TIME(STR_TO_DATE(schedule_start_time, '%h:%i %p')) AND TIME(STR_TO_DATE(schedule_end_time, '%h:%i %p')))";
        //$sql="SELECT * from tbl_truck_schedule where truck_id ='$truck_id' AND schedule_date = '$today_date' AND (STR_TO_DATE( '$today_time', '%h:%i %p') BETWEEN STR_TO_DATE(schedule_start_time, '%h:%i %p') AND STR_TO_DATE(schedule_end_time, '%h:%i %p'))";
        //$sql="SELECT * from tbl_truck_schedule where truck_id ='$truck_id' AND schedule_date = '$today_date' AND (STR_TO_DATE( '$today_time', '%h:%i %p') BETWEEN STR_TO_DATE(schedule_start_time, '%h:%i %p') AND STR_TO_DATE(schedule_end_time, '%h:%i %p') OR STR_TO_DATE(schedule_end_time, '%h:%i %p') <= STR_TO_DATE( schedule_start_time, '%h:%i %p'))";
        
        $sql="SELECT * from tbl_truck_schedule  where truck_id ='$truck_id' AND ('$today_time' BETWEEN schedule_start_time AND schedule_end_time)";
        
        //echo $sql;
       //die;
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function get_newsfeed_truck_list($latitude, $longitude) {
        global $con;
        //$sql = "SELECT truck_id,truck_name,truck_profile_image,truck_location,truck_latitude,truck_longitude,truck_detail,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
     // cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck where (((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
     // cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) <= '31.0686'";
      
        $today_date = date('Y-m-d');
        $sql = "SELECT TP.priority,T.truck_id,T.truck_name,T.truck_profile_image,T.truck_location,T.truck_latitude,T.truck_longitude,T.truck_detail,(((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) as distance from tbl_truck AS T JOIN tbl_promotion_truck AS TP on T.truck_id = TP.truck_id where (((acos(sin((truck_latitude *pi()/180)) * sin(('" . $latitude . "' *pi()/180))+cos((truck_latitude *pi()/180)) *
      cos(('" . $latitude . "' *pi()/180)) * cos(((truck_longitude - '" . $longitude . "')*pi()/180))))*180/pi())*60*1.1515*1) <= '31.0686'  AND ('" . $today_date . "' BETWEEN TP.promotion_start_date AND TP.promotion_end_date) AND TP.promotion_status='confirm' ORDER BY TP.priority ASC";
      
      
        $result = mysqli_query($con,$sql) or die(mysqli_error($con));
        return $result;
    }

    public function encryptIt($q) {
        $cryptKey = 'password_key';
        $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
        return( $qEncoded );
    }

    public function decryptIt($q) {
        $cryptKey = 'password_key';
        $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");
        return( $qDecoded );
    }


    function iOS_send_push_notification($device, $message, $badge, $push_type) {
        $total_badge = intval(@$badge);
        $sound = 'default';
        $payload = array();
        $payload['aps'] = array('alert' => $message, 'badge' => $total_badge,
            'sound' => $sound,
            'content-available' => 1,
            'message' => array('message' => $message, 'push_type' => $push_type)
        );
        $payload = json_encode($payload);

        $apns_url = NULL;
        $apns_cert = NULL;
        $apns_port = 2195;
        $development = $this->development;
        if ($development) {
            $apns_url = 'gateway.sandbox.push.apple.com';
            $apns_cert = $this->dev_apns_cert;
        } else {
            $apns_url = 'gateway.push.apple.com';
            $apns_cert = $this->dis_apns_cert;
        }
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
        $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);

        $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;

        $res = fwrite($apns, $apns_message);

        if (!$res) {
            //return 'Message not delivered' . PHP_EOL;
            return 1;
        } else {
            return 0;
        }

        @socket_close($apns);
        @fclose($apns);
    }


    function android_send_push_notification($device, $message, $badge, $push_type) {

        //Google cloud messaging GCM-API url
        $url = 'https://fcm.googleapis.com/fcm/send';
        $total_badge = intval(@$badge);
        $id = array($device);

        $data = array(
            'message' => $message,
            'image' => '0',
            'serverside' => 'no',
            'badge' => $total_badge,
            'push_type' => $push_type
        );

        $fields = array(
            'registration_ids' => $id,
            'data' => $data
        );
        $headers = array(
            'Authorization: key=AIzaSyAIkIIwmWl_lr-QlfqRAxVH5PYq3fYri80',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

//        if ($result === FALSE) {
//            die('Curl failed: ' . curl_error($ch));
//        }
        curl_close($ch);
        return $result;
    }
