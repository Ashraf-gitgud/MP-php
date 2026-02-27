<?php
require '../db/base.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code_client = $_POST['code_client'] ?? '';
    $produit_id  = $_POST['produit_id'] ?? '';
    $qty         = $_POST['qty'] ?? 0;

    if ($code_client && $produit_id && $qty > 0) {

        $stmt = $pdo->prepare("SELECT nom, prix, stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            $message = "Produit introuvable.";
        } elseif ($qty > $prod['stock']) {
            $message = "Stock insuffisant.";
        } else {

            $total = $prod['prix'] * $qty;

            $stmt = $pdo->prepare("
                INSERT INTO commandes (code_client, nom, prix, qty, total)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$code_client, $prod['nom'], $prod['prix'], $qty, $total]);
            $code = $pdo->lastInsertId();

            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$qty, $produit_id]);

            $stmt = $pdo->prepare("SELECT nom, prenom, email FROM clients WHERE code_client = ?");
            $stmt->execute([$code_client]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            $file = 'facture/commande_' . date('dmY&His') . '.txt';

            $content  = "===== FACTURE COMMANDE #" . $code . " =====\n";
            $content .= "Date: " . date('d/m/Y H:i:s') . "\n\n";
            $content .= "CLIENT\n";
            $content .= "Code: " . $code_client . "\n";
            $content .= "Nom: " . $client['nom'] . " " . $client['prenom'] . "\n";
            $content .= "Email: " . $client['email'] . "\n\n";
            $content .= "PRODUIT\n";
            $content .= "Nom: " . $prod['nom'] . "\n";
            $content .= "Prix unitaire: " . $prod['prix'] . "\n";
            $content .= "Quantité: " . $qty . "\n";
            $content .= "--------------------------\n";
            $content .= "Montant total: " . $total . "\n";
            $content .= "===========================\n";

            file_put_contents($file, $content);

            $message = "Commande ajoutée.";
        }

    } else {
        $message = "Tous les champs sont obligatoires et quantité > 0.";
    }
}

$clients = $pdo->query("SELECT code_client, nom, prenom FROM clients ORDER BY nom")
               ->fetchAll(PDO::FETCH_ASSOC);

$produits = $pdo->query("SELECT id, nom, stock FROM produits ORDER BY nom")
                ->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <h1><a href="../index.php" class="header-link">Panneau d'administration</a></h1>
    
    <nav class="navbar">
        <div class="nav-left">
            <div class="dropdown">
                <button class="dropbtn">Clients ▼</button>
                <div class="dropdown-content">
                    <a href="../clients/liste_clients.php"> Liste des clients</a>
                    <a href="../clients/ajouter_client.php"> Ajouter un client</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Produits ▼</button>
                <div class="dropdown-content">
                    <a href="../produits/liste_produits.php">Liste des produits</a>
                    <a href="../produits/ajouter_produit.php"> Ajouter un produit</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Commandes ▼</button>
                <div class="dropdown-content">
                    <a href="liste_commandes.php"> Liste des commandes</a>
                    <a href="ajouter_commande.php"> Nouvelle commande</a>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <a href="../connexion/logout.php" class="power-btn">Déconnexion</a>
        </div>
    </nav>

    <form class="form-card" method="post">
    <h2 class="form-title">Ajouter une commande</h2> 
        <?php if (!empty($message)): ?>
        <div class="form-message"><?= $message ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label>Client:</label>
            <select class="form-input" name="code_client" required>
                <option value="">--Sélectionner--</option>
                <?php foreach ($clients as $c) {
                    echo "<option value=\"{$c['code_client']}\">{$c['nom']} {$c['prenom']}</option>";
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Produit:</label>
            <select class="form-input" name="produit_id" required>
                <option value="">--Sélectionner--</option>
                <?php foreach ($produits as $p) {
                    echo '<option value="'.$p['id'].'">'.$p['nom'].' (Stock: '.$p['stock'].')</option>';
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Quantité:</label>
            <input class="form-input" type="number" name="qty" min="1" required>
        </div>

        <button class="form-btn" type="submit">Ajouter</button>
    </form>

</body>
</html>