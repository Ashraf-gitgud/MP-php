<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    header("Location: liste_produits.php");
    exit;
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE nom LIKE ? ORDER BY nom");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM produits ORDER BY nom");
}

$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h2 class="list-title">Liste des produits</h2>
    <form method="get" class="filter-form">
        <input type="text" name="q" placeholder="Rechercher un produit..." 
            value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>">
        <button type="submit">Rechercher</button>
    
        <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
            <a href="liste_produits.php" class="clear-btn">Effacer</a>
        <?php endif; ?>
    </form>
    <table class="list-table">
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
            <td><?= $p['prix'] ?></td>
            <td><?= $p['stock'] ?></td>
            <td style="display:flex; gap:0.4rem; align-items:center;">
                <a href="modifier_produit.php?id=<?= $p['id'] ?>" class="table-btn">Modifier</a>
                <form method="post" style="display:inline-flex;">
                    <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                    <button class="table-btn delete-btn" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>