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

if (empty($vPayload["customer_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "customer_id parameter missing.";
}

if (empty($vPayload["first_name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "first_name parameter missing.";
}

if (empty($vPayload["last_name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "last_name parameter missing.";
}

if (empty($vPayload["city"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "city parameter missing.";
}

if (empty($vPayload["country_code"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "country_code parameter missing.";
}

if (empty($vPayload["address1"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "address1 parameter missing.";
}

if (empty($vPayload["state_or_province"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "state_or_province parameter missing.";
}

if (empty($vPayload["postal_code"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "postal_code parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vReturnData = create_customer_address($vPayload);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
