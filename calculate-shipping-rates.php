<?php
require_once "config.php";
require_once "functions.php";
$vQueryString = "";

$vResponse = [];


if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else {
    $vPayload = v::$a;
}

// if (!empty($vPayload["shipper_account_id"])) {
//     $shipperAccountId = $vPayload["shipper_account_id"];
// } else {
//     $shipperAccountId = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID"];
// }
//get shipper accounts 
$vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API"] . "shipper-accounts";
if (isset($vPayload["api_mode"]) && strtolower($vPayload["api_mode"]) == "prod") {
    $vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API_PROD"] . "shipper-accounts";
    // $shipperAccountId = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID_PROD"];
}
$vParam["method"] = "GET";
$shipperAccountId = [];
$vShipperReturnData = call_aftership_api($vParam);
// print_r($vShipperReturnData->data->shipper_accounts);
if (isset($vShipperReturnData->meta) && $vShipperReturnData->meta->code==200) {
    foreach ($vShipperReturnData->data->shipper_accounts as $key => $shipper) {
        $shipperAccountId[]["id"]  = $shipper->id;
    }
} else {
    $vResponse["status"] = 400;
    $vResponse["error"] = "No shipper account found.";
}
$vParam["method"] = "POST";
$vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API"] . "rates";
if (isset($vPayload["api_mode"]) && strtolower($vPayload["api_mode"]) == "prod") {
    $vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API_PROD"] . "rates";
    // $shipperAccountId = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID_PROD"];
}
// $vParam["api_url"] =  $GLOBALS["vConfig"]["AS_SHIPPING_API_PROD"] . "rates";

$vParam["body"]["shipper_accounts"] = $shipperAccountId;



if (empty($vPayload["ship_from"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "ship_from parameter missing.";
} else {
    // if (empty($vPayload["ship_from"]["state"])) {
    //     $vResponse["status"] = 400;
    //     $vResponse["error"] = "ship_from state parameter missing.";
    // }
    if (empty($vPayload["ship_from"]["street1"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_from street1 parameter missing.";
    }
    if (empty($vPayload["ship_from"]["postal_code"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_from postal_code parameter missing.";
    }
    if (empty($vPayload["ship_from"]["country"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_from country parameter missing.";
    } else {
        // $units = getUnitsByCountry($vPayload["ship_from"]["country"]);
        // $units = [];
    }
    if (count($vResponse) <= 0) {
        $vParam["body"]["shipment"]["ship_from"] = $vPayload["ship_from"];
    }
}
if (empty($vPayload["ship_to"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "ship_to parameter missing.";
} else {
    // if (empty($vPayload["ship_to"]["state"])) {
    //     $vResponse["status"] = 400;
    //     $vResponse["error"] = "ship_to state parameter missing.";
    // }
    if (empty($vPayload["ship_to"]["postal_code"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_to postal_code parameter missing.";
    }
    if (empty($vPayload["ship_to"]["street1"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_to street1 parameter missing.";
    }
    if (empty($vPayload["ship_to"]["country"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_to country parameter missing.";
    }
    if (count($vResponse) <= 0) {
        $vParam["body"]["shipment"]["ship_to"] = $vPayload["ship_to"];
    }
}

if (empty($vPayload["parcels"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "parcels parameter missing.";
} else {
    if (empty($vPayload["parcels"]["box_type"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "parcels box_type parameter missing.";
    }
    if (empty($vPayload["parcels"]["dimension"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "parcels dimension parameter missing.";
    } else {
        if (empty($vPayload["parcels"]["dimension"]["width"])) {
            $vResponse["status"] = 400;
            $vResponse["error"] = "parcels dimension width parameter missing.";
        }
        if (empty($vPayload["parcels"]["dimension"]["height"])) {
            $vResponse["status"] = 400;
            $vResponse["error"] = "parcels dimension height parameter missing.";
        }
        if (empty($vPayload["parcels"]["dimension"]["depth"])) {
            $vResponse["status"] = 400;
            $vResponse["error"] = "parcels dimension depth parameter missing.";
        }
        if (empty($vPayload["parcels"]["dimension"]["unit"])) {
            // $vResponse["status"] = 400;
            // $vResponse["error"] = "parcels dimension unit parameter missing.";
        }
    }
    if (empty($vPayload["parcels"]["items"]) && count($vPayload["parcels"]["items"]) <= 0) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "parcels items parameter missing.";
    } else {
        $weight = 0;
        foreach ($vPayload["parcels"]["items"] as $key => $item) {
            if (empty($item["quantity"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = "parcels items." . $key . " quantity parameter missing.";
            }
            if (empty($item["description"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = "parcels items." . $key . " description parameter missing.";
            }
            if (empty($item["weight"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = "parcels items." . $key . " weight parameter missing.";
            } else {
                // if (empty($item["weight"]["unit"])) {
                //     $vResponse["status"] = 400;
                //     $vResponse["error"] = "parcels items." . $key . " weight unit parameter missing.";
                // }
                if (empty($item["weight"]["value"])) {
                    $vResponse["status"] = 400;
                    $vResponse["error"] = "parcels items." . $key . " weight value parameter missing.";
                } else {
                    $weight += ($item["quantity"] * $item["weight"]["value"]);
                }
            }
        }
    }

    $vPayload["parcels"]["weight"]["unit"] = "kg";
    $vPayload["parcels"]["weight"]["value"] = $weight;
    if (count($vResponse) <= 0) {
        $vParam["body"]["shipment"]["parcels"][] = $vPayload["parcels"];
    }


    if (isset($vPayload["ship_date"])) {
        $vParam["body"]["ship_date"] = $vPayload["ship_date"];
    }
}

if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    $vReturnData = call_aftership_api($vParam);
    
    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData);
        else
            v::$r = vR(200, $vReturnData->data);
    }
}
