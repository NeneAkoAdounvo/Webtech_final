<?php
// src/models/User.php
class User {
    private $conn;
    private $table_name = 'users';

    public $id;
    public $username;
    public $email;
    public $role_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all users
    public function readAll() {
        $query = "SELECT id, username, email, role_id FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create user
    public function create($username, $email, $password, $role_id) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username = ?, email = ?, 
                      password = ?, role_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt->bind_param("sssi", $username, $email, $hashed_password, $role_id);
        
        return $stmt->execute();
    }

    // Update user
    public function update($id, $username, $email, $role_id) {
        $query = "UPDATE " . $this->table_name . "
                  SET username = ?, email = ?, role_id = ?
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("ssii", $username, $email, $role_id, $id);
        
        return $stmt->execute();
    }

    // Delete user
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}