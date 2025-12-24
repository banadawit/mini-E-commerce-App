<?php
require_once 'BaseModel.php';

class Order extends BaseModel {
    
    public function createOrder($user_id, $total_price, $cart_items) {
        try {
            $this->db->beginTransaction();

            // 1. Insert into orders table
            $query = "INSERT INTO orders (user_id, total_price, status) VALUES (:user_id, :total, 'Pending')";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $user_id, 'total' => $total_price]);
            $order_id = $this->db->lastInsertId();

            // 2. Insert into order_items and Reduce Stock
            foreach ($cart_items as $product_id => $quantity) {
                // Get current price
                $stmt = $this->db->prepare("SELECT price, stock FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                // Insert Item
                $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $product_id, $quantity, $product['price']]);

                // Reduce Stock (Requirement 2.7)
                $new_stock = $product['stock'] - $quantity;
                $stmt = $this->db->prepare("UPDATE products SET stock = ? WHERE id = ?");
                $stmt->execute([$new_stock, $product_id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAllOrders() {
        $query = "SELECT o.*, u.name as customer_name FROM orders o 
                  JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByUser($user_id) {
        $query = "SELECT o.*,
                         COUNT(oi.id) as items_count,
                         GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_names
                  FROM orders o
                  LEFT JOIN order_items oi ON o.id = oi.order_id
                  LEFT JOIN products p ON oi.product_id = p.id
                  WHERE o.user_id = :user_id
                  GROUP BY o.id
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>