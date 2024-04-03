<?php
require_once "config.php";
require_once "functions.php";
$vResponse = [];
if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);

if (empty($vPayload["id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "id parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vReturnData = update_customer_address($vPayload);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
