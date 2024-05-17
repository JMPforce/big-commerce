<?php
require_once "config.php";
require_once "functions.php";

$vResponse = [];
$vQuery = "";

if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

parse_str($_SERVER['QUERY_STRING'], $vQuery);
if (empty($vPayload["cart_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "cart_id parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {

    $vParam["api_url"] =  "carts/" . $vPayload["cart_id"];
    $vParam["method"] = "GET";

    $vResponseData = call_big_commerce_api($vParam);

    if (!isset($vResponseData->data)) {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vResponseData);
        else
            v::$r = vR(200, $vResponseData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vResponseData->data);
        else
            v::$r = vR(200, $vResponseData->data);
    }
}
