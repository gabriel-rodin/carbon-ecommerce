CREATE TABLE products (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10.2) NOT NULL,
    list_price DECIMAL(10.2) NOT NULL,
    brand INT NOT NULL,
    categories VARCHAR(255) NOT NULL,
    image TEXT NOT NULL,
    description TEXT NOT NULL,
    featured TINYINT NOT NULL DEFAULT 0,
    sizes VARCHAR(255) NOT NULL,
    deleted TINYINT NOT NULL DEFAULT 0,
    sold TINYINT NOT NULL DEFAULT 0
);

CREATE TABLE brand (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(255) NOT NULL
);

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(255) NOT NULL,
    parent INT NOT NULL
);

CREATE TABLE cart (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    items VARCHAR(255) NOT NULL,
    paid TINYINT NOT NULL DEFAULT 0,
    shipped TINYINT NOT NULL DEFAULT 0
);

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    permissions VARCHAR(255) NOT NULL,
    join_date DATETIME NOT NULL,
    last_login DATETIME
);
/**
 * ADMIN LOGIN /admin/login.php
 * email : test@mail.com
 * password : test01
 */
INSERT INTO users (id, full_name, email, password, permissions, join_date, last_login) VALUES (1, 'John Doe', 'test@mail.com', '$2y$12$ripple1981RIPPLE19810u5.0lXcsE3ha5lShk/thX4Z6YNHII6TO', 'admin', NOW(), NOW());

CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    charge_id INT NOT NULL,
    cart_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    street2 VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    state VARCHAR(255) NOT NULL,
    zip_code VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    sub_total DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) NOT NULL,
    grand_total DECIMAL(10,2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    txn_date DATETIME
);