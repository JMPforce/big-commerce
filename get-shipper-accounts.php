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

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
}else{

    $vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API"] . "shipper-accounts";
    if (isset($vPayload["api_mode"]) && strtolower($vPayload["api_mode"]) == "prod") {
        $vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API_PROD"] . "shipper-accounts";
        // $shipperAccountId = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID_PROD"];
    }
    $vParam["method"] = "GET";

    $vReturnData = call_aftership_api($vParam);

    if (!isset($vReturnData->meta)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData->data->shipper_accounts);
        else
            v::$r = vR(200, $vReturnData->data->shipper_accounts);
    }
}