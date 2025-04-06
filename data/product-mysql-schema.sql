-- Create database
CREATE DATABASE product_db;
USE product_db;

-- Create products table
CREATE TABLE products (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    minPrice DECIMAL(10,2) NOT NULL,
    category VARCHAR(100),
    stock INT DEFAULT 0,
    imageUrl VARCHAR(255),
    brand VARCHAR(100),
    rating DECIMAL(3,1),
    reviews INT DEFAULT 0,
    releaseDate DATE
);

-- Create listings table (for the nested listings data)
CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    imageUrl VARCHAR(255),
    vendor VARCHAR(100),
    price DECIMAL(10,2),
    link VARCHAR(255),
    details TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
