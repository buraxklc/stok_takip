<?php
// app/core/Model.php
require_once 'app/config/database.php';

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Diğer metodlar...
}