# MLD - CafThé Dashboard Vendeur

## users

users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150),
  email VARCHAR(180) UNIQUE,
  password VARCHAR(255),
  role ENUM('admin', 'vendeur'),
  is_active BOOLEAN
)

## categories

categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) UNIQUE,
  description TEXT
)

## products

products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT,
  sku VARCHAR(80) UNIQUE,
  name VARCHAR(180),
  description TEXT,
  sale_type ENUM('poids', 'unite'),
  price DECIMAL(10,2),
  vat_rate DECIMAL(4,2),
  stock DECIMAL(10,2),
  image VARCHAR(255),
  origin VARCHAR(150),
  is_active BOOLEAN,
  FOREIGN KEY category_id REFERENCES categories(id)
)

## clients

clients (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150),
  email VARCHAR(180) UNIQUE,
  phone VARCHAR(30),
  address TEXT,
  favorites TEXT,
  abandoned_cart TEXT
)

## sales

sales (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  client_id INT NULL,
  status ENUM('pending', 'preparing', 'shipped', 'delivered', 'completed', 'cancelled'),
  payment_method ENUM('cb', 'especes', 'cheque'),
  delivery_method ENUM('magasin', 'livraison'),
  total_ht DECIMAL(10,2),
  total_vat DECIMAL(10,2),
  total_ttc DECIMAL(10,2),
  sale_date DATETIME,
  FOREIGN KEY user_id REFERENCES users(id),
  FOREIGN KEY client_id REFERENCES clients(id)
)

## sale_items

sale_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  sale_id INT,
  product_id INT,
  quantity DECIMAL(10,2),
  unit_price DECIMAL(10,2),
  vat_rate DECIMAL(4,2),
  total_ht DECIMAL(10,2),
  total_vat DECIMAL(10,2),
  total_ttc DECIMAL(10,2),
  FOREIGN KEY sale_id REFERENCES sales(id),
  FOREIGN KEY product_id REFERENCES products(id)
)
