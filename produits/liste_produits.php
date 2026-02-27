<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../connexion/login.php');
    exit();
}
require '../db/base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    header("Location: produits-liste.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM produits ORDER BY nom");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
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
        <h2>Liste des produits</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($produits as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['nom'] ?></td>
                <td><?= $p['descr'] ?></td>
                <td><?= $p['prix'] ?> €</td>
                <td><?= $p['stock'] ?></td>
                <td>
                    <a href='modifier_produit.php?id=<?= $p['id'] ?>'>Modifier</a>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='delete_id' value='<?= $p['id'] ?>'>
                        <button type='submit'>Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>