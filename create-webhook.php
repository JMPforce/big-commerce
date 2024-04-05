<?php
require_once "config.php";
require_once "functions.php";
$vResponse = [];
$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);

if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

if (empty($vPayload["scope"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "scope parameter missing.";
}

if (empty($vPayload["destination"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "destination parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vParam["api_url"] =  "hooks";
    $vParam["method"] = "POST";
    if (empty($vPayload["is_active"])) {
        $vPayload["is_active"] = true;
    }
    $vParam["body"] = $vPayload;
    // print_r($vParam);exit;
    $vReturnData = call_big_commerce_api($vParam);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
