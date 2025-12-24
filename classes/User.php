<?php
require_once 'BaseModel.php';

class User extends BaseModel
{
    private $table = "users";

    public function register($name, $email, $password)
    {
        try {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO " . $this->table . " (name, email, password, role) VALUES (:name, :email, :password, 'customer')";
            $stmt = $this->db->prepare($query);

            // Sanitize inputs (basic layer)
            $name = htmlspecialchars(strip_tags($name));
            $email = htmlspecialchars(strip_tags($email));

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            // If the error is a Duplicate Entry (Error code 23000), return false
            if ($e->getCode() == 23000) {
                return false;
            }
            // For other errors, you might want to log them
            return false;
        }
    }

    public function login($email, $password)
    {
        $query = "SELECT id, name, email, password, role FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                // Password is correct, return user data
                return $row;
            }
        }
        return false;
    }

    public function emailExists($email)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
