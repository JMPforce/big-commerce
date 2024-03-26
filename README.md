
# Big Commerce API

Big commerce API to Manage products, pricing and orders.


## Table of contents
- [Products](#product-lists)  
  - [Product lists](#product-lists)
  - [Product create](#product-create)
  - [Product details](#product-details)
  - [Product delete](#product-delete)
- [Categories](#category-lists)  
  - [Category lists](#category-lists)
  - [Category create](#category-create)
  - [Category details](#category-details)
  - [Category delete](#category-delete)


    
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

```http
  GET /get-product
```
Payload
```bash
{
    "product_id":121
}
```
