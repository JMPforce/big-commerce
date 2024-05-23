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
if (empty($vPayload["query"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "query parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {


    $vApiKey = $GLOBALS["vConfig"]["PLACE_API_KEY"];
    $vQuery = urlencode($vPayload["query"]);

    $vQueryString = "key=" .  $vApiKey . "&query=" . $vQuery . "&language=en";
    // if (isset($vPayload["country"])) {
    //     $vQueryString .= "&region=" . strtoupper($vPayload["country"]);
    // }
    $vParam["api_url"] =  "https://maps.googleapis.com/maps/api/place/textsearch/json?" . $vQueryString;
    $vParam["method"] = "GET";
    $vReturnData = call_google_place_api($vParam);

    if (strtolower($vReturnData->status) != "ok" && count($vReturnData->results) <= 0) {
        v::$r = vR(200, $vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData->results);
        else
            v::$r = vR(200, $vReturnData->results);
    }
}
