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

<form method="post">
    Nom: <input type="text" name="nom" required><br>
    Description: <textarea name="descr"></textarea><br>
    Prix: <input type="number" step="0.01" name="prix" required><br>
    Stock: <input type="number" name="stock" required><br>
    <button type="submit">Ajouter</button>
</form>