<?php
require_once "config.php";
require_once "functions.php";
$vTable = "carts";
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
    //Query customer using email address
    $vParam["api_url"] =  "customers?include=addresses&email:in=" . $vPayload["billing_address"]["email"];
    $vParam["method"] = "GET";
    $vResponseCustomerData = call_big_commerce_api($vParam);
    // print_r($vResponseCustomerData->data);
    if (isset($vResponseCustomerData) && count($vResponseCustomerData->data) > 0) {
        $vParam["body"]["customer_id"] = $vResponseCustomerData->data[0]->id;
        $vPayload["billing_address"]["first_name"] = $vResponseCustomerData->data[0]->first_name;
        $vPayload["billing_address"]["last_name"] = $vResponseCustomerData->data[0]->last_name;
        if (!empty($vResponseCustomerData->data[0]->company))
            $vPayload["billing_address"]["company"] = $vResponseCustomerData->data[0]->company;
        if (!empty($vResponseCustomerData->data[0]->phone))
            $vPayload["billing_address"]["phone"] = $vResponseCustomerData->data[0]->phone;
        if (!empty($vResponseCustomerData->data[0]->address_count) && $vResponseCustomerData->data[0]->address_count > 0) {
            $customerBillingAddress = $vResponseCustomerData->data[0]->addresses[$vResponseCustomerData->data[0]->address_count - 1];
            $vPayload["billing_address"]["first_name"] = $customerBillingAddress->first_name;
            $vPayload["billing_address"]["last_name"] = $customerBillingAddress->last_name;
            $vPayload["billing_address"]["address1"] = $customerBillingAddress->address1;
            $vPayload["billing_address"]["city"] = $customerBillingAddress->city;
            $vPayload["billing_address"]["state"] = $customerBillingAddress->state_or_province;
            // $vPayload["billing_address"]["state_or_province_code"] = $customerBillingAddress->state;
            $vPayload["billing_address"]["company"] = $customerBillingAddress->company;
            $vPayload["billing_address"]["phone"] = $customerBillingAddress->phone;
            $vPayload["billing_address"]["postal_code"] = $customerBillingAddress->postal_code;
            $vPayload["billing_address"]["country_code"] = $customerBillingAddress->country_code;
        }
    } else {
        $vParam["body"]["customer_id"] = 0;
    }
    //create cart
    $vParam["api_url"] =  "carts";
    $vParam["method"] = "POST";

    $vParam["body"]["channel_id"] = 1;
    // $vParam["body"]["currency"]["code"] = "USD";
    // $vParam["body"]["locale"] = "en-US";
    // $vParam["body"]["currency"]["code"] = "EUR";
    // $vParam["body"]["locale"] = "en-IE";
    // print_r($vParam);
    $vResponseCartData = call_big_commerce_api($vParam);
    // print_r($vResponseCartData);exit;

    if (!isset($vResponseCartData->data)) {
        echo json_encode($vResponseCartData);
    } else {
        unset($vParam["body"]);
        // echo json_encode($vResponseCartData);exit;
        $cartId = $vResponseCartData->data->id;
        //stor cart meta 
        $connection = db_connection();
        $data["cart_id"] = $cartId;
        $data["meta"] = json_encode($vPayload["cart_meta"]);
        $vApiMode = "sandbox";
        if (isset($vPayload["api_mode"]) && strtolower($vPayload["api_mode"]) == "prod") {
            $vApiMode = "prod";
        }
        $vShipperInfo["api_mode"] = $vApiMode;
        $vShipperInfo["shipper"] = $vPayload["shipper_info"];
        $data["shipper_info"] = json_encode($vShipperInfo);

        $sql = "INSERT INTO {$vTable} (cart_id,meta,shipper_info,created) values ('" . $data["cart_id"] . "','" . $data["meta"] . "','" . $data["shipper_info"] . "',now()) RETURNING cart_id";
        $result = insert($connection, $sql);
        closeConnection($connection);
        //cart redirectu url
        $vParam["api_url"] =  "carts/" . $cartId . "/redirect_urls";
        $vParam["method"] = "POST";
        $vResponseDataCart = call_big_commerce_api($vParam);
        //push cart_id with the response
        $vResponseDataCart->data->cart_id = $cartId;
        // print_r($vResponseDataCart->data);exit;
        if (!isset($vResponseDataCart->data)) {
            echo json_encode($vResponseDataCart);
        } else {
            if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
                echo json_encode($vResponseDataCart->data);
            else {
                // echo json_encode($vResponseDataCart->data);
                v::$r = vR(200, $vResponseDataCart->data);
            }

            // exit;
            //add billing address
            // unset($vParam["body"]);
            // $vParam["api_url"] = "checkouts/" . $cartId . "/billing-address";
            // $vParam["body"] = $vPayload["billing_address"];
            // print_r($vParam);
            // $vResponseDataBilling = call_big_commerce_api($vParam);
            //    print_r($vResponseDataBilling);
            //add shipping address
            // if (isset($vResponseDataBilling->data)) {
            // unset($vParam["body"]);
            // $vParam["api_url"] = "checkouts/" . $cartId . "/consignments";
            // foreach ($vPayload["shipping_address"] as $key => $shippingAddress) {
            //     $consignments[$key]["address"] = $shippingAddress;
            //     if ($vReturnData->data->line_items->physical_items) {
            //         foreach ($vReturnData->data->line_items->physical_items as $key2 => $item) {
            //             $consignments[$key]["line_items"][$key2]["item_id"] = $item->id;
            //             $consignments[$key]["line_items"][$key2]["quantity"] = $item->quantity;
            //         }
            //     }
            //     if ($vReturnData->data->line_items->digital_items) {
            //         foreach ($vReturnData->data->line_items->digital_items as $key3 => $item) {
            //             $consignments[$key]["line_items"][$key3]["item_id"] = $item->id;
            //             $consignments[$key]["line_items"][$key3]["quantity"] = $item->quantity;
            //         }
            //     }
            // }
            // $vParam["body"] = $consignments;
            //    print_r($vParam);exit;
            // $vResponseConsignments = call_big_commerce_api($vParam);
            // print_r($vResponseConsignments);
            // if (isset($vResponseConsignments->data)) {
            //     if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            //         echo json_encode($vResponseDataCart->data);
            //     else
            //         v::$r = vR(200, $vResponseDataCart->data);
            // } else {
            //     echo json_encode($vResponseConsignments);
            // }
            //     if ($_SERVER["SERVER_NAME"] == "big-commerce.local")
            //         echo json_encode($vResponseDataCart->data);
            //     else
            //         v::$r = vR(200, $vResponseDataCart->data);
            // } else {
            //     echo json_encode($vResponseDataBilling);
            // }
        }
    }
}
