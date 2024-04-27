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

if (empty($vPayload["email"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "email parameter missing.";
} else {
    if (!filter_var($vPayload["email"], FILTER_VALIDATE_EMAIL)) {
        $vResponse["status"] = 400;
        $vResponse["error"] = $vPayload["email"] . " is not a vaild email address.";
    }
}

if (empty($vPayload["first_name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "first_name parameter missing.";
}

if (empty($vPayload["last_name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "last_name parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    
    $vReturnData = create_customer($vPayload);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
