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

<h2>Modifier Produit</h2>
<form method="post">
    Nom: <input type="text" name="nom" value="<?= $prod['nom'] ?>" required><br>
    Description: <textarea name="descr"><?= $prod['descr'] ?></textarea><br>
    Prix: <input type="number" step="0.01" name="prix" value="<?= $prod['prix'] ?>" required><br>
    Stock: <input type="number" name="stock" value="<?= $prod['stock'] ?>" required><br>
    <button type="submit">Mettre à jour</button>
</form>