<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_client = isset($_POST['code_client']) ? $_POST['code_client'] : '';
    $produit_id  = isset($_POST['produit_id']) ? $_POST['produit_id'] : '';
    $qty         = isset($_POST['qty']) ? $_POST['qty'] : 0;

    if ($code_client && $produit_id && $qty > 0) {

        $stmt = $pdo->prepare("SELECT nom, prix, stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($qty > $prod['stock']) { echo "Stock insuffisant."; exit; }
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
        $file = 'facture/commande_'.date('dmY&His',time()).'.txt';

        $content = "===== FACTURE COMMANDE #".$code." =====\n";
        $content .= "Date: ".date('d/m/Y H:i:s')."\n\n";
        $content .= "CLIENT\n";
        $content .= "Code: ".$code_client."\n";
        $content .= "Nom: ".$client['nom']." ".$client['prenom']."\n";
        $content .= "Email: ".$client['email']."\n\n";
        $content .= "PRODUIT\n";
        $content .= "Nom: ".$prod['nom']."\n";
        $content .= "Prix unitaire: ".$prod['prix']."\n";
        $content .= "Quantité: ".$qty."\n";
        $content .= "--------------------------\n";
        $content .= "Montant total: ".$total."\n";
        $content .= "===========================\n";
        file_put_contents($file, $content);
        echo "Commande ajoutée.";
    } else {
        echo "Tous les champs sont obligatoires et quantité > 0.";
    }
}
$clients = $pdo->query("SELECT code_client, nom, prenom FROM clients ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$produits = $pdo->query("SELECT id, nom, stock FROM produits ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <div class="dropdown">
                <button class="dropbtn">Clients ▼</button>
                <div class="dropdown-content">
                    <a href="clients-liste.php">📋 Liste des clients</a>
                    <a href="clients-ajouter.php">➕ Ajouter un client</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Produits ▼</button>
                <div class="dropdown-content">
                    <a href="produits-liste.php">📋 Liste des produits</a>
                    <a href="produits-ajouter.php">➕ Ajouter un produit</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Commandes ▼</button>
                <div class="dropdown-content">
                    <a href="commandes-liste.php">📋 Liste des commandes</a>
                    <a href="commandes-nouvelle.php">➕ Nouvelle commande</a>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <a href="../deconnexion.php" class="power-btn">⏻ Déconnexion</a>
        </div>
    </nav>

    <div class="content">
        <h2>Ajouter une commande</h2>
        <form method="post">
            <label>Client:</label>
            <select name="code_client" required>
                <option value="">--Sélectionner--</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['code_client'] ?>"><?= $c['nom'] ?> <?= $c['prenom'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Produit:</label>
            <select name="produit_id" required>
                <option value="">--Sélectionner--</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['nom'] ?> (Stock: <?= $p['stock'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <label>Quantité:</label>
            <input type="number" name="qty" min="1" required>
            
            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>