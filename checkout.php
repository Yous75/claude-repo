<?php
include('config.php');
include('header.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Cart must not be empty
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

$error   = '';
$success = '';

// Calculate cart total
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize form data
    $nom_client    = mysqli_real_escape_string($conn, $_POST['nom_client']);
    $prenom_client = mysqli_real_escape_string($conn, $_POST['prenom_client']);
    $email_client  = mysqli_real_escape_string($conn, $_POST['email_client']);
    $telephone     = mysqli_real_escape_string($conn, $_POST['telephone']);
    $adresse       = mysqli_real_escape_string($conn, $_POST['adresse']);
    $ville         = mysqli_real_escape_string($conn, $_POST['ville']);
    $wilaya        = mysqli_real_escape_string($conn, $_POST['wilaya']);
    $user_id       = (int) $_SESSION['user_id'];

    // Validation
    if (empty($nom_client) || empty($prenom_client) || empty($email_client) || empty($adresse) || empty($ville) || empty($wilaya)) {
        $error = 'Please fill in all required fields.';
    } else {

        // 1. Insert the order into orders table
        $insert_order = "INSERT INTO orders (user_id, nom_client, prenom_client, email_client, telephone, adresse, ville, wilaya, total)
                         VALUES ($user_id, '$nom_client', '$prenom_client', '$email_client', '$telephone', '$adresse', '$ville', '$wilaya', $total)";

        mysqli_query($conn, $insert_order);
        $order_id = mysqli_insert_id($conn);

        // 2. Insert each cart item into order_items table
        foreach ($_SESSION['panier'] as $item) {
            $pid  = (int) $item['id'];
            $nom  = mysqli_real_escape_string($conn, $item['nom']);
            $prix = (float) $item['prix'];
            $qty  = (int) $item['quantite'];

            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, nom, prix, quantite)
                                 VALUES ($order_id, $pid, '$nom', $prix, $qty)");

            // 3. Decrease stock
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = $pid AND stock >= $qty");
        }

        // 4. Clear the session cart
        $_SESSION['panier'] = array();
        $_SESSION['message'] = 'Your order has been placed successfully! Order #' . $order_id;

        header('Location: order_success.php?id=' . $order_id);
        exit;
    }
}
?>

