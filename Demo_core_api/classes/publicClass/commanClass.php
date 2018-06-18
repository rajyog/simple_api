<?php

error_reporting(E_ALL ^ E_DEPRECATED);

//Create Connection Class
class CommanClass {

    public static function RemoveSpecialChar($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function user_profile_image_upload() {
        $image_name = $_FILES["user_image"]["name"];
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/user/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($_FILES["user_image"]["tmp_name"], "./upload/user/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }

    public static function truck_profile_image_upload() {
        $image_name = $_FILES["truck_profile_image"]["name"];
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/truck/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($_FILES["truck_profile_image"]["tmp_name"], "./upload/truck/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }

    public static function image_upload() {
        $image_name = $_FILES["user_image_name"]["name"];
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($_FILES["user_image_name"]["tmp_name"], "./upload/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }

    public static function mix_truck_image_upload($name, $tmp_name) {
        $image_name = $name;
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/truck/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($tmp_name, "./upload/truck/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }

    public static function single_truck_image_upload() {
        $image_name = $_FILES["truck_image"]["name"];
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/truck/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($_FILES["truck_image"]["tmp_name"], "./upload/truck/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }

    public static function mix_item_image_upload($name, $tmp_name) {
        $image_name = $name;
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/items/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($tmp_name, "./upload/items/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }
    public static function single_item_image_upload() {
        $image_name = $_FILES["item_image"]["name"];
        $tmp_arr = explode(".", $image_name);
        $img_extn = end($tmp_arr);
        $new_image_name = 'image_' . uniqid() . '_' . date('YmdHis') . '.' . $img_extn;
        $flag = 0;
        if (file_exists("./upload/items/" . $new_image_name)) {
            return false;
        } else {
            move_uploaded_file($_FILES["item_image"]["tmp_name"], "./upload/items/" . $new_image_name);
            $flag = 1;
            $static_url = $new_image_name;
            return $static_url;
        }
    }
}

?>