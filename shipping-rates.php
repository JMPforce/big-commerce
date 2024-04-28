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


$vParam["api_url"] =  "rates";
$vParam["method"] = "GET";
$limit = 2;
if (empty($vPayload["created_at_max"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "created_at_max parameter missing.";
}
if (empty($vPayload["created_at_min"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "created_at_min parameter missing.";
}
if (!empty($vPayload["limit"])) {
    $limit = $vPayload["limit"];
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vParam["api_url"] .= "?limit=" . $limit . "&created_at_max=" . $vPayload["created_at_max"] . "&created_at_min=" . $vPayload["created_at_min"];
    $vReturnData = call_aftership_api($vParam);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData->data);
        else
            v::$r = vR(200, $vReturnData->data);
    }
}
