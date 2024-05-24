<?php
require_once "config.php";
require_once "functions.php";
$vQueryString = "";
$vQuery = "";
$vResponse = [];
parse_str($_SERVER['QUERY_STRING'], $vQuery);


if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;


if (empty($vPayload["tracking_code"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "tracking_code parameter missing.";
} else {
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    if (is_numeric($vPayload["tracking_code"])) {
        $vParam["api_url"] =  "orders/" . $vPayload["tracking_code"] . "/shipments";
        $vParam["method"] = "GET";
        unset($vPayload["order_id"]);
        $vParam["body"] = $vPayload;

        $vReturnData = call_big_commerce_api($vParam, "v2");
        // print_r($vReturnData);
        if (isset($vReturnData->tracking_number))
            $tracking_id = $vReturnData->tracking_number;
        else
            $tracking_id = $vPayload["tracking_code"];
    } else {
        $tracking_id = $vPayload["tracking_code"];
    }

    $vParam2["api_url"] =  "trackings/".$tracking_id;
    $vParam2["method"] = "GET";
    
    $vReturnData = call_aftership_tracking_api($vParam2);
    // print_r($vReturnData->data);
    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData->data);
        else
            v::$r = vR(200, $vReturnData->data);
    }
}
