<?php
// Database connection
$db_host = 'localhost';
$db_name = 'product_db';
$db_user = 'root';  // Change to your DB username
$db_pass = '';      // Change to your DB password

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load JSON data
    $json_data = file_get_contents('../data/products.json');
    $products = json_decode($json_data, true);
    
    if (!$products) {
        die("Error parsing JSON file.");
    }
    
    // Begin transaction for better performance and consistency
    $db->beginTransaction();
    
    // Prepare statements
    $stmt_product = $db->prepare("
        INSERT INTO products (id, name, description, minPrice, category, stock, imageUrl, brand, rating, reviews, releaseDate)
        VALUES (:id, :name, :description, :minPrice, :category, :stock, :imageUrl, :brand, :rating, :reviews, :releaseDate)
    ");
    
    $stmt_listing = $db->prepare("
        INSERT INTO listings (product_id, imageUrl, vendor, price, link, details)
        VALUES (:product_id, :imageUrl, :vendor, :price, :link, :details)
    ");
    
    // Insert data
    foreach ($products as $product) {
        $stmt_product->execute([
            ':id' => $product['id'],
            ':name' => $product['name'],
            ':description' => $product['description'],
            ':minPrice' => $product['minPrice'],
            ':category' => $product['category'],
            ':stock' => $product['stock'],
            ':imageUrl' => $product['imageUrl'],
            ':brand' => $product['brand'],
            ':rating' => $product['rating'],
            ':reviews' => $product['reviews'],
            ':releaseDate' => $product['releaseDate']
        ]);
        
        // Insert listings for this product
        if (isset($product['listings']) && is_array($product['listings'])) {
            foreach ($product['listings'] as $listing) {
                $stmt_listing->execute([
                    ':product_id' => $product['id'],
                    ':imageUrl' => $listing['imageUrl'],
                    ':vendor' => $listing['vendor'],
                    ':price' => $listing['price'],
                    ':link' => $listing['link'],
                    ':details' => $listing['details']
                ]);
            }
        }
    }
    
    // Commit transaction
    $db->commit();
    
    echo "Successfully imported " . count($products) . " products to database.";
    
} catch (PDOException $e) {
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    die("Database error: " . $e->getMessage());
}
