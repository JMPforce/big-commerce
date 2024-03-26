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

    $vParam["api_url"] =  "catalog/trees/categories";
    $vParam["method"] = "PUT";
    $vParam["body"] = $vPayloadBody;

    $vResponse = call_big_commerce($vParam);

    if ($vResponse["status"] == 400) {
        echo $vResponse["message"];
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vResponse["data"]);
        else
            v::$r = vR(200, $vResponse["data"]);
    }
}
