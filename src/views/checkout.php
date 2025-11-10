<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/home">
                <i class="bi bi-cup-hot-fill"></i> Coffee Shop
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1><i class="bi bi-credit-card"></i> Finalizar Compra</h1>
        
        <!-- Mensajes -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulario de Cliente -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-person"></i> Información de Contacto</h5>
                        <form method="POST" action="/cart/process-order">
                            <div class="mb-3">
                                <label class="form-label">Nombre completo *</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="customer_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono *</label>
                                <input type="tel" name="customer_phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dirección de entrega *</label>
                                <textarea name="delivery_address" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-check-circle"></i> Confirmar Pedido
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Resumen del Pedido -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-receipt"></i> Resumen del Pedido</h5>
                        <hr>
                        
                        <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                <?php echo $item['name']; ?> 
                                <small class="text-muted">x<?php echo $item['quantity']; ?></small>
                            </span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="h5 text-success">$<?php echo number_format($cartTotal, 2); ?></strong>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                <strong>Compra como invitado:</strong> No necesitas crear una cuenta. 
                                Te contactaremos para confirmar tu pedido.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>