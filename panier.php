<?php
include('config.php');
include('header.php');

// Initialize cart
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Handle remove item
if (isset($_GET['remove'])) {
    $remove_id = (int) $_GET['remove'];
    unset($_SESSION['panier'][$remove_id]);
    header('Location: panier.php');
    exit;
}

// Handle clear cart
if (isset($_GET['clear'])) {
    $_SESSION['panier'] = array();
    header('Location: panier.php');
    exit;
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $pid = (int) $_POST['product_id'];
    $qty = (int) $_POST['quantite'];

    if ($qty <= 0) {
        unset($_SESSION['panier'][$pid]);
    } else {
        $_SESSION['panier'][$pid]['quantite'] = $qty;
    }
    header('Location: panier.php');
    exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}
?>

<div class="page-wrapper">

    <div class="page-top">
        <h1>My Cart</h1>
    </div>

    <?php if (empty($_SESSION['panier'])): ?>

        <!-- Empty cart message -->
        <div class="cart-empty">
            <p>Your cart is empty.</p>
            <a href="index.php" class="btn-submit">Continue Shopping</a>
        </div>

    <?php else: ?>

        <div class="cart-layout">

            <!-- LEFT: Cart Items -->
            <div class="cart-items">

                <?php foreach ($_SESSION['panier'] as $id => $item): ?>

                <div class="cart-item">

                    <!-- Product name and price -->
                    <div class="cart-item-info">
                        <p class="cart-item-name"><?php echo htmlspecialchars($item['nom']); ?></p>
                        <p class="cart-item-price"><?php echo number_format($item['prix'], 2); ?> DA each</p>
                    </div>

                    <!-- Quantity update form -->
                    <form action="panier.php" method="POST" class="qty-form">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <input type="hidden" name="update_qty" value="1">
                        <button type="submit" name="quantite" value="<?php echo $item['quantite'] - 1; ?>" class="qty-btn">-</button>
                        <b class="qty-num"><?php echo $item['quantite']; ?></b>
                        <button type="submit" name="quantite" value="<?php echo $item['quantite'] + 1; ?>" class="qty-btn">+</button>
                    </form>

                    <!-- Line total -->
                    <p class="cart-item-total"><?php echo number_format($item['prix'] * $item['quantite'], 2); ?> DA</p>

                    <!-- Remove link -->
                    <a href="panier.php?remove=<?php echo $id; ?>" class="btn-remove">Remove</a>

                </div>

                <?php endforeach; ?>

                <!-- Clear cart and continue links -->
                <div class="cart-actions">
                    <a href="panier.php?clear=1" class="btn-clear">Clear Cart</a>
                    <a href="index.php" class="btn-continue">Continue Shopping</a>
                </div>

            </div>

            <!-- RIGHT: Order Summary -->
            <div class="cart-summary">
                <h2>Order Summary</h2>

                <div class="summary-line">
                    <p>Subtotal</p>
                    <p><?php echo number_format($total, 2); ?> DA</p>
                </div>
                <div class="summary-line">
                    <p>Shipping</p>
                    <p class="free">Free</p>
                </div>
                <div class="summary-line summary-total">
                    <p>Total</p>
                    <p><?php echo number_format($total, 2); ?> DA</p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                <?php else: ?>
                    <a href="connexion.php" class="btn-checkout">Sign In to Checkout</a>
                    <p class="auth-footer" style="margin-top:10px;">
                        Or <a href="inscription.php">create an account</a>
                    </p>
                <?php endif; ?>

                <p class="safe-msg">Secure & Safe Payment</p>
            </div>

        </div>

    <?php endif; ?>

</div>

<?php include('footer.php'); ?>
