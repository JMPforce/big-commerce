<?php
require_once "config.php";
require_once "functions.php";
$vResponse = [];
if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);
// print_r($vPayload);exit;
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
     $vResponse = create_brand($vPayload);

    if ($vResponse["status"]==400) {
        echo $vResponse["message"];
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vResponse["data"]);
        else
            v::$r = vR(200, $vResponse["data"]);
    }
}
