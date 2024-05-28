<?php
require_once "config.php";
require_once "functions.php";
$vTable = "carts";
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
        $vParam["api_url"] =  "orders/" . $vPayload["tracking_code"] . "/?include=consignments.line_items";
        $vParam["method"] = "GET";
        //order details
        $vOrderResponseData = call_big_commerce_api($vParam, "v2");
        if ($vOrderResponseData->id && $vOrderResponseData->status_id == 10 && strtolower($vOrderResponseData->status) == "completed") {
            $vConnection = db_connection();
            //fetch cart meta from DB
            $vSql = "SELECT * FROM {$vTable} WHERE cart_id='" . $vOrderResponseData->cart_id . "' AND label_created=false AND labels is NULL";
            $vResult = select($vConnection, $vSql);

            if (isset($vResult) && count($vResult) > 0) {

                $vCartMeta = json_decode($vResult[0]["meta"]);
                $vShippingData = json_decode($vResult[0]["shipper_info"]);
                $vLabelData = json_decode($vResult[0]["labels"]);
                $vShipperInfo = $vShippingData->shipper;
                $vApiMode = ($vShippingData->api_mode) ? $vShippingData->api_mode : "sandbox";

                closeConnection($vConnection);
                print_r($vLabelData);
            }
        }
    } else {
        $tracking_id = $vPayload["tracking_code"];
    }

    // $vParam2["api_url"] =  "trackings/" . $tracking_id;
    // $vParam2["method"] = "GET";

    // $vReturnData = call_aftership_tracking_api($vParam2);
    // print_r($vReturnData->data);
    // if (!isset($vReturnData->data)) {
    //     echo json_encode($vReturnData);
    // } else {
    //     if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
    //         echo json_encode($vReturnData->data);
    //     else
    //         v::$r = vR(200, $vReturnData->data);
    // }
}
