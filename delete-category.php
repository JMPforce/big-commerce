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

if (empty($vPayload["category_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "category_id parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {

    $vPayloadBody[] = $vPayload;

    $vParam["api_url"] =  "catalog/trees/categories/".$vPayload["category_id"];
    $vParam["method"] = "DELETE";
    // $vParam["body"] = $vPayloadBody;

    $vReturnData = call_big_commerce($vParam);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
