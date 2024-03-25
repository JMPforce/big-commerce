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
    foreach ($vPayload as $key => $val) {
        $vPayloadBody[$key] = $val;
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $GLOBALS["vConfig"]["API_BASE"] . "catalog/products",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($vPayloadBody),
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
            echo $vResponse;
        else
            v::$r = vR(200, json_decode($vResponse));
    }
}
