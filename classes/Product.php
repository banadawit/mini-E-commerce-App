<?php
require_once 'BaseModel.php';

class Product extends BaseModel
{
    private $table = "products";

    public function create($name, $description, $price, $stock, $category_id, $image)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, price, stock, category_id, image) 
                  VALUES (:name, :description, :price, :stock, :category_id, :image)";

        $stmt = $this->db->prepare($query);

        // Basic sanitization
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);

        return $stmt->execute();
    }

    // UPDATED: Simple getAll for Admin usage (lists everything)
    public function getAll()
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NEW: For Frontend - Supports Search, Pagination, and Sorting
    public function getProducts($search = "", $limit = 6, $offset = 0, $category_id = null, $sort = 'newest')
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE (p.name LIKE :search OR p.description LIKE :search)";
        
        // Add category filter if specified
        if ($category_id) {
            $query .= " AND p.category_id = :category_id";
        }
        
        // Add sorting
        switch ($sort) {
            case 'price_asc':
                $query .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $query .= " ORDER BY p.price DESC";
                break;
            case 'popular':
                $query .= " ORDER BY p.popularity DESC";
                break;
            case 'newest':
            default:
                $query .= " ORDER BY p.created_at DESC";
        }
        
        // Add pagination
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);

        $searchTerm = "%{$search}%";
        $stmt->bindParam(':search', $searchTerm);
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NEW: Helper to count products for pagination links
    public function countProducts($search = "", $category_id = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE (name LIKE :search OR description LIKE :search)";
        
        if ($category_id) {
            $query .= " AND category_id = :category_id";
        }
        
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$search}%";
        $stmt->bindParam(':search', $searchTerm);
        
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getById($id)
    {
        // Added join here too, in case you want to show Category Name on the detail page
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function update($id, $name, $description, $price, $stock, $category_id, $image = null)
    {
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        if ($image) {
            $query = "UPDATE " . $this->table . " 
                      SET name=:name, description=:description, price=:price, stock=:stock, category_id=:category_id, image=:image 
                      WHERE id=:id";
        } else {
            $query = "UPDATE " . $this->table . " 
                      SET name=:name, description=:description, price=:price, stock=:stock, category_id=:category_id 
                      WHERE id=:id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':id', $id);
        if ($image) $stmt->bindParam(':image', $image);

        return $stmt->execute();
    }
}
