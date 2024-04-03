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

if (!empty($vPayload["category_name"])) {
    $vCategoryParam["name"] = $vPayload["category_name"];
    $vCategoryParam["parent_id"] = 0;
    $vCategoryParam["tree_id"] = 1;
    $vCategoryResponse = create_category($vCategoryParam);

    if (isset($vCategoryResponse["status"]) && $vCategoryResponse["status"] == 400) {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vCategoryResponse);
        else
            v::$r = vR(400, $vCategoryResponse["message"]);
        exit;
    } else {
    $vPayload["categories"] = [$vCategoryResponse["0"]->category_id];
    }
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

    $vReturnData = call_big_commerce_api($vParam);
    
    $vError = "";
    if (!isset($vReturnData->data)) {
        $vError = $vReturnData["message"];
    }

    if ($vError == "") {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
            echo json_encode($vReturnData);
        } else
            v::$r = vR(200, $vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
            echo $vError;
        } else
            v::$r = vR(400, [$vError]);
    }
}
