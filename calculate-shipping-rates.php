<?php
require_once "config.php";
require_once "functions.php";
$vQueryString = "";

$vResponse = [];


$vParam["api_url"] =  "rates";
$vParam["method"] = "POST";

if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else
    $vPayload = v::$a;

// print_r($vPayload);
$vParam["body"]["shipper_accounts"][]["id"] = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID"];

if (empty($vPayload["ship_from"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "ship_from parameter missing.";
} else {
    if (empty($vPayload["ship_from"]["street1"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_from street1 parameter missing.";
    }
    if (empty($vPayload["ship_from"]["contact_name"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_from contact_name parameter missing.";
    }
    if (empty($vPayload["ship_from"]["country"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_from country parameter missing.";
    }
    if (count($vResponse) <= 0) {
        $vParam["body"]["shipment"]["ship_from"] = $vPayload["ship_from"];
    }
}
if (empty($vPayload["ship_to"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "ship_to parameter missing.";
} else {
    if (empty($vPayload["ship_to"]["street1"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_to street1 parameter missing.";
    }
    if (empty($vPayload["ship_to"]["contact_name"])) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "ship_to contact_name parameter missing.";
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
            $vResponse["status"] = 400;
            $vResponse["error"] = "parcels dimension unit parameter missing.";
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
                if (empty($item["weight"]["unit"])) {
                    $vResponse["status"] = 400;
                    $vResponse["error"] = "parcels items." . $key . " weight unit parameter missing.";
                }
                if (empty($item["weight"]["value"])) {
                    $vResponse["status"] = 400;
                    $vResponse["error"] = "parcels items." . $key . " weight value parameter missing.";
                } else {
                    $weight += ($item["quantity"] * $item["weight"]["value"]);
                }
            }
        }
    }
    
    $vPayload["parcels"]["weight"]["unit"] = "lb";
    $vPayload["parcels"]["weight"]["value"] = $weight;
    if (count($vResponse) <= 0) {
        $vParam["body"]["shipment"]["parcels"][] = $vPayload["parcels"];
    }
    // if (empty($vPayload["return_to"])) {
    //     // $vResponse["status"] = 400;
    //     // $vResponse["error"] = "return_to parameter missing.";
    // } else {
    //     if (empty($vPayload["return_to"]["contact_name"])) {
    //         $vResponse["status"] = 400;
    //         $vResponse["error"] = "return_to contact_name parameter missing.";
    //     }
    //     if (empty($vPayload["return_to"]["street1"])) {
    //         $vResponse["status"] = 400;
    //         $vResponse["error"] = "return_to street1 parameter missing.";
    //     }
    //     if (empty($vPayload["return_to"]["country"])) {
    //         $vResponse["status"] = 400;
    //         $vResponse["error"] = "return_to country parameter missing.";
    //     }
    // }

    // if (count($vResponse) <= 0) {
    //     // $vParam["body"]["shipment"]["return_to"] = $vPayload["return_to"];
    // }
}
// print_r($vParam["body"]);exit;
if (count($vResponse) > 0) {
    if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
        echo json_encode($vResponse);
    } else {
        v::$r = vR(400, $vResponse);
    }
} else {
    // echo json_encode($vParam["body"]);exit;
    $vReturnData = call_aftership_api($vParam);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            echo json_encode($vReturnData->data);
        else
            v::$r = vR(200, $vReturnData->data);
    }
}
