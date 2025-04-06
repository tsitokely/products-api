<?php
require_once 'config.php';
require_once 'products.php';

// Get the URL parts
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Remove base folder name if needed
$api_position = array_search('api', $path_parts);
if ($api_position !== false) {
    $path_parts = array_slice($path_parts, $api_position + 1);
}

// Initialize products controller
$product_api = new ProductAPI();

// Basic routing
$resource = $path_parts[0] ?? '';

// Handle routes
if ($resource === 'products' || $resource === '') {
    // Get by ID: /products/123
    if (isset($path_parts[1]) && is_numeric($path_parts[1])) {
        $product_api->getById(intval($path_parts[1]));
    }
    // Get by brand: /products/brand/apple
    else if (isset($path_parts[1]) && $path_parts[1] === 'brand' && isset($path_parts[2])) {
        $product_api->getByBrand($path_parts[2]);
    }
    // Get by category: /products/category/electronics
    else if (isset($path_parts[1]) && $path_parts[1] === 'category' && isset($path_parts[2])) {
        $product_api->getByCategory($path_parts[2]);
    }
    // Get by price range: /products/price/800/1200
    else if (isset($path_parts[1]) && $path_parts[1] === 'price' && isset($path_parts[2]) && isset($path_parts[3])) {
        $product_api->getByPriceRange(intval($path_parts[2]), intval($path_parts[3]));
    }
    // Get paginated: /products/page/1/10
    else if (isset($path_parts[1]) && $path_parts[1] === 'page' && isset($path_parts[2]) && isset($path_parts[3])) {
        $product_api->getPaginated(intval($path_parts[2]), intval($path_parts[3]));
    }
    // Search products: /products/search/keyword
    else if (isset($path_parts[1]) && $path_parts[1] === 'search' && isset($path_parts[2])) {
        $product_api->search($path_parts[2]);
    }
    // Top rated products: /products/top/5
    else if (isset($path_parts[1]) && $path_parts[1] === 'top' && isset($path_parts[2])) {
        $product_api->getTopRated(intval($path_parts[2]));
    }
    // Get all: /products
    else {
        $product_api->getAll();
    }
} else {
    // Return 404 for invalid endpoints
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}
