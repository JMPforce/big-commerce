<?php
require_once "config.php";
require_once "functions.php";
$vPercentage = $GLOBALS["vConfig"]["FC_RATES_PERCENTAGE"];
$vDUnit = $GLOBALS["vConfig"]["D_UNITS"];
$vWUnit = $GLOBALS["vConfig"]["W_UNITS"];
$vQueryString = "";

$vResponse = [];

// $shipperAccountIdSandbox[]["id"]  = "3ba41ff5-59a7-4ff0-8333-64a4375c7f21";//USPS
$shipperAccountIdSandbox[]["id"]  = "6f43fe77-b056-45c3-bce4-9fec4040da0c"; //FedEx
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
if (isset($vShipperReturnData->meta) && $vShipperReturnData->meta->code == 200) {
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
}

if (isset($vPayload["api_mode"]) && strtolower($vPayload["api_mode"]) == "prod") {
    $vParam["body"]["shipper_accounts"] = $shipperAccountId;
} else {
    $vParam["body"]["shipper_accounts"] = $shipperAccountIdSandbox;
}

if (empty($vPayload["ship_from"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "ship_from parameter missing.";
} else {
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
    } else 
    if (count($vResponse) <= 0) {
        $vParam["body"]["shipment"]["ship_from"] = $vPayload["ship_from"];
    }
}
if (empty($vPayload["ship_to"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "ship_to parameter missing.";
} else {
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

    if (empty($vPayload["parcels"]["item_id"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "Parcels item_id parameter missing.";
    }
    $vReturnDataP = get_product($vPayload["parcels"]["item_id"]);
    if (empty($vReturnDataP->data)) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "Product not found.";
    } else {
        $vParamC["api_url"] =  "currencies";
        $vParamC["method"] = "GET";
        $vReturnDataC = call_big_commerce_api($vParamC, "v2");
        $currencyIndex = findIndexByKey($vReturnDataC, "is_default", true);
        $vDefaultCurrency = $vReturnDataC[$currencyIndex]->currency_code;

        $vCustomFields = $vReturnDataP->data->custom_fields;
        $vParcels["description"] = "Golf bags & luggage";
        $vParcels["dimension"]["unit"] = $vDUnit;
        //find item dimensions
        $widthIndex = findIndexByName($vReturnDataP->data->custom_fields, "width");
        $vParcels["dimension"]["width"] = floatval($vReturnDataP->data->custom_fields[$widthIndex]->value);
        $heightIndex = findIndexByName($vReturnDataP->data->custom_fields, "height");
        $vParcels["dimension"]["height"] = floatval($vReturnDataP->data->custom_fields[$heightIndex]->value);
        $depthIndex = findIndexByName($vReturnDataP->data->custom_fields, "depth");
        $vParcels["dimension"]["depth"] = floatval($vReturnDataP->data->custom_fields[$depthIndex]->value);
        $weightIndex = findIndexByName($vReturnDataP->data->custom_fields, "weight");
        $vWeight = $vReturnDataP->data->custom_fields[$weightIndex]->value;

        $vQuantity = ($vPayload["parcels"]["quantity"]) ? $vPayload["parcels"]["quantity"] : 1;
        $vItems["description"] = "Golf bags & luggage";
        $vItems["quantity"] = $vQuantity;
        $vItems["price"]["currency"] = $vDefaultCurrency;
        $vItems["price"]["amount"] = $vReturnDataP->data->price;
        $vItems["item_id"] = strval($vPayload["parcels"]["item_id"]);
        $vItems["weight"]["unit"] = $vWUnit;
        $vItems["weight"]["value"] = floatval($vWeight);

        $vParcels["items"][] = $vItems;

        $vParcels["box_type"] = "custom";
        $vParcels["weight"]["unit"] = $vWUnit;
        $vParcels["weight"]["value"] = floatval($vWeight * $vQuantity);
        
        $vParam["body"]["shipment"]["parcels"][] = $vParcels;
        $vParam["body"]["shipment"]["delivery_instructions"] = "Handle with care";

        if (isset($vPayload["ship_date"])) {
            $vParam["body"]["ship_date"] = $vPayload["ship_date"];
        }
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
        //Get currency, FedEx return price in USD and DHL returns in EUR, So we need to convert them to store default currency
        if ($vReturnData->data->rates) {
            foreach ($vReturnData->data->rates as $key => $rates) {
                $vAmount = $rates->total_charge->amount;
                $vCurrency = $rates->total_charge->currency;
                $vRatesCurrencyIndex = findIndexByKey($vReturnDataC, "currency_code", $vCurrency);
                $vFcActualCosts["amount"] = $vAmount + (($vPercentage / 100) * $vAmount);
                if ($vDefaultCurrency != $vReturnDataC[$vRatesCurrencyIndex]->currency_code) {
                    //Convert currency to store default
                    $vFcActualCosts["amount"] = ($vAmount + (($vPercentage / 100) * $vAmount)) * $vReturnDataC[$vRatesCurrencyIndex]->currency_exchange_rate;
                }
                $vFcActualCosts["currency"] = $vDefaultCurrency;
                $vReturnData->data->rates[$key]->fc_actual_costs = $vFcActualCosts;
            }

            if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
                echo json_encode($vReturnData);
            else {
                v::$r = vR(200, $vReturnData->data);
            }
        }
    }
}
