<?php
require_once __DIR__ . '/../models/Product.php';

class CartController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $quantity = intval($_POST['quantity'] ?? 1);
            
            // Verificar que el producto existe
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                $_SESSION['error'] = 'Producto no encontrado';
                header('Location: /home');
                exit;
            }
            
            // Agregar al carrito de sesión (sin login requerido)
            $this->addToSessionCart($product, $quantity);
            $_SESSION['success'] = '✅ Producto agregado al carrito';
            
            header('Location: ' . ($_POST['return_url'] ?? '/home'));
            exit;
        }
    }
    
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $this->removeFromSessionCart($productId);
            $_SESSION['success'] = '🗑️ Producto eliminado del carrito';
            
            header('Location: /cart');
            exit;
        }
    }
    
    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $quantity = intval($_POST['quantity']);
            
            $this->updateSessionCartQuantity($productId, $quantity);
            
            header('Location: /cart');
            exit;
        }
    }
    
    public function view() {
        // Mostrar carrito sin requerir login
        $cartItems = $this->getSessionCartItems();
        $cartTotal = $this->getSessionCartTotal();
        $itemCount = $this->getSessionCartItemCount();
        
        require __DIR__ . '/../views/cart.php';
    }
    
    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->clearSessionCart();
            $_SESSION['success'] = '🧹 Carrito vaciado';
            
            header('Location: /cart');
            exit;
        }
    }
    
    public function checkout() {
        // Checkout para usuarios invitados - SIN login
        $cartItems = $this->getSessionCartItems();
        
        if (empty($cartItems)) {
            $_SESSION['error'] = 'Tu carrito está vacío';
            header('Location: /cart');
            exit;
        }
        
        // Mostrar página de checkout para invitados
        require __DIR__ . '/../views/checkout.php';
    }
    
    public function processOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartItems = $this->getSessionCartItems();
            
            if (empty($cartItems)) {
                $_SESSION['error'] = 'Tu carrito está vacío';
                header('Location: /cart');
                exit;
            }
            
            // Procesar pedido como invitado
            $orderData = [
                'customer_name' => $_POST['customer_name'],
                'customer_email' => $_POST['customer_email'],
                'customer_phone' => $_POST['customer_phone'],
                'delivery_address' => $_POST['delivery_address'],
                'items' => $cartItems,
                'total' => $this->getSessionCartTotal(),
                'order_date' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];
            
            // Limpiar carrito después del pedido
            $this->clearSessionCart();
            
            $_SESSION['success'] = '🎉 ¡Pedido realizado con éxito! Te contactaremos pronto.';
            header('Location: /home');
            exit;
        }
    }
    
    // ==================== MÉTODOS DE SESIÓN ====================
    
    private function initSessionCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    private function addToSessionCart($product, $quantity = 1) {
        $this->initSessionCart();
        
        // 🛒 CORRECCIÓN: Manejar tanto objetos como arrays
        $productId = (string)$product->_id; // Para objetos stdClass
        $productName = $product->name ?? ''; // Para objetos stdClass
        $productPrice = $product->price ?? 0; // Para objetos stdClass
        $productImage = $product->image ?? ''; // Para objetos stdClass
        $productDescription = $product->description ?? ''; // Para objetos stdClass
        $productIcon = $product->icon ?? 'bi bi-cup-hot'; // Para objetos stdClass
        
        // Si es array (fallback)
        if (is_array($product)) {
            $productId = (string)$product['_id'];
            $productName = $product['name'] ?? '';
            $productPrice = $product['price'] ?? 0;
            $productImage = $product['image'] ?? '';
            $productDescription = $product['description'] ?? '';
            $productIcon = $product['icon'] ?? 'bi bi-cup-hot';
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            // Incrementar cantidad si ya existe
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            // Agregar nuevo producto al carrito
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'name' => $productName,
                'price' => $productPrice,
                'quantity' => $quantity,
                'image' => $productImage,
                'description' => $productDescription,
                'icon' => $productIcon
            ];
        }
    }
    
    private function removeFromSessionCart($productId) {
        $this->initSessionCart();
        unset($_SESSION['cart'][$productId]);
    }
    
    private function updateSessionCartQuantity($productId, $quantity) {
        $this->initSessionCart();
        
        if ($quantity <= 0) {
            $this->removeFromSessionCart($productId);
            return;
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }
    
    private function getSessionCartItems() {
        $this->initSessionCart();
        return $_SESSION['cart'];
    }
    
    private function getSessionCartTotal() {
        $items = $this->getSessionCartItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    private function getSessionCartItemCount() {
        $items = $this->getSessionCartItems();
        $count = 0;
        
        foreach ($items as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }
    
    private function clearSessionCart() {
        $_SESSION['cart'] = [];
    }
}
?>