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

if (empty($vPayload["brand_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "brand_id parameter missing.";
}

if (empty($vPayload["name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "name parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vParam["api_url"] =  "catalog/brands/".$vPayload["brand_id"];
    $vParam["method"] = "PUT";
    unset($vPayload["brand_id"]);
    $vParam["body"] = $vPayload;
    // print_r($vParam);exit;
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
