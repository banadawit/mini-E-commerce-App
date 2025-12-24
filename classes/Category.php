<?php
require_once 'BaseModel.php';

class Category extends BaseModel
{
    public function getAll()
    {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // You can manually insert some categories in PHPMyAdmin 
    // (e.g., Electronics, Fashion, Home) to test.
}
