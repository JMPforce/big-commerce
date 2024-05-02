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
if (!isset($vPayload["items"])) {
    $vResponse["status"] = 400;
    $vResponse["error"] = "items parameter missing.";
} else {
    if (count($vPayload["items"]) <= 0) {
        $vResponse["status"] = 400;
        $vResponse["error"] = "items parameter missing.";
    } else {
        foreach ($vPayload["items"] as $key => $item) {
            if (empty($item["product_id"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = " items." . $key . " product_id parameter missing.";
            }
            if (empty($item["name"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = " items." . $key . " name parameter missing.";
            }
            if (empty($item["quantity"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = " items." . $key . " quantity parameter missing.";
            }
            if (empty($item["list_price"])) {
                $vResponse["status"] = 400;
                $vResponse["error"] = " items." . $key . " list_price parameter missing.";
            }
        }
        if (count($vResponse) <= 0) {
            $vParam["body"]["line_items"] = $vPayload["items"];
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
    $vParam["api_url"] =  "carts";
    $vParam["method"] = "POST";
    $vParam["body"]["customer_id"] = 0;
    $vParam["body"]["channel_id"] = 1;
    $vParam["body"]["currency"]["code"] = "USD";
    $vParam["body"]["locale"] = "en-US";

    $vReturnData = call_big_commerce_api($vParam);
    // print_r($vReturnData);

    if (!isset($vReturnData->data)) {
        echo json_encode($vReturnData);
    } else {
        unset($vParam["body"]);
        // echo json_encode($vReturnData);exit;
        $cartId = $vReturnData->data->id;
        //cart redirectu url
        $vParam["api_url"] =  "carts/" . $cartId . "/redirect_urls";
        $vParam["method"] = "POST";
        $vResponseDataCart = call_big_commerce_api($vParam);
        // print_r($vResponseDataCart);
        if (!isset($vResponseDataCart->data)) {
            echo json_encode($vResponseDataCart);
        } else {
            //add billing address
            unset($vParam["body"]);
            $vParam["api_url"] = "checkouts/" . $cartId . "/billing-address";
            $vParam["body"] = $vPayload["billing_address"];
            // print_r($vParam);
            $vResponseDataBilling = call_big_commerce_api($vParam);
            //    print_r($vResponseDataBilling);exit;
            //add shipping address
            if (isset($vResponseDataBilling->data)) {
                unset($vParam["body"]);
                $vParam["api_url"] = "checkouts/" . $cartId . "/consignments";
                foreach ($vPayload["shipping_address"] as $key => $shippingAddress) {
                    $consignments[$key]["address"] = $shippingAddress;
                    if ($vReturnData->data->line_items->physical_items) {
                        foreach ($vReturnData->data->line_items->physical_items as $key2 => $item) {
                            $consignments[$key]["line_items"][$key2]["item_id"] = $item->id;
                            $consignments[$key]["line_items"][$key2]["quantity"] = $item->quantity;
                        }
                    }
                    if ($vReturnData->data->line_items->digital_items) {
                        foreach ($vReturnData->data->line_items->digital_items as $key3 => $item) {
                            $consignments[$key]["line_items"][$key3]["item_id"] = $item->id;
                            $consignments[$key]["line_items"][$key3]["quantity"] = $item->quantity;
                        }
                    }
                }
                $vParam["body"] = $consignments;
            //    print_r($vParam);exit;
                $vResponseConsignments = call_big_commerce_api($vParam);
                // print_r($vResponseConsignments);
                if (isset($vResponseConsignments->data)) {
                    if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
                        echo json_encode($vResponseDataCart->data);
                    else
                        v::$r = vR(200, $vResponseDataCart->data);
                } else {
                    echo json_encode($vResponseConsignments);    
                }
            } else {
                echo json_encode($vResponseDataBilling);
            }
        }
    }
}
