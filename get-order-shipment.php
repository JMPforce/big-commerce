<?php
require_once "config.php";
require_once "functions.php";
$vTable = "webhooks";
$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);

if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else {
    $vPayload = v::$a;
}

$connection = db_connection();

if ((!empty($vPayload) && !empty($vPayload["order_id"]))) {
    $vParam["api_url"] =  "orders/" . $vPayload["order_id"] . "/shipments";
    $vParam["method"] = "GET";
    unset($vPayload["order_id"]);
    $vParam["body"] = $vPayload;

    $vReturnData = call_big_commerce_api($vParam, "v2");

    if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
        echo json_encode($vReturnData);
    else
        v::$r = vR(200, $vReturnData);
} else {

    $sql = "SELECT * FROM  {$vTable} WHERE scope='store/shipment/created' OR scope='store/shipment/updated' OR scope='store/shipment/deleted' ORDER BY created_at DESC";
    $result = select($connection, $sql);
    closeConnection($connection);
    $vReturnData = [];
    foreach ($result as $key => $row) {
        $vReturnData[$key]["scope"] = $row["scope"];
        $vData = json_decode($row["meta"], true);
        $vReturnData[$key]["data"]["type"] = $vData["type"];
        $vReturnData[$key]["data"]["shipment_id"] = $vData["id"];
        $vReturnData[$key]["data"]["order_id"] = $vData["orderId"];
    }
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        // $vReturnData = ["status" => 200, "scope" => $result["0"]];
        echo json_encode($vReturnData);
    } else {
        v::$r = vR(200, $vReturnData);
    }
}
