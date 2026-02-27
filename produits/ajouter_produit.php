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
            echo "Produit ajouté.";
        } catch (PDOException $e) {
            echo "Erreur: ".$e->getMessage();
        }
    } else {
        echo "Remplissez les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
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
        <h2>Ajouter un produit</h2>
        <form method="post">
            <label>Nom:</label>
            <input type="text" name="nom" required>
            
            <label>Description:</label>
            <textarea name="descr"></textarea>
            
            <label>Prix:</label>
            <input type="number" step="0.01" name="prix" required>
            
            <label>Stock:</label>
            <input type="number" name="stock" required>
            
            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>