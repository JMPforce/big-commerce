
# Big Commerce API

Big commerce API to Manage products, pricing and orders.


## Table of contents
- [Products](#get-all-products)  
  - [Product lists](#get-all-products)
  - [Create Product](#create-product)
  - [Product details](#product-details)
  - [Product delete](#product-delete)
- [Categories](#get-all-categories)  
  - [Category lists](#get-all-categories)
  - [Category create](#category-create)
  - [Category details](#category-details)
  - [Category delete](#category-delete)
- [Brands](#get-all-brands)  
  - [Brands lists](#get-all-brands)
  - [Brands create](#brand-create)
  - [Brands details](#brand-details)
  - [Brands delete](#brand-delete)
- [Customers](#get-all-customers)  
  - [Customer lists](#get-all-customers)
  - [Create customer](#create-customer)
  - [Update customer](#update-customer)
  - [Delete customer](#delete-customer)
- [Customer Address](#get-all-customers-addresses)  
  - [Customer address lists](#get-all-customers-addresses)
  - [Create customer address](#create-customer-address)
  - [Update customer address](#update-customer-address)
  - [Delete customer address](#delete-customer-address)
- [Order](#webhooks)  
  - [Webhooks](#webhooks)
  
- [Webhooks](#webhooks)  
  - [Webhooks](#webhooks)
  - [Create webhooks](#create-webhooks)
  - [Webhooks log](#webhooks-log)
  - [Get Order Shipments](#get-order-shipments)
- [Afterships API](#calculate-shipping-rates)  
  - [Calculate shipping rates](#calculate-shipping-rates)
  - [Tracking order](#tracking-order)


    
## API Reference


#### Get all products
[(Back to top)](#table-of-contents)
```http
  GET /product-lists
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `limit` | `number` |  Controls the number of items per page in a limited (paginated) list of products. The default product limit is 50 with a maximum limit of 250. |
| `page` | `number` |  Specifies the page number in a limited (paginated) list of products.|
| `sort` | `string` |  Allowed values are id, name, sku | price | date_modified | date_last_imported | inventory_level | is_visible | total_sold|
| `direction` | `string` | Sort direction. Acceptable values are: asc, desc.|
| `is_featured` | `number` | Filter items by is_featured. 1 for true, 0 for false.|
| `id` | `number` | Filter items by ID.|
| `name` | `string` | Filter items by name.|
| `brand_id` | `number` | Filter items by brand_id.|
| `keyword` | `string` | Filter items by keywords found in the name or sku fields.|
| `categories:in` | `array` | Filter items by categories. Use for products in multiple categories. For example, categories:in=12,15.|
| `sku` | `string` | Filter items by main SKU. To filter by variant SKU, see Get All Variants.|
| `include_fields` | `array` | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.|

#### Product details
[(Back to top)](#table-of-contents)
```http
  GET /get-product
```
Payload
```bash
{
    "product_id":121
}
```

#### Create product
[(Back to top)](#table-of-contents)
```http
  GET /create-product
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. A unique product name. >= 1 characters<= 250 characters|
| `type` | `string` | **Required**. The product type. One of: physical - a physical stock unit, digital - a digital download. Allowed: physical | digital|
| `price` | `number` | **Required**. The price of the product. The price should include or exclude tax, based on the store settings.|
| `weight` | `number` | **Required**. Weight of the product, which can be used when calculating shipping costs. This is based on the unit set on the store|
| `categories` | `array` | Existing category id as array, like [22,23]|
| `category_name` | `string` | Unique category name to create during product creation |


Sample Payload
```bash
{
  "name": "Smith Journal 13",
  "type": "physical",
  "sku": "SM-13",
  "description": "<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vel metus ac est egestas porta sed quis erat. Integer id nulla massa. Proin vitae enim nisi. Praesent non dignissim nulla. Nulla mattis id massa ac pharetra. Mauris et nisi in dolor aliquam sodales. Aliquam dui nisl, dictum quis leo sit amet, rutrum volutpat metus. Curabitur libero nunc, interdum ac libero non, tristique porttitor metus. Ut non dignissim lorem, in vestibulum leo. Vivamus sodales quis turpis eget.</span></p>",
  "weight": 9999999999,
  "width": 9999999999,
  "depth": 9999999999,
  "height": 9999999999,
  "price": 0.1,
  "cost_price": 0.1,
  "retail_price": 0.1,
  "sale_price": 0.1,
  "map_price": 0,
  "tax_class_id": 255,
  "product_tax_code": "string",
  "categories": [
    0
  ],
  "brand_id": 1000000000,
  "brand_name": "Common Good",
  "inventory_level": 2147483647,
  "inventory_warning_level": 2147483647,
  "inventory_tracking": "none",
  "fixed_cost_shipping_price": 0.1,
  "is_free_shipping": true,
  "is_visible": true,
  "is_featured": true,
  "related_products": [
    0
  ],
  "warranty": "string",
  "bin_picking_number": "string",
  "layout_file": "string",
  "upc": "string",
  "search_keywords": "string",
  "availability_description": "string",
  "availability": "available",
  "gift_wrapping_options_type": "any",
  "gift_wrapping_options_list": [
    0
  ],
  "sort_order": -2147483648,
  "condition": "New",
  "is_condition_shown": true,
  "order_quantity_minimum": 1000000000,
  "order_quantity_maximum": 1000000000,
  "page_title": "string",
  "meta_keywords": [
    "string"
  ],
  "meta_description": "string",
  "view_count": 1000000000,
  "preorder_release_date": "2019-08-24T14:15:22Z",
  "preorder_message": "string",
  "is_preorder_only": true,
  "is_price_hidden": true,
  "price_hidden_label": "string",
  "custom_url": {
    "url": "string",
    "is_customized": true,
    "create_redirect": true
  },
  "open_graph_type": "product",
  "open_graph_title": "string",
  "open_graph_description": "string",
  "open_graph_use_meta_description": true,
  "open_graph_use_product_name": true,
  "open_graph_use_image": true,
  "gtin": "string",
  "mpn": "string",
  "date_last_imported": "string",
  "reviews_rating_sum": 3,
  "reviews_count": 4,
  "total_sold": 80,
  "custom_fields": [
    {
      "id": 6,
      "name": "ISBN",
      "value": "1234567890123"
    }
  ],
  "bulk_pricing_rules": [
    {
      "quantity_min": 10,
      "quantity_max": 50,
      "type": "price",
      "amount": 10
    }
  ],
  "images": [
    {
      "image_file": "string",
      "is_thumbnail": true,
      "sort_order": -2147483648,
      "description": "string",
      "image_url": "string",
      "id": 0,
      "product_id": 0,
      "date_modified": "2019-08-24T14:15:22Z"
    }
  ],
  "videos": [
    {
      "title": "Writing Great Documentation",
      "description": "A video about documenation",
      "sort_order": 1,
      "type": "youtube",
      "video_id": "z3fRu9pkuXE",
      "id": 0,
      "product_id": 0,
      "length": "string"
    }
  ],
  "variants": [
    {
      "cost_price": 0.1,
      "price": 0.1,
      "sale_price": 0.1,
      "retail_price": 0.1,
      "weight": 0.1,
      "width": 0.1,
      "height": 0.1,
      "depth": 0.1,
      "is_free_shipping": true,
      "fixed_cost_shipping_price": 0.1,
      "purchasing_disabled": true,
      "purchasing_disabled_message": "string",
      "upc": "string",
      "inventory_level": 2147483647,
      "inventory_warning_level": 2147483647,
      "bin_picking_number": "string",
      "mpn": "string",
      "gtin": "012345678905",
      "product_id": 0,
      "id": 0,
      "sku": "string",
      "option_values": [
        {
          "option_display_name": "Color",
          "label": "Beige"
        }
      ],
      "calculated_price": 0.1,
      "calculated_weight": 0
    }
  ]
}
```


#### Get all categories
[(Back to top)](#table-of-contents)
```http
  GET /category-lists
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `limit` | `number` |  Controls the number of items per page in a limited (paginated) list of products. The default product limit is 50 with a maximum limit of 250. |
| `page` | `number` |  Specifies the page number in a limited (paginated) list of products.|
| `sort` | `string` |  Allowed values are id, name, sku | price | date_modified | date_last_imported | inventory_level | is_visible | total_sold|
| `direction` | `string` | Sort direction. Acceptable values are: asc, desc.|
| `id` | `number` | Filter items by ID.|
| `name` | `string` | Filter items by name.|
| `name:like` | `string` | Filter items by part of a name. For example, name:like=new returns brands with names that include new.|
| `keyword` | `string` | Filter items by part of a name. For example, name:like=new returns brands with names that include new.|


#### Get all brands
[(Back to top)](#table-of-contents)
```http
  GET /brand-lists
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `limit` | `number` |  Controls the number of items per page in a limited (paginated) list of products. The default product limit is 50 with a maximum limit of 250. |
| `page` | `number` |  Specifies the page number in a limited (paginated) list of products.|
| `sort` | `string` |  Allowed values are id, name, sku | price | date_modified | date_last_imported | inventory_level | is_visible | total_sold|
| `direction` | `string` | Sort direction. Acceptable values are: asc, desc.|
| `id` | `number` | Filter items by ID.|
| `name` | `string` | Filter items by name.|
| `name:like` | `string` | Filter items by part of a name. For example, name:like=new returns brands with names that include new.|


#### Get all customers
[(Back to top)](#table-of-contents)
```http
  GET /customer-lists
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `limit` | `number` |  Controls the number of items per page in a limited (paginated) list of products. The default product limit is 50 with a maximum limit of 250. |
| `page` | `number` |  Specifies the page number in a limited (paginated) list of products.|
| `sort` | `string` |  Allowed values are id, name, sku | price | date_modified | date_last_imported | inventory_level | is_visible | total_sold|
| `direction` | `string` | Sort direction. Acceptable values are: asc, desc.|
| `id` | `number` | Filter items by ID.|
| `name:in` | `array` | Filter items by first_name and last_name. name=james moriarty|
| `name:like` | `array` | Filter items by substring in first_name and last_name. name:like=moriarty, sherlock Concatenates the first_name and last_name fields.|
| `company:in` | `array` | Filter items by company. company:in=bigcommerce,commongood|
| `email:in` | `array` | Filter items by email. email:in=janedoe@example.com|


#### Create customer
[(Back to top)](#table-of-contents)
```http
  GET /create-customer
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `email` | `string` | **Required**. The email of the customer. Must be unique. >= 3 characters<= 255 characters|
| `first_name` | `string` | **Required**. The first name of the customer. >= 1 characters<= 100 characters|
| `last_name` | `string` | **Required**. The first name of the customer. >= 1 characters<= 100 characters|


Sample Payload
```bash
  {
    "email": "string@example.com",
    "first_name": "string",
    "last_name": "string",
    "company": "string",
    "phone": "string",
    "notes": "string",
    "tax_exempt_category": "string",
    "customer_group_id": 0,
    "addresses": [
      {
        "address1": "Addr 1",
        "address2": "",
        "address_type": "residential",
        "city": "San Francisco",
        "company": "History",
        "country_code": "US",
        "first_name": "Ronald",
        "last_name": "Swimmer",
        "phone": "707070707",
        "postal_code": "33333",
        "state_or_province": "California",
        "form_fields": [
          {
            "name": "test",
            "value": "test"
          }
        ]
      }
    ],
    "authentication": {
      "force_password_reset": true,
      "new_password": "string123"
    },
    "accepts_product_review_abandoned_cart_emails": true,
    "store_credit_amounts": [
      {
        "amount": 43.15
      }
    ],
    "origin_channel_id": 1,
    "channel_ids": [
      1
    ],
    "form_fields": [
      {
        "name": "test",
        "value": "test"
      }
    ]
  }
```

#### Update customer
[(Back to top)](#table-of-contents)
```http
  GET /update-customer
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `string` | **Required**. ID of the Customer This must be included in the request body|


Sample payload
```bash
{
    "id": 1,
    "email": "sahbajuddin@gmail.com",
    "first_name": "Sahbaj",
    "last_name": "Uddin",
    "company": "bGlobal",
    "phone": "123456",
    "notes": "string",
    "tax_exempt_category": "string",
    "customer_group_id": 0
    
}
```

#### Delete customer
[(Back to top)](#table-of-contents)
```http
  GET /delete-customer
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id:in` | `string` | **Required**. Filter items by ID. id:in=4,5,6|


#### Get all customers addresses
[(Back to top)](#table-of-contents)
```http
  GET /customer-address-lists
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `limit` | `number` |  Items count per page. |
| `page` | `number` |  Page number. page=1|
| `id:in` | `array` | Filter items by ID. id:in=4,5,6|
| `customer_id:in` | `array` | Filter items by customer_id. customer_id:in=4,5,6|
| `name:in` | `array` | Filter items by first_name and last_name. name=james moriarty|
| `company:in` | `array` | Filter items by company. company:in=bigcommerce,commongood|



#### Create customer address
[(Back to top)](#table-of-contents)
```http
  GET /create-customer-address
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `customer_id` | `number` | **Required**. Customer ID|
| `first_name` | `string` | **Required**. The first name of the customer address. >= 1 characters<= 255 characters|
| `last_name` | `string` | **Required**. The first name of the customer address. >= 1 characters<= 255 characters|
| `address1` | `string` | **Required**. The address 1 line. Example 123 Example Street|
| `city` | `string` | **Required**. The city of the customer address. >= 0 characters<= 100 characters|
| `state_or_province` | `string` | **Required**. The state or province name spelled out in full. It is required for countries that need a state/province to complete an address. State or province codes not accepted. >= 0 characters<= 100 characters|
| `postal_code` | `string` | **Required**. The postal code of the customer address. It is required for countries that need postal codes to complete an address. >= 0 characters<= 30 characters|
| `country_code` | `string` | **Required**. The country code of the customer address. >= 2 characters<= 2 characters|


Sample Payload
```bash
  {
    "first_name": "John",
    "last_name": "Doe",
    "address1": "111 E West Street",
    "address2": "654",
    "city": "Akron",
    "state_or_province": "Ohio",
    "postal_code": "44325",
    "country_code": "US",
    "phone": "1234567890",
    "address_type": "residential",
    "customer_id": 11,
    "form_fields": [
      {
        "name": "test",
        "value": "test"
      }
    ]
  }
```

#### Update customer address
[(Back to top)](#table-of-contents)
```http
  GET /update-customer-address
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `string` | **Required**. ID of the Customer This must be included in the request body|


Sample payload
```bash
  {
    "id": 11,
    "first_name": "John",
    "last_name": "Doe",
    "address1": "111 E West Street",
    "address2": "654",
    "city": "Akron",
    "state_or_province": "Ohio",
    "postal_code": "44325",
    "country_code": "US",
    "phone": "1234567890",
    "address_type": "residential",
    "form_fields": [
      {
        "name": "test",
        "value": "test"
      }
    ]
  }
```


#### Delete customer address
[(Back to top)](#table-of-contents)
```http
  GET /delete-customer-address
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id:in` | `string` | **Required**. Filter items by ID. id:in=4,5,6|



#### Webhooks
[(Back to top)](#table-of-contents)
```http
  POST /webhooks
```
Destination need to be set to this endpoint while creating webhook.


#### Create webhooks
[(Back to top)](#table-of-contents)
```http
  POST /create-webhooks
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `scope` | `string` | **Required**. Event you subscribe to. https://developer.bigcommerce.com/docs/webhooks/callbacks |
| `destination` | `url` | **Required**. URL must be active, return a 200 response, and be served on port 443. Custom ports arenʼt currently supported. |
| `is_active` | `boolean` | Boolean value that indicates whether the webhook is active or not. A webhook subscription becomes deactivated after 90 days of inactivity. Default is true |

#### Webhooks log
[(Back to top)](#table-of-contents)
```http
  GET /get-order-shipment
```
List all the Webhooks logs that was called from big-commerce during shipment created/updated/deleted events triggered


#### Get Order Shipments
[(Back to top)](#table-of-contents)
```http
  POST /get-order-shipment
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `order_id` | `number` | **Required**. order_id |


### Aftership API

#### Calculate shipping rates
[(Back to top)](#table-of-contents)
```http
  POST /calculate-shipping-rates
```
| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `ship_from` | `Address` | **Required**. ship_from |
| `ship_to` | `Address` | **Required**. ship_to |
| `parcels` | `Array` | **Required**. parcels |

#### ship_from
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `street1` | `String` | **Required**. street1, address_line1 of address |
| `country` | `String` | **Required**. country, Country in ISO 3166-1 alpha 3 code |
| `contact_name` | `String` | **Required**. contact_name, contact name of address |

#### ship_to
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `street1` | `String` | **Required**. street1, address_line1 of address |
| `country` | `String` | **Required**. country, Country in ISO 3166-1 alpha 3 code |
| `contact_name` | `String` | **Required**. contact_name, contact name of address |

#### parcels
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `box_type` | `String` | **Required**. box_type, Type of box for packaging |
| `dimension` | `Dimension` | **Required**. dimension, Dimension object: the description of width/height/depth information |
| `items` | `Array` | **Required**. items, items of package Item object, use to describe product to ship |
| `weight` | `Weight` | **Required**. items, Weight object: unit weight of the item |

####  dimension (parcels)
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `width` | `Number` | **Required**. width |
| `height` | `Number` | **Required**. height |
| `depth` | `Number` | **Required**. depth |
| `unit` | `String` | **Required**. unit, Allowed values: cm, in, mm, m, ft, yd |

#### items (parcels)
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `quantity` | `Integer` | **Required**. quantity, The quantity of the item Minimum: 1 |
| `description` | `String` | **Required**. description, The description of the item |
| `weight` | `Weight` | **Required**. weight, Weight object: unit weight of the item |

####  Weight (items)
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `unit` | `String` | **Required**. unit, weight unit, Allowed values: lb, kg, oz, g |
| `value` | `Number` | **Required**. value, value of Weight |

####  Weight (parcels)
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `unit` | `String` | **Required**. unit, weight unit, Allowed values: lb, kg, oz, g |
| `value` | `Number` | **Required**. value, value of Weight |

####  return_to
[(Back to top)](#table-of-contents)

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `street1` | `String` | **Required**. street1, address_line1 of address |
| `country` | `String` | **Required**. country, Country in ISO 3166-1 alpha 3 code |
| `contact_name` | `String` | **Required**. contact_name, contact name of address |



Sample payload
```bash
{
    "ship_from": {
        "contact_name": "AfterShip Shipping",
        "company_name": "AfterShip Shipping",
        "street1": "230 W 200 S LBBY",
        "city": "Salt Lake City",
        "state": "UT",
        "postal_code": "84101",
        "country": "USA",
        "phone": "123456789",
        "email": "test@test.com"
    },
    "ship_to": {
        "contact_name": "AfterShip Shipping",
        "company_name": "AfterShip Shipping",
        "street1": "230 W 200 S LBBY",
        "city": "Salt Lake City",
        "state": "UT",
        "postal_code": "84101",
        "country": "USA",
        "phone": "123456789",
        "email": "test@test.com"
    },
    "parcels": {
        "box_type": "custom",
        "dimension": {
            "width": 10,
            "height": 10,
            "depth": 10,
            "unit": "cm"
        },
        "items": [
            {
                "description": "Food Bar",
                "quantity": 1,
                "price": {
                    "currency": "USD",
                    "amount": 100
                },
                "item_id": "1234567",
                "origin_country": "CHN",
                "weight": {
                    "unit": "kg",
                    "value": 10
                },
                "sku": "imac2014",
                "hs_code": "1006.30"
            }
        ],
        "description": "Food XS",
        "weight": {
            "unit": "kg",
            "value": 10
        }
    },
    "return_to": {
        "contact_name": "AfterShip Shipping",
        "street1": "This is the first streeet",
        "street2": "This is the second streeet",
        "city": "New York",
        "state": "New York",
        "postal_code": "10001",
        "country": "USA",
        "phone": "1-123-456-5496",
        "email": "test@test.test",
        "type": "residential"
    },
    "delivery_instructions": "handle with care"
}
```


#### Tracking order
[(Back to top)](#table-of-contents)
```http
  POST /tracking-order
```
| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `tracking_code` | `String or Number` | **Required**. it can be order id or tracking number |
