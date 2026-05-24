# MCD - CafThé Dashboard Vendeur

## Entities

### User
A user represents a person who can access the seller dashboard.

Fields:
- id
- name
- email
- password
- role
- is_active

Roles:
- admin
- vendeur

### Category
A category groups products.

Examples:
- Thé
- Café
- Accessoire

Fields:
- id
- name
- description

### Product
A product represents an item sold by CafThé.

Fields:
- id
- category_id
- sku
- name
- description
- sale_type
- price
- vat_rate
- stock
- image
- origin
- is_active

### Client
A client represents a customer known by the shop.

Fields:
- id
- name
- email
- phone
- address
- favorites
- abandoned_cart

### Sale
A sale represents one order or receipt.

Fields:
- id
- user_id
- client_id
- status
- payment_method
- delivery_method
- total_ht
- total_vat
- total_ttc
- sale_date

### Sale Item
A sale item represents one product line inside a sale.

Fields:
- id
- sale_id
- product_id
- quantity
- unit_price
- vat_rate
- total_ht
- total_vat
- total_ttc