<div class="page-wrapper">

    <div class="page-top">
        <h1>Checkout</h1>
    </div>

    <?php if ($error): ?>
        <p class="msg-error"><?php echo $error; ?></p>
    <?php endif; ?>

    <div class="checkout-layout">

        <!-- LEFT: Shipping Form -->
        <div class="checkout-form">

            <h2 class="form-section-title">Shipping Information</h2>

            <form action="checkout.php" method="POST">

                <!-- Name fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom_client">First Name</label>
                        <input type="text" id="prenom_client" name="prenom_client"
                               placeholder="Sophie"
                               value="<?php echo htmlspecialchars($_SESSION['user_prenom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nom_client">Last Name</label>
                        <input type="text" id="nom_client" name="nom_client"
                               placeholder="Martin"
                               value="<?php echo htmlspecialchars($_SESSION['user_nom']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email_client">Email Address</label>
                    <input type="email" id="email_client" name="email_client"
                           placeholder="your@email.com"
                           value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Phone Number</label>
                    <input type="text" id="telephone" name="telephone" placeholder="+213 000 000 000">
                </div>

                <div class="form-group">
                    <label for="adresse">Street Address</label>
                    <input type="text" id="adresse" name="adresse" placeholder="123 Rue des Fleurs" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ville">City</label>
                        <input type="text" id="ville" name="ville" placeholder="Algiers" required>
                    </div>
                    <div class="form-group">
                        <label for="wilaya">Wilaya</label>
                        <select id="wilaya" name="wilaya" required>
                            <option value="">Select Wilaya</option>
                            <option value="Adrar">01 - Adrar</option>
                            <option value="Chlef">02 - Chlef</option>
                            <option value="Laghouat">03 - Laghouat</option>
                            <option value="Oum El Bouaghi">04 - Oum El Bouaghi</option>
                            <option value="Batna">05 - Batna</option>
                            <option value="Bejaia">06 - Bejaia</option>
                            <option value="Biskra">07 - Biskra</option>
                            <option value="Bechar">08 - Bechar</option>
                            <option value="Blida">09 - Blida</option>
                            <option value="Bouira">10 - Bouira</option>
                            <option value="Tamanrasset">11 - Tamanrasset</option>
                            <option value="Tebessa">12 - Tebessa</option>
                            <option value="Tlemcen">13 - Tlemcen</option>
                            <option value="Tiaret">14 - Tiaret</option>
                            <option value="Tizi Ouzou">15 - Tizi Ouzou</option>
                            <option value="Alger">16 - Alger</option>
                            <option value="Djelfa">17 - Djelfa</option>
                            <option value="Jijel">18 - Jijel</option>
                            <option value="Setif">19 - Setif</option>
                            <option value="Saida">20 - Saida</option>
                            <option value="Skikda">21 - Skikda</option>
                            <option value="Sidi Bel Abbes">22 - Sidi Bel Abbes</option>
                            <option value="Annaba">23 - Annaba</option>
                            <option value="Guelma">24 - Guelma</option>
                            <option value="Constantine">25 - Constantine</option>
                            <option value="Medea">26 - Medea</option>
                            <option value="Mostaganem">27 - Mostaganem</option>
                            <option value="MSila">28 - M'Sila</option>
                            <option value="Mascara">29 - Mascara</option>
                            <option value="Ouargla">30 - Ouargla</option>
                            <option value="Oran">31 - Oran</option>
                            <option value="El Bayadh">32 - El Bayadh</option>
                            <option value="Illizi">33 - Illizi</option>
                            <option value="Bordj Bou Arreridj">34 - Bordj Bou Arreridj</option>
                            <option value="Boumerdes">35 - Boumerdes</option>
                            <option value="El Tarf">36 - El Tarf</option>
                            <option value="Tindouf">37 - Tindouf</option>
                            <option value="Tissemsilt">38 - Tissemsilt</option>
                            <option value="El Oued">39 - El Oued</option>
                            <option value="Khenchela">40 - Khenchela</option>
                            <option value="Souk Ahras">41 - Souk Ahras</option>
                            <option value="Tipaza">42 - Tipaza</option>
                            <option value="Mila">43 - Mila</option>
                            <option value="Ain Defla">44 - Ain Defla</option>
                            <option value="Naama">45 - Naama</option>
                            <option value="Ain Temouchent">46 - Ain Temouchent</option>
                            <option value="Ghardaia">47 - Ghardaia</option>
                            <option value="Relizane">48 - Relizane</option>
                        </select>
                    </div>
                </div>

                <!-- Payment method (cash on delivery only for now) -->
                <h2 class="form-section-title" style="margin-top:25px;">Payment Method</h2>

                <div class="payment-box">
                    <input type="radio" name="paiement" value="cash" checked>
                    Cash on Delivery
                </div>
                <div class="payment-box">
                    <input type="radio" name="paiement" value="ccp">
                    CCP Transfer
                </div>

                <button type="submit" class="btn-submit" style="margin-top:20px;">Place Order</button>

            </form>

        </div>

        <!-- RIGHT: Order Summary -->
        <div class="cart-summary">
            <h2>Your Order</h2>

            <?php foreach ($_SESSION['panier'] as $item): ?>
            <div class="checkout-item">
                <p><?php echo htmlspecialchars($item['nom']); ?> x<?php echo $item['quantite']; ?></p>
                <p><?php echo number_format($item['prix'] * $item['quantite'], 2); ?> DA</p>
            </div>
            <?php endforeach; ?>

            <div class="summary-line" style="margin-top:15px;">
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

            <p class="safe-msg">Secure & Safe Payment</p>
        </div>

    </div>

</div>

<?php include('footer.php'); ?>
