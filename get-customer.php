<?php
require_once "config.php";
require_once "functions.php";
$vQueryString = "";
$vQuery = "";

parse_str($_SERVER['QUERY_STRING'], $vQuery);
if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

$vParam["api_url"] =  "customers";
$vParam["method"] = "GET";

// if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {
//     $vParam["api_url"] .= "?" . $_SERVER['QUERY_STRING'];
// }

$vParam["api_url"] .= "?include=addresses";
if ($vPayload["email_address"]) {
    $vParam["api_url"] .= "&email:in=" . $vPayload["email_address"];
}
// print_r($vParam);
$vReturnData = call_big_commerce_api($vParam);

if (!isset($vReturnData->data)) {
    echo json_encode($vReturnData);
} else {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
        echo json_encode($vReturnData);
    else
        v::$r = vR(200, $vReturnData);
}
