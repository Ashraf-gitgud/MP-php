<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (!$id) { echo "Produit non spécifié."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $descr = isset($_POST['descr']) ? trim($_POST['descr']) : '';
    $prix  = isset($_POST['prix']) ? trim($_POST['prix']) : '';
    $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';

    if ($nom && $prix && $stock !== '') {
        $stmt = $pdo->prepare("
            UPDATE produits 
            SET nom = ?, descr = ?, prix = ?, stock = ?
            WHERE id = ?
        ");
        $stmt->execute([$nom, $descr, $prix, $stock, $id]);
        header("Location: liste_produits.php");
        exit;
    } else {
        echo "Tous les champs obligatoires doivent être remplis.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$prod = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prod) { echo "Produit introuvable."; exit; }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier produit</title>
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
        <h2>Modifier Produit</h2>
        <form method="post">
            <label>Nom:</label>
            <input type="text" name="nom" value="<?= $prod['nom'] ?>" required>
            
            <label>Description:</label>
            <textarea name="descr"><?= $prod['descr'] ?></textarea>
            
            <label>Prix:</label>
            <input type="number" step="0.01" name="prix" value="<?= $prod['prix'] ?>" required>
            
            <label>Stock:</label>
            <input type="number" name="stock" value="<?= $prod['stock'] ?>" required>
            
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>