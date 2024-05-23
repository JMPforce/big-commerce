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
    $vQuery = urldecode($vPayload["query"]);
    // echo $vParam["api_url"] =  urldecode("https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . $vQuery . "&key=" . $vApiKey . "&language=en");
    $vParam["api_url"] =  "https://maps.googleapis.com/maps/api/place/textsearch/json?query=Powerscourt Golf Club&key=AIzaSyAbvzlTuxrYxM63Oyf-JFvXqqaZR08P5ck&region=IE&language=en-IE";
    $vParam["method"] = "GET";

    $vReturnData = call_google_place_api($vParam);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
