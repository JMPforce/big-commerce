
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
  - [Customer create](#customer-create)
  - [Brands details](#brand-details)
  - [Brands delete](#brand-delete)


    
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
[
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
]
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