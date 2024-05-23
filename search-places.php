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

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=Powerscourt%20Golf%20Club&key=AIzaSyAbvzlTuxrYxM63Oyf-JFvXqqaZR08P5ck&region=IE&language=en-IE',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    // $response = curl_exec($curl);

    // curl_close($curl);
    // echo $response;



    $vApiKey = $GLOBALS["vConfig"]["PLACE_API_KEY"];
    $vQuery = urlencode($vPayload["query"]);
    // http_build_query(["query"=>])
    $vParam["api_url"] =  "https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . $vQuery . "&key=" . $vApiKey . "&language=en";
    // $vParam["api_url"] =  "https://maps.googleapis.com/maps/api/place/textsearch/json?query=Powerscourt Golf Club&key=AIzaSyAbvzlTuxrYxM63Oyf-JFvXqqaZR08P5ck&region=IE&language=en-IE";
    $vParam["method"] = "GET";
    // print_r($vParam);
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
