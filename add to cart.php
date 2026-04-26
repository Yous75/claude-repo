<?php
session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$product_id   = (int) $_POST['product_id'];
$product_nom  = $_POST['product_nom'];
$product_prix = (float) $_POST['product_prix'];
$redirect_to  = $_POST['redirect_to'];

// Initialize cart if it does not exist
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// If product already in cart, increase quantity
if (isset($_SESSION['panier'][$product_id])) {
    $_SESSION['panier'][$product_id]['quantite']++;
} else {
    // Add new product to cart
    $_SESSION['panier'][$product_id] = array(
        'id'       => $product_id,
        'nom'      => $product_nom,
        'prix'     => $product_prix,
        'quantite' => 1
    );
}

$_SESSION['message'] = '"' . $product_nom . '" added to cart!';

// Go back to the page where user clicked Add to Cart
header('Location: ' . $redirect_to);
exit;
?>
