<?php
require_once 'BaseModel.php';

class Cart extends BaseModel
{
    private $userId;

    public function __construct()
    {
        parent::__construct();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->userId = $_SESSION['user_id'] ?? null;
        
        // Initialize session cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // If user is logged in and session cart is empty, load from database
        // This handles page refreshes and new sessions for logged-in users
        if ($this->userId && empty($_SESSION['cart'])) {
            $this->loadUserCart();
        }
    }

    public function add($product_id, $quantity = 1)
    {
        $product_id = (int) $product_id;
        $quantity = (int) $quantity;

        if ($quantity < 1) {
            $quantity = 1;
        }

        // Update session cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        // If user is logged in, update database
        if ($this->userId) {
            $this->updateDatabaseCart($product_id, $_SESSION['cart'][$product_id]);
        }
    }

    public function remove($product_id)
    {
        $product_id = (int) $product_id;
        
        // Remove from session
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        // If user is logged in, remove from database
        if ($this->userId) {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$this->userId, $product_id]);
        }
    }

    public function getItems()
    {
        return $_SESSION['cart'];
    }

    public function update($product_id, $quantity)
    {
        $product_id = (int) $product_id;
        $quantity = (int) $quantity;

        if ($quantity <= 0) {
            $this->remove($product_id);
            return;
        }

        // Only update if the item is actually in the cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = $quantity;
            
            // If user is logged in, update database
            if ($this->userId) {
                $this->updateDatabaseCart($product_id, $quantity);
            }
        }
    }
    
    private function updateDatabaseCart($product_id, $quantity)
    {
        try {
            $stmt = $this->db->prepare("REPLACE INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$this->userId, $product_id, $quantity]);
        } catch (Exception $e) {
            error_log("Error updating cart in database: " . $e->getMessage());
        }
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
        
        // If user is logged in, clear from database
        if ($this->userId) {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$this->userId]);
        }
    }

    // Returns total number of items (e.g., for the navbar badge)
    public function getTotalQuantity()
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $qty) {
            $total += $qty;
        }
        return $total;
    }
    
    // Load user's cart from database (called after login or when session cart is empty)
    public function loadUserCart()
    {
        // Ensure userId is set (in case this is called manually)
        if (!$this->userId) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $this->userId = $_SESSION['user_id'] ?? null;
        }
        
        if (!$this->userId) return;
        
        $stmt = $this->db->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        
        // Reset session cart
        $_SESSION['cart'] = [];
        
        // Load items from database
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['cart'][$row['product_id']] = $row['quantity'];
        }
    }
}
