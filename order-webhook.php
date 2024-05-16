<?php
require_once "config.php";
require_once "functions.php";
$vTable = "carts";
$vResponse = [];
$vQuery = "";
parse_str($_SERVER['QUERY_STRING'], $vQuery);

if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vPayload = json_decode(file_get_contents('php://input'), true);
} else {
    $vPayload = v::$a;
}

if ($vPayload["data"]["id"] && $vPayload["scope"] = "store/order/created") {
    $vOrderId = $vPayload["data"]["id"];
    $vParam["api_url"] =  "orders/" . $vOrderId . "/?include=consignments.line_items";
    $vParam["method"] = "GET";
    //order details
    $vOrderResponseData = call_big_commerce_api($vParam, "v2");
    // print_r($vOrderResponseData);exit;
    $vCustomerId = $vOrderResponseData->customer_id;
    if ($vOrderResponseData->id) {
        $vConnection = db_connection();
        //fetch cart meta from DB
        $vSql = "SELECT * FROM {$vTable} WHERE cart_id='" . $vOrderResponseData->cart_id . "'";
        $vResult = select($vConnection, $vSql);
        $vCartMeta = json_decode($vResult[0]["meta"]);
        closeConnection($vConnection);

        //customer details
        $vParam["api_url"] =  "customers?id:in=" . $vCustomerId;
        $vParam["method"] = "GET";
        $vCustomerResponseData = call_big_commerce_api($vParam);
        // print_r($vCustomerResponseData);exit;
        foreach ($vCartMeta as $index => $cart) {
            //create order meta
            $vOrderMeta["permission_set"] = "write_and_sf_access";
            $vOrderMeta["namespace"] = "Shipment_" . $vOrderId . "_" . ($index + 1);
            $vOrderMeta["key"] = "shipment_" . $vOrderId . "_" . ($index + 1);
            $vOrderMeta["value"] = json_encode($cart);
            $vOrderMeta["description"] = "Shipment for order " . $vOrderId;
            $vParam["api_url"] =  "orders/" . $vOrderId . "/metafields";
            $vParam["method"] = "POST";
            $vParam["body"] = $vOrderMeta;
            $vOrderMetaResponseData = call_big_commerce_api($vParam);
            // print_r($vOrderMetaResponseData);
            unset($vParam["body"]);
            $vParcels["box_type"] = "custom";
            $vParcels["dimension"]["width"] = 10;
            $vParcels["dimension"]["height"] = 10;
            $vParcels["dimension"]["depth"] = 10;
            $vParcels["dimension"]["unit"] = 'in';
            $vTotalWeight = 0;
            foreach ($vOrderResponseData->consignments as $data) {
                foreach ($data->downloads[0]->line_items as $key => $row) {
                    $vItems[$key]["description"] = $row->name;
                    $vItems[$key]["quantity"] = $row->quantity;
                    $vItems[$key]["item_id"] = strval($row->product_id);
                    $vItems[$key]["price"]["currency"] = "USD";
                    $vItems[$key]["price"]["amount"] = intval($row->base_price);
                    $vItems[$key]["origin_country"] = $cart->ship_from->country;
                    $vItems[$key]["weight"]["unit"] = "lb";
                    $vItems[$key]["weight"]["value"] = intval($row->weight);
                    $vTotalWeight += intval($row->weight);
                }
            }

            $vParcels["items"] = $vItems;
            $vParcels["description"] = "Golf bags";
            $vParcels["weight"]["unit"] = "lb";
            $vParcels["weight"]["value"] = $vTotalWeight;

            $vParam["api_url"] =  "labels";
            $vParam["method"] = "POST";

            $vParam["body"]["order_id"] = strval($vOrderId);
            $vParam["body"]["order_number"] = $vOrderId . "-" . ($index + 1);
            $vParam["body"]["return_shipment"] = false;
            $vParam["body"]["is_document"] = false;
            $vParam["body"]["service_type"] = "usps-discounted_express_mail";
            $vParam["body"]["ship_date"] = date("Y-m-d", strtotime($cart->ship_from->date));

            $vParam["body"]["custom_fields"]["pick_up_date"] = $cart->ship_from->date;
            $vParam["body"]["custom_fields"]["drop_off_date"] = $cart->ship_to->date;

            $vParam["body"]["shipper_account"]["id"] = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID"];
            $vParam["body"]["shipment"]["ship_from"]["contact_name"] = $vCustomerResponseData->data[0]->first_name;
            $vParam["body"]["shipment"]["ship_from"]["company_name"] = !empty($vCustomerResponseData->data[0]->company) ? $vCustomerResponseData->data[0]->company : "Forecaddie";
            $vParam["body"]["shipment"]["ship_from"]["street1"] = $cart->ship_from->address;
            $vParam["body"]["shipment"]["ship_from"]["city"] = $cart->ship_from->city;
            $vParam["body"]["shipment"]["ship_from"]["state"] = $cart->ship_from->state;
            $vParam["body"]["shipment"]["ship_from"]["postal_code"] = $cart->ship_from->postal_code;
            $vParam["body"]["shipment"]["ship_from"]["phone"] = $cart->ship_from->phone;
            $vParam["body"]["shipment"]["ship_from"]["email"] = $vCustomerResponseData->data[0]->email;
            $vParam["body"]["shipment"]["ship_from"]["country"] = $cart->ship_from->country;

            $vParam["body"]["shipment"]["ship_to"]["contact_name"] = $vCustomerResponseData->data[0]->first_name;
            $vParam["body"]["shipment"]["ship_to"]["company_name"] = !empty($vCustomerResponseData->data[0]->company) ? $vCustomerResponseData->data[0]->company : "Forecaddie";
            $vParam["body"]["shipment"]["ship_to"]["street1"] = $cart->ship_to->address;
            $vParam["body"]["shipment"]["ship_to"]["city"] = $cart->ship_to->city;
            $vParam["body"]["shipment"]["ship_to"]["state"] = $cart->ship_to->state;
            $vParam["body"]["shipment"]["ship_to"]["postal_code"] = $cart->ship_to->postal_code;
            $vParam["body"]["shipment"]["ship_to"]["phone"] = $cart->ship_to->phone;
            $vParam["body"]["shipment"]["ship_to"]["email"] = $vCustomerResponseData->data[0]->email;
            $vParam["body"]["shipment"]["ship_to"]["country"] = $cart->ship_to->country;


            $vParam["body"]["shipment"]["parcels"] = [$vParcels];
            // print_r($vParam);
            $vReturnData = call_aftership_api($vParam);
            print_r($vReturnData);
            // exit;
        }
    }
}


if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vReturnData = ["status" => 200, "message" => "OK"];
    echo json_encode($vReturnData);
} else {
    v::$r = vR(200, $vReturnData);
}
