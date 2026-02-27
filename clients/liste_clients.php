<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_code'])) {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE code_client = ?");
    $stmt->execute([$_POST['delete_code']]);
    header("Location: liste_clients.php");
    exit;
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE nom LIKE ? OR prenom LIKE ? ORDER BY nom, prenom");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM clients ORDER BY nom, prenom");
}
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    <a href="liste_clients.php"> Liste des clients</a>
                    <a href="ajouter_client.php"> Ajouter un client</a>
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
                    <a href="../commandes/liste_commandes.php"> Liste des commandes</a>
                    <a href="../commandes/ajouter_commande.php"> Nouvelle commande</a>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <a href="../connexion/logout.php" class="power-btn">Déconnexion</a>
        </div>
    </nav>
    <h2 class="list-title">Liste des clients</h2>
    <form method="get" class="filter-form">
        <input type="text" name="q" placeholder="Rechercher un client..." 
            value="<?= isset($_GET['q'])?$_GET['q']:'' ?>">
        <button type="submit">Rechercher</button>
        <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
            <a href="liste_clients.php" class="clear-btn">Effacer</a>
        <?php endif; ?>
    </form>
    <table class="list-table">
        <tr>
            <th>Code</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($clients as $c): ?>
        <tr>
            <td><?= $c['code_client'] ?></td>
            <td><?= $c['nom'] ?></td>
            <td><?= $c['prenom'] ?></td>
            <td><?= $c['email'] ?></td>
            <td><?= $c['tele'] ?></td>
            <td>
                <a href="modifier_client.php?code_client=<?= $c['code_client'] ?>" class="table-btn">Modifier</a>
                <form method="post" style="display:inline-flex;">
                    <input type="hidden" name="delete_code" value="<?= $c['code_client'] ?>">
                    <button class="table-btn delete-btn" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>


</body>
</html>
