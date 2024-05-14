<?php
require_once "config.php";
require_once "functions.php";
$vQueryString = "";
$vQuery = "";
// print_r($_SERVER['QUERY_STRING']);
// exit;
parse_str($_SERVER['QUERY_STRING'], $vQuery);

$vParam["api_url"] =  "catalog/products";
if (isset($vQuery["limit"])) {
    $vParam["api_url"] .= "?limit=" . $vQuery["limit"];
}else{
    $vParam["api_url"] .= "?limit=10";
}
$vParam["api_url"] .= "&include=images,custom_fields";
if (isset($vQuery["sort"])) {
    $vParam["api_url"] .= "&sort=" . $vQuery["sort"];
}else{
    $vParam["api_url"] .= "&sort=name";
}
if (isset($vQuery["direction"])) {
    $vParam["api_url"] .= "&direction=" . $vQuery["direction"];
}else{
    $vParam["api_url"] .= "&direction=asc";
}
if (isset($vQuery["page"])) {
    $vParam["api_url"] .= "&page=" . $vQuery["page"];
}
if (isset($vQuery["categories:in"])) {
    $vParam["api_url"] .= "&categories:in=" . $vQuery["categories:in"];
}
if (isset($vQuery["include_fields"])) {
    $vParam["api_url"] .= "&include_fields=" . $vQuery["include_fields"];
}else{
    $vParam["api_url"] .= "&include_fields=id,name,price,categories,description,type,sku,weight,height,depth,page_title,meta_keywords,meta_description,custom_url";
}

// if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {
//     $vParam["api_url"] .= "?" . $_SERVER['QUERY_STRING'];
// }
$vParam["method"] = "GET";
// print_r($vParam);
$vReturnData = call_big_commerce_api($vParam);

if (!isset($vReturnData->data)) {
    echo json_encode($vReturnData);
} else {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
        echo json_encode($vReturnData);
    else
        v::$r = vR(200, $vReturnData);
}
