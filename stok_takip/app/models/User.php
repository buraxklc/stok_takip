<?php
// app/models/User.php

class User extends Model {
    protected $table = 'users';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function findByUsername($username) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE username = ? LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $username);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function findByEmail($email) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['username']);
            $stmt->bindParam(2, $data['password']);
            $stmt->bindParam(3, $data['name']);
            $stmt->bindParam(4, $data['email']);
            $stmt->bindParam(5, $data['role']);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE " . $this->table . " SET username = ?, name = ?, email = ?, role = ?, status = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['username']);
            $stmt->bindParam(2, $data['name']);
            $stmt->bindParam(3, $data['email']);
            $stmt->bindParam(4, $data['role']);
            $stmt->bindParam(5, $data['status']);
            $stmt->bindParam(6, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function updatePassword($id, $password) {
        try {
            $query = "UPDATE " . $this->table . " SET password = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            // Şifreyi hashle
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt->bindParam(1, $password_hash);
            $stmt->bindParam(2, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getAll() {
        try {
            $query = "SELECT id, username, name, email, role, status, created_at FROM " . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT id, username, name, email, role, status, created_at FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}