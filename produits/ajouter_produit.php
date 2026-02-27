<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $descr = isset($_POST['descr']) ? trim($_POST['descr']) : '';
    $prix  = isset($_POST['prix']) ? trim($_POST['prix']) : '';
    $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';

    if ($nom && $prix && $stock !== '') {
        $stmt = $pdo->prepare("
            INSERT INTO produits (nom, descr, prix, stock)
            VALUES (:nom, :descr, :prix, :stock)
        ");
        try {
            $stmt->execute([
                ':nom'   => $nom,
                ':descr' => $descr,
                ':prix'  => $prix,
                ':stock' => $stock
            ]);
            $message = "Produit ajouté.";
        } catch (PDOException $e) {
            $message = "Un erreur est survenue";
        }
    } else {
        $message = "Remplissez les champs obligatoires.";
    }
}
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
                    <a href="liste_produits.php">Liste des produits</a>
                    <a href="ajouter_produit.php"> Ajouter un produit</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Commandes ▼</button>
                <div class="dropdown-content">
                    <a href="../commandes/liste_commandes.php"> Liste des commandes</a>
                    <a href="../commandes/ajouter_commande.php"> Nouvelle commande</a>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <a href="../connexion/logout.php" class="power-btn">Déconnexion</a>
        </div>
    </nav>
    <form class="form-card" method="post">
    <h2 class="form-title">Ajouter un produit</h2>
    <?php if (!empty($message)): ?>
    <div class="form-message"><?= $message ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label>Nom:</label>
        <input class="form-input" type="text" name="nom" required>
    </div>
    <div class="form-group">
        <label>Description:</label>
        <textarea class="form-input" name="descr" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label>Prix:</label>
        <input class="form-input" type="number" step="0.01" name="prix" required>
    </div>
    <div class="form-group">
        <label>Stock:</label>
        <input class="form-input" type="number" name="stock" required>
    </div>
    <button class="form-btn" type="submit">Ajouter</button>
</form>
