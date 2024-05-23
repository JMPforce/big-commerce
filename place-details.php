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
if (empty($vPayload["place_id"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "place_id parameter missing.";
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {

    $vParam["api_url"] =  "https://places.googleapis.com/v1/places/" . $vPayload["place_id"];
    $vParam["method"] = "GET";
    $vParam["fields"] = "id,name,formattedAddress,addressComponents,displayName";
    $vReturnData = call_google_place_api($vParam);

    if (strtolower($vReturnData->name) != "" && $vReturnData->id != "") {
        v::$r = vR(200, $vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData);
    }
}
