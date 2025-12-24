<?php
require_once 'User.php';

// Inheritance: Admin inherits all methods and properties from User
class Admin extends User
{

    /**
     * Get summary statistics for the Admin Dashboard.
     * This demonstrates specialized business logic only an admin can access.
     */
    public function getDashboardStats()
    {
        $stats = [];

        // 1. Get total number of products
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products");
        $stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Get total number of orders
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Get total revenue
        $stmt = $this->db->query("SELECT SUM(total_price) as total FROM orders");
        $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['total_revenue'] = $revenue ? $revenue : 0;

        // 4. Get total registered customers
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
        $stats['total_customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    /**
     * Admin can update order status (Pending, Shipped, Delivered)
     */
    public function updateOrderStatus($order_id, $status)
    {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $order_id);
        return $stmt->execute();
    }
}
