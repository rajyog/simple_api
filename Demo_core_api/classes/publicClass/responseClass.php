<?php

error_reporting(E_ALL ^ E_DEPRECATED);

//Create Connection Class
class ResponseClass {

    public static function ResponseMessage($res, $responseMessage, $result) {
        echo json_encode(array("ResponseCode" => "$res", "ResponseMsg" => "$responseMessage", "Result" => "$result"));
    }

    public static function successResponseInArray($keyName, $array, $res, $responseMessage, $result) {
        echo json_encode(array("$keyName" => $array, "ResponseCode" => "$res", "ResponseMsg" => "$responseMessage", "Result" => "$result","ServerTimeZone" => date('T')));
    }

}

?>