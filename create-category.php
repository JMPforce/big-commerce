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

if (empty($vPayload["name"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "name parameter missing.";
}
if (empty($vPayload["parent_id"])) {
    $vPayload["parent_id"] = 0;
}

if (empty($vPayload["tree_id"])) {
    $vPayload["tree_id"] = 1;
}


if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {

    // $vPayloadBody[] = $vPayload;

    // $vParam["api_url"] =  "catalog/trees/categories";
    // $vParam["method"] = "POST";
    // $vParam["body"] = $vPayloadBody;

    // $vResponse = call_big_commerce($vParam);
     $vResponse = create_category($vPayload);

    if ($vResponse["status"]==400) {
        echo $vResponse["message"];
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vResponse["data"]);
        else
            v::$r = vR(200, $vResponse["data"]);
    }
}
