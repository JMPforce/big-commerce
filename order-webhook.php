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

    $vCustomerId = $vOrderResponseData->customer_id;
    if ($vOrderResponseData->id) {
        $vConnection = db_connection();
        //fetch cart meta from DB

        $vSql = "SELECT * FROM {$vTable} WHERE cart_id='" . $vOrderResponseData->cart_id . "'";
        $vResult = select($vConnection, $vSql);
        $vCartMeta = json_decode($vResult[0]["meta"]);
        $vShipperInfo = json_decode($vResult[0]["shipper_info"]);

        closeConnection($vConnection);

        //customer details
        $vParam["api_url"] =  "customers?id:in=" . $vCustomerId;
        $vParam["method"] = "GET";
        $vCustomerResponseData = call_big_commerce_api($vParam);

        foreach ($vCartMeta as $index => $cart) {

            foreach ($vOrderResponseData->consignments as $data) {

                foreach ($data->downloads[0]->line_items as $key => $row) {
                    //create order meta
                    // $vOrderMeta["permission_set"] = "write_and_sf_access";
                    // $vOrderMeta["namespace"] = "Shipment_" . $vOrderId . "_" . ($index + 1);
                    // $vOrderMeta["key"] = "shipment_" . $vOrderId . "_" . ($index + 1);
                    // $vOrderMeta["value"] = json_encode($cart);
                    // $vOrderMeta["description"] = "Shipment for order " . $vOrderId;
                    // $vParam["api_url"] =  "orders/" . $vOrderId . "/metafields";
                    // $vParam["method"] = "POST";
                    // $vParam["body"] = $vOrderMeta;
                    // $vOrderMetaResponseData = call_big_commerce_api($vParam);
                    //
                    unset($vParam["body"]);
                    $vParam["api_url"] =  "catalog/products/" . $row->product_id . "?include_fields=id,name&include=custom_fields";
                    $vParam["method"] = "GET";
                    $vItems = [];
                    $vReturnProductData = call_big_commerce_api($vParam);
                    //find item shipper info
                    $shipperIndex = findIndexByProductId($vShipperInfo, $row->product_id);
                    $vShipperId = $vShipperInfo[$shipperIndex]->shipper_method->shipper_id;
                    $vServiceType = $vShipperInfo[$shipperIndex]->shipper_method->service_type;
                    //find item dimensions
                    $widthIndex = findIndexByName($vReturnProductData->data->custom_fields, "width");
                    $width = $vReturnProductData->data->custom_fields[$widthIndex]->value;
                    $heightIndex = findIndexByName($vReturnProductData->data->custom_fields, "height");
                    $height = $vReturnProductData->data->custom_fields[$heightIndex]->value;
                    $depthIndex = findIndexByName($vReturnProductData->data->custom_fields, "depth");
                    $depth = $vReturnProductData->data->custom_fields[$depthIndex]->value;
                    $weightIndex = findIndexByName($vReturnProductData->data->custom_fields, "weight");
                    $weight = $vReturnProductData->data->custom_fields[$weightIndex]->value;
                    $vParcels["box_type"] = "custom";
                    

                    for ($i = 0; $i < $row->quantity; $i++) {
                        $vParcels["dimension"]["width"] = intval($width);
                        $vParcels["dimension"]["height"] = intval($height);
                        $vParcels["dimension"]["depth"] = intval($depth);
                        $vItems["description"] = $row->name;
                        $vItems["quantity"] = 1;
                        $vItems["item_id"] = strval($row->product_id);
                        $vItems["origin_country"] = getCountryCode($cart->ship_from->country);
                        $units = getUnitsByCountry($cart->ship_from->country);
                        $vParcels["dimension"]["unit"] = $units["dimension"];
                        // $vItems["price"]["currency"] = "USD";
                        $vItems["price"]["currency"] = $units["currency"];
                        $vItems["price"]["amount"] = intval($row->base_price);

                        $vItems["weight"]["unit"] = $units["weight"];
                        $vItems["weight"]["value"] = intval($weight);
                        $vTotalWeight = intval($weight);

                        $vParcels["items"] = [$vItems];
                        $vParcels["description"] = "Golf bags & luggage";
                        $vParcels["weight"]["unit"] = $units["weight"];
                        $vParcels["weight"]["value"] = $vTotalWeight;


                        $vParam["api_url"] =  "labels";
                        $vParam["method"] = "POST";
                        $reference = "reference-test-dhl1-" . $vOrderId . "-" . $row->product_id . "_" . ($i + 1);
                        $vParam["body"]["order_id"] = $reference;
                        $vParam["body"]["order_number"] = $reference;
                        $vParam["body"]["return_shipment"] = false;
                        $vParam["body"]["is_document"] = false;
                        $vParam["body"]["ship_date"] = date("Y-m-d", strtotime($cart->ship_from->date));

                        $vParam["body"]["custom_fields"]["pick_up_date"] = $cart->ship_from->date;
                        $vParam["body"]["custom_fields"]["drop_off_date"] = $cart->ship_to->date;

                        // $vParam["body"]["shipper_account"]["id"] = $GLOBALS["vConfig"]["AS_SHIPPER_ACCOUNT_ID"];
                        // $vParam["body"]["service_type"] = $GLOBALS["vConfig"]["AS_SHIPPER_SERVICE_TYPE"];
                        $vParam["body"]["shipper_account"]["id"] = $vShipperId;
                        $vParam["body"]["service_type"] = $vServiceType;
                        $vCustomerName = $vCustomerResponseData->data[0]->first_name;
                        if ($vCustomerResponseData->data[0]->last_name)
                            $vCustomerName .= " " . $vCustomerResponseData->data[0]->last_name;
                        $vParam["body"]["shipment"]["ship_from"]["contact_name"] = $vCustomerName;
                        if ($vCustomerResponseData->data[0]->company)
                            $vParam["body"]["shipment"]["ship_from"]["company_name"] = $vCustomerResponseData->data[0]->company;
                        $vParam["body"]["shipment"]["ship_from"]["street1"] = $cart->ship_from->address;
                        if (!empty($cart->ship_from->city))
                            $vParam["body"]["shipment"]["ship_from"]["city"] = $cart->ship_from->city;
                        $vParam["body"]["shipment"]["ship_from"]["state"] = $cart->ship_from->state;
                        $vParam["body"]["shipment"]["ship_from"]["postal_code"] = $cart->ship_from->postal_code;
                        $vParam["body"]["shipment"]["ship_from"]["phone"] = $cart->ship_from->phone;
                        $vParam["body"]["shipment"]["ship_from"]["email"] = $vCustomerResponseData->data[0]->email;
                        $vParam["body"]["shipment"]["ship_from"]["country"] = $cart->ship_from->country;

                        $vParam["body"]["shipment"]["ship_to"]["contact_name"] = $vCustomerName;
                        // $vParam["body"]["shipment"]["ship_to"]["company_name"] = !empty($vCustomerResponseData->data[0]->company) ? $vCustomerResponseData->data[0]->company : "Forecaddie";
                        if ($vCustomerResponseData->data[0]->company)
                            $vParam["body"]["shipment"]["ship_to"]["company_name"] = $vCustomerResponseData->data[0]->company;
                        $vParam["body"]["shipment"]["ship_to"]["street1"] = $cart->ship_to->address;
                        if (!empty($cart->ship_to->city))
                            $vParam["body"]["shipment"]["ship_to"]["city"] = $cart->ship_to->city;
                        $vParam["body"]["shipment"]["ship_to"]["state"] = $cart->ship_to->state;
                        $vParam["body"]["shipment"]["ship_to"]["postal_code"] = $cart->ship_to->postal_code;
                        $vParam["body"]["shipment"]["ship_to"]["phone"] = $cart->ship_to->phone;
                        $vParam["body"]["shipment"]["ship_to"]["email"] = $vCustomerResponseData->data[0]->email;
                        $vParam["body"]["shipment"]["ship_to"]["country"] = $cart->ship_to->country;

                        $vParam["body"]["shipment"]["parcels"] = [$vParcels];
                        if ($cart->ship_from->country != $cart->ship_to->country) {
                            $vParam["body"]["customs"]["purpose"] = "personal";
                            $vParam["body"]["customs"]["terms_of_trade"] = "dap";
                        }

                        // echo json_encode($vParam);
                        $vReturnData = call_aftership_api($vParam);
                        echo json_encode($vReturnData);
                    }
                }
            }
        }
    }
}


if ($_SERVER["SERVER_NAME"] == "big-commerce.local") {
    $vReturnData = ["status" => 200, "message" => "OK"];
    echo json_encode($vReturnData);
} else {
    v::$r = vR(200, $vReturnData);
}
