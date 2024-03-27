<?php
require_once "config.php";

function find_brand($name)
{
    $vResponse = [];
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        $vPayload = json_decode(file_get_contents('php://input'), true);
    } else
        $vPayload = v::$a;

    $curl = curl_init();

    $vQuery = "";
    parse_str($_SERVER['QUERY_STRING'], $vQuery);
    if (empty($vPayload["brand_id"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "brand_id parameter missing.";
    }

    if (count($vResponse) > 0) {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
            echo json_encode($vResponse);
        } else {
            v::$r = vR(400, $vResponse);
        }
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $GLOBALS["vConfig"]["API_BASE"] . "catalog/brands/" . $vQuery["brand_id"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/json",
            "X-Auth-Token: " . $GLOBALS["vConfig"]["AUTH_TOKEN"]
        ],
    ]);

    $vResponse = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            return $vResponse;
        else
            return json_decode($vResponse);
    }
}


function call_big_commerce($vParam)
{
    $curl = curl_init();

    $vCurlArray[CURLOPT_URL] = $GLOBALS["vConfig"]["API_BASE"] . $vParam["api_url"];
    $vCurlArray[CURLOPT_RETURNTRANSFER] = true;
    $vCurlArray[CURLOPT_ENCODING] = "";
    $vCurlArray[CURLOPT_MAXREDIRS] = 10;
    $vCurlArray[CURLOPT_TIMEOUT] = 30;
    $vCurlArray[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
    $vCurlArray[CURLOPT_CUSTOMREQUEST] = $vParam["method"];
    if (!empty($vParam["body"]))
        $vCurlArray[CURLOPT_POSTFIELDS] = json_encode($vParam["body"]);
    $vCurlArray[CURLOPT_HTTPHEADER] = [
        "Accept: application/json",
        "Content-Type: application/json",
        "X-Auth-Token: " . $GLOBALS["vConfig"]["AUTH_TOKEN"]
    ];

    curl_setopt_array($curl, $vCurlArray);

    $vResponse = curl_exec($curl);
    $vReturnData = json_decode($vResponse);
    // print_r($vReturnData);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ["status" => 400, "message" => "cURL Error #:" . $err];
    } else {
        if (isset($vReturnData->status) && $vReturnData->status != 200) {
            return ["status" => 400, "message" => ($vReturnData->title) ? $vReturnData->title : "API call error, Check your payload."];
        } else
            return $vReturnData;
    }
}


function create_category($vPayload)
{
    $vPayloadBody[] = $vPayload;

    $vParam["api_url"] =  "catalog/trees/categories";
    $vParam["method"] = "POST";
    $vParam["body"] = $vPayloadBody;

    $vReturn = call_big_commerce($vParam);
    print_r($vReturn);
    if (isset($vReturn->errors)) {
        return ["status" => 400, "message" => $vReturn->errors->title];
    } elseif (isset($vReturn->data)) {
        return $vReturn->data;
    } else {
        return $vReturn;
    }
}

function create_brand($vPayload)
{
    $vParam["api_url"] =  "catalog/brands";
    $vParam["method"] = "POST";
    $vParam["body"] = $vPayload;

    return call_big_commerce($vParam);
}

function create_customer($vPayload)
{
    $vPayloadBody[] = $vPayload;
    $vParam["api_url"] =  "customers";
    $vParam["method"] = "POST";
    $vParam["body"] = $vPayloadBody;

    return call_big_commerce($vParam);
}