<?php
require_once "config.php";
require_once "functions.php";

$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);

$vParam["api_url"] =  "catalog/brands";
if (isset($vQuery["limit"])) {
    $vParam["api_url"] .= "?limit=" . $vQuery["limit"];
}
if (isset($vQuery["page"])) {
    $vParam["api_url"] .= "?limit=" . $vQuery["limit"] . "&page=" . $vQuery["page"];
}
$vParam["method"] = "GET";

$vResponse = call_big_commerce($vParam);

if ($vResponse["status"] == 400) {
    echo $vResponse["message"];
} else {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
        echo json_encode($vResponse["data"]);
    else
        v::$r = vR(200, $vResponse["data"]);
}
