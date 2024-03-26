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

$vReturnData = call_big_commerce($vParam);
// print_r($vResponse);
if (!isset($vReturnData->data)) {
    echo json_encode($vReturnData);
} else {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
        echo json_encode($vReturnData);
    else
        v::$r = vR(200, $vReturnData);
}
