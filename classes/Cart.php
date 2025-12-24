<?php
class Cart
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add($product_id, $quantity = 1)
    {
        $product_id = (int) $product_id;
        $quantity = (int) $quantity;

        if ($quantity < 1) {
            $quantity = 1;
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function remove($product_id)
    {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
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
        }
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
    }

    // NEW: Returns total number of items (e.g., for the navbar badge)
    public function getTotalQuantity()
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $qty) {
            $total += $qty;
        }
        return $total;
    }
}
