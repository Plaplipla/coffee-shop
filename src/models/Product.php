<?php
require_once __DIR__ . '/../core/Database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->find('products', ['active' => true]);
    }
    
    public function findById($id) {
        return $this->db->findOne('products', ['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
    
    public function create($data) {
        $data['active'] = true;
        $data['created_at'] = new MongoDB\BSON\UTCDateTime();
        return $this->db->insert('products', $data);
    }
    
    // NUEVO MÉTODO: Obtener producto por ID (compatible con string)
    public function getProductById($id) {
        // Si es string, convertir a ObjectId
        if (is_string($id)) {
            try {
                $id = new MongoDB\BSON\ObjectId($id);
            } catch (Exception $e) {
                return null;
            }
        }
        
        return $this->db->findOne('products', ['_id' => $id]);
    }
}
?>