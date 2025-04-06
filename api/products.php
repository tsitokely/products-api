<?php
class ProductAPI {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    // Get all products
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM products");
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        echo json_encode($products);
    }
    
    // Get product by ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"]);
            return;
        }
        
        // Add listings
        $product['listings'] = $this->getListingsForProduct($id);
        
        echo json_encode($product);
    }
    
    // Get products by brand
    public function getByBrand($brand) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE brand LIKE :brand");
        $stmt->execute([':brand' => '%' . $brand . '%']);
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        echo json_encode($products);
    }
    
    // Get products by category
    public function getByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE category LIKE :category");
        $stmt->execute([':category' => '%' . $category . '%']);
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        echo json_encode($products);
    }
    
    // Get products by price range
    public function getByPriceRange($min, $max) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE minPrice BETWEEN :min AND :max");
        $stmt->execute([':min' => $min, ':max' => $max]);
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        echo json_encode($products);
    }
    
    // Get paginated results
    public function getPaginated($page, $limit) {
        // Count total products
        $stmt_count = $this->db->query("SELECT COUNT(*) FROM products");
        $total = $stmt_count->fetchColumn();
        
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get paginated data
        $stmt = $this->db->prepare("SELECT * FROM products LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        // Create result structure
        $results = [
            'total' => $total,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page,
            'products' => $products
        ];
        
        if ($offset + $limit < $total) {
            $results['next'] = [
                'page' => $page + 1,
                'limit' => $limit
            ];
        }
        
        if ($offset > 0) {
            $results['previous'] = [
                'page' => $page - 1,
                'limit' => $limit
            ];
        }
        
        echo json_encode($results);
    }
    
    // Search products by keyword (in name or description)
    public function search($keyword) {
        $stmt = $this->db->prepare("
            SELECT * FROM products 
            WHERE name LIKE :keyword 
            OR description LIKE :keyword
        ");
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        echo json_encode($products);
    }
    
    // Get top rated products
    public function getTopRated($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT * FROM products 
            ORDER BY rating DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        // Add listings to each product
        foreach ($products as &$product) {
            $product['listings'] = $this->getListingsForProduct($product['id']);
        }
        
        echo json_encode($products);
    }
    
    // Helper method to get listings for a product
    private function getListingsForProduct($product_id) {
        $stmt = $this->db->prepare("SELECT imageUrl, vendor, price, link, details FROM listings WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $product_id]);
        return $stmt->fetchAll();
    }
}
