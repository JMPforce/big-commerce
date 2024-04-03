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
if (empty($vPayload["product_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "product_id parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vParam["api_url"] =  "catalog/products/" . $vPayload["product_id"];
    $vParam["method"] = "DELETE";
    // $vParam["body"] = $vPayload;

    $vReturnData = call_big_commerce_api($vParam);
    // print_r($vReturnData);
    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
