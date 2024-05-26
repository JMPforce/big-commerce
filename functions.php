<?php
require_once "config.php";

function db_connection()
{
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        $db = "sandbox";
    } else {
        $db = "vship";
    }

    $credentials = fetchCredentials();
    try {
        $connection = pg_connect("postgresql://" . $credentials["postgres"][$db]["username"] . ":" . $credentials["postgres"][$db]["password"] . "@" . $credentials["postgres"][$db]["host"] . ":" . $credentials["postgres"][$db]["port"] . "/" . $credentials["postgres"][$db]["database"]) or die('Could not connect: ' . pg_last_error());
        pg_set_client_encoding($connection, "UNICODE");
        return $connection;
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}

function fetchCredentials($vType = "auth")
{

    if ($vType == "config") {
        $vJsonString = file_get_contents(ROOT_PATH . "/config.json");
    } else {
        $vJsonString = file_get_contents(ROOT_PATH . "/auth.json");
    }
    return  json_decode($vJsonString, true);
}

function closeConnection($conn)
{
    pg_close($conn);
}

function select($conn, $query = "", $params = [])
{
    try {
        // $sql = pg_query($this->connection, $query) or die('Query failed: ' . pg_last_error());
        $sql = pg_query($conn, $query) or die('Query failed: ' . pg_last_error());
        $result = pg_fetch_all($sql, PGSQL_ASSOC);
        pg_free_result($sql);
        // $this->close();
        return $result;
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
    return false;
}

function insert($conn, $query = "", $params = [])
{
    try {
        $sql = pg_query($conn, $query) or die('Query failed: ' . pg_last_error());
        // $this->close();
        return $sql;
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
    return false;
}

function randomString($n = 21)
{
    $vCharacters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $vRandomString = '';

    for ($i = 0; $i < $n; $i++) {
        $vIndex = rand(0, strlen($vCharacters) - 1);
        $vRandomString .= $vCharacters[$vIndex];
    }

    return $vRandomString;
}

function get_product($id)
{
    if (empty($id)) {
        return array("status" => 400, "message" => "Product ID empty.");
    }
    $vParam["api_url"] =  "catalog/products/" . $id."/?include=custom_fields&&include_fields=id,name,price,description,sku";
    $vParam["method"] = "GET";

    return call_big_commerce_api($vParam);
}

function call_aftership_tracking_api($vParam)
{
    $curl = curl_init();

    $vCurlArray[CURLOPT_URL] = $GLOBALS["vConfig"]["AS_TRACKING_API"] . $vParam["api_url"];
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
        "as-api-key: " . $GLOBALS["vConfig"]["AS_API_KEY"]
    ];

    curl_setopt_array($curl, $vCurlArray);

    $vResponse = curl_exec($curl);
    $vReturnData = json_decode($vResponse);

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

function get_country_code($name)
{
    // echo $name;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://restcountries.com/v3.1/name/' . $name,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $vResponse = curl_exec($curl);

    curl_close($curl);
    $vReturnData = json_decode($vResponse);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ["status" => 400, "message" => "cURL Error #:" . $err];
    } else {

        return $vReturnData;
    }
}
function call_google_place_api($vParam)
{
    $curl = curl_init();
    $vHeaders = [
        "Accept: application/json",
        "Content-Type: application/json"
    ];
    if (!empty($vParam['fields'])) {
        array_push($vHeaders, "X-Goog-Api-Key: " . $GLOBALS["vConfig"]["PLACE_API_KEY"], "X-Goog-FieldMask:" . $vParam['fields']);
    }
    curl_setopt_array($curl, array(
        CURLOPT_URL => $vParam["api_url"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $vParam["method"],
        CURLOPT_HTTPHEADER => $vHeaders
    ));

    $vResponse = curl_exec($curl);

    curl_close($curl);
    $vReturnData = json_decode($vResponse);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ["status" => 400, "message" => "cURL Error #:" . $err];
    } else {
        return $vReturnData;
    }
}

function call_aftership_api($vParam)
{
    $curl = curl_init();

    $vCurlArray[CURLOPT_URL] =  $vParam["api_url"];
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
        "as-api-key: " . $GLOBALS["vConfig"]["AS_API_KEY"]
    ];

    curl_setopt_array($curl, $vCurlArray);

    $vResponse = curl_exec($curl);
    $vReturnData = json_decode($vResponse);
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


function call_big_commerce_api($vParam, $api_version = "")
{
    $curl = curl_init();
    if ($api_version == "v2")
        $vCurlArray[CURLOPT_URL] = $GLOBALS["vConfig"]["API_BASE_V2"] . $vParam["api_url"];
    else
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
    $err = curl_error($curl);


    curl_close($curl);
    if ($api_version == "v2") {
        if ($vReturnData) {
            return $vReturnData;
        }
    } else {
        if ($err) {
            return ["status" => 400, "message" => "cURL Error #:" . $err];
        } else {
            if (isset($vReturnData->status) && $vReturnData->status != 200) {
                return ["status" => 400, "message" => isset($vReturnData->title) ? $vReturnData->title : "API call error, Check your payload."];
            } else {
                if (is_array($vReturnData))
                    return $vReturnData[0];
                else
                    return $vReturnData;
            }
        }
    }
    // if ($err) {
    //     return ["status" => 400, "message" => "cURL Error #:" . $err];
    // } else {
    //     if (isset($vReturnData->status) && $vReturnData->status != 200) {
    //         return ["status" => 400, "message" => isset($vReturnData->title) ? $vReturnData->title : "API call error, Check your payload."];
    //     } else {
    //         if (is_array($vReturnData))
    //             return $vReturnData[0];
    //         else
    //             return $vReturnData;
    //     }
    // }
}


function create_category($vPayload)
{
    $vPayloadBody[] = $vPayload;

    $vParam["api_url"] =  "catalog/trees/categories";
    $vParam["method"] = "POST";
    $vParam["body"] = $vPayloadBody;

    $vReturn = call_big_commerce_api($vParam);

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

    return call_big_commerce_api($vParam);
}

function create_customer($vPayload)
{
    $vPayloadBody[] = $vPayload;
    $vParam["api_url"] =  "customers";
    $vParam["method"] = "POST";
    $vParam["body"] = $vPayloadBody;

    return call_big_commerce_api($vParam);
}

function create_customer_address($vPayload)
{
    $vPayloadBody[] = $vPayload;
    $vParam["api_url"] =  "customers/addresses";
    $vParam["method"] = "POST";
    $vParam["body"] = $vPayloadBody;

    return call_big_commerce_api($vParam);
}

function update_customer_address($vPayload)
{
    $vPayloadBody[] = $vPayload;
    $vParam["api_url"] =  "customers/addresses";
    $vParam["method"] = "PUT";
    $vParam["body"] = $vPayloadBody;

    return call_big_commerce_api($vParam);
}

function findIndexByKey($array, $key, $value)
{
    foreach ($array as $index => $object) {
        if ($object->{$key} === $value) {
            return $index;
        }
    }
    return -1; // Return -1 if the name is not found
}
function findIndexByProductId($array, $id)
{
    foreach ($array as $index => $object) {
        if ($object->product_id === $id) {
            return $index;
        }
    }
    return -1; // Return -1 if the name is not found
}
function findIndexByName($array, $name)
{
    foreach ($array as $index => $object) {
        if ($object->name === $name) {
            return $index;
        }
    }
    return -1; // Return -1 if the name is not found
}

function getCountryCode($name)
{
    switch (strtolower($name)) {
        case 'united kingdom':
        case 'great britain':
        case 'uk':
        case 'gb':
            $code = "GBR";
            break;
        case 'ireland':
        case 'ie':
            $code = "IRL";
            break;

        default:
            $code = "USA";
            break;
    }
    return $code;
}
function getUnitsByCountry($name)
{
    // print_r($GLOBALS["vConfig"]);
    switch (strtolower($name)) {
        case 'united kingdom':
        case 'great britain':
        case 'uk':
        case 'gb':
        case 'gbr':
        case 'ireland':
        case 'ie':
        case 'irl':
            $units["weight"] = $GLOBALS["vConfig"]["EU"]["WEIGHT_UNIT"];
            $units["dimension"] = $GLOBALS["vConfig"]["EU"]["DIMENSION_UNIT"];
            $units["currency"] = $GLOBALS["vConfig"]["EU"]["CURRENCY"];
            break;

        default:
            $units["weight"] = $GLOBALS["vConfig"]["US"]["WEIGHT_UNIT"];
            $units["dimension"] = $GLOBALS["vConfig"]["US"]["DIMENSION_UNIT"];
            $units["currency"] = $GLOBALS["vConfig"]["US"]["CURRENCY"];
            break;
    }
    return $units;
}
