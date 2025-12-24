<?php
require_once 'BaseModel.php';

class Admin extends BaseModel
{
    public function getDashboardStats()
    {
        $stats = [];

        // 1. Total Products
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM products");
        $stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // 2. Total Orders
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // 3. Total Revenue (Sum of all orders)
        $stmt = $this->db->query("SELECT SUM(total_price) as total FROM orders");
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // 4. Total Customers
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
        $stats['total_customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $stats;
    }
}
