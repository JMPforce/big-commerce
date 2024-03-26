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

if (empty($vPayload["name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "name parameter missing.";
}

if (empty($vPayload["type"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "type(physical | digital) parameter missing.";
}

if (empty($vPayload["weight"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "weight parameter missing.";
}

if (empty($vPayload["price"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "price parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vParam["api_url"] =  "catalog/products";
    $vParam["method"] = "POST";
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
