<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        .product-icon {
            font-size: 3rem;
            color: #8B4513;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/home">
                <i class="bi bi-cup-hot-fill"></i> Coffee Shop
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link" href="/home">Inicio</a>
                <a class="nav-link active" href="/cart">
                    <i class="bi bi-cart3"></i> Carrito
                    <?php 
                    $itemCount = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $itemCount += $item['quantity'];
                        }
                    }
                    if ($itemCount > 0): ?>
                    <span class="badge bg-danger"><?php echo $itemCount; ?></span>
                    <?php endif; ?>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="/logout">Cerrar Sesión</a>
                <?php else: ?>
                    <a class="nav-link" href="/login">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1><i class="bi bi-cart3"></i> Tu Carrito de Compras</h1>
        
        <!-- Mensajes -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php
        // Calcular totales desde la sesión
        $cartItems = $_SESSION['cart'] ?? [];
        $cartTotal = 0;
        $itemCount = 0;
        
        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];
        }
        ?>

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-cart-x product-icon"></i>
                <h4 class="mt-3">Tu carrito está vacío</h4>
                <p>¡Descubre nuestros deliciosos cafés!</p>
                <a href="/home" class="btn btn-primary">
                    <i class="bi bi-cup-hot"></i> Ir a Comprar
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo $item['image']; ?>" 
                                             alt="<?php echo $item['name']; ?>" 
                                             class="cart-item-image">
                                    <?php else: ?>
                                        <i class="<?php echo $item['icon'] ?? 'bi bi-cup-hot'; ?> product-icon"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="text-muted mb-0 small"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <span class="h6">$<?php echo number_format($item['price'], 2); ?></span>
                                    <small class="text-muted d-block">c/u</small>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" action="/cart/update-quantity" class="d-flex align-items-center">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <input type="number" name="quantity" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="10" 
                                               class="form-control quantity-input">
                                        <button type="submit" class="btn btn-outline-primary btn-sm ms-2" title="Actualizar">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong class="h6">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                    <form method="POST" action="/cart/remove" class="mt-2">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Resumen del Carrito -->
                <div class="col-md-4">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-receipt"></i> Resumen de Compra</h5>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Productos:</span>
                                <span><?php echo $itemCount; ?> items</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="h5 text-success">$<?php echo number_format($cartTotal, 2); ?></strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="/checkout" class="btn btn-success btn-lg">
                                    <i class="bi bi-credit-card"></i> Proceder al Pago
                                </a>
                                
                                <form method="POST" action="/cart/clear">
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Vaciar Carrito
                                    </button>
                                </form>
                                
                                <a href="/home" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left"></i> Seguir Comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>