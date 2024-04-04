<?php
require_once "config.php";
require_once "functions.php";
$vTable = "webhooks";
$vResponse = [];
$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);

if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else {
    $vPayload = v::$a;
}

$connection =db_connection();


// print_r($vPayload);
// exit;

$data["id"] = randomString();
$data["scope"] = $vPayload["scope"];
$data["data"] = json_encode($vPayload["data"]);
$data["payload"] = json_encode($vPayload);

$sql = "INSERT INTO {$vTable} (id,scope,data,payload,created_at) values ('" . $data["id"] . "','" . $data["scope"] . "','" . $data["data"] . "','" . $data["payload"] . "',now()) RETURNING id";
$result = insert($connection, $sql);
closeConnection($connection);
if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vReturnData = ["status" => 200, "message" => "OK"];
    echo json_encode($vReturnData);
} else {
    v::$r = vR(200, $vReturnData);
}
