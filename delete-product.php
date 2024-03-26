<?php
require_once "config.php";
$vResponse = [];
if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

$curl = curl_init();

$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);
if (empty($vQuery["product_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "product_id parameter missing.";
}


if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {

    $vParam["api_url"] =  "catalog/products/" . $vQuery["product_id"];
    $vParam["method"] = "DELETE";
    $vParam["body"] = $vPayload;

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
