<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE nom = ?");
        $stmt->execute([$order['qty'], $order['nom']]);
        $dt = strtotime($order['date_commande']);
        $filename = 'facture/commande_'.date('dmY&His', $dt).'.txt';        
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        header("Location: liste_commandes.php");
        exit;
        if (file_exists($filename)) {
            unlink($filename);
        }

    }

}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT c.id, c.code_client, cl.nom AS client_nom, cl.prenom AS client_prenom, 
               cl.email AS client_email, c.nom AS produit_nom, c.prix, c.qty, c.total, c.date_commande
        FROM commandes c
        JOIN clients cl ON c.code_client = cl.code_client
        WHERE cl.nom LIKE ? OR cl.prenom LIKE ?
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT c.id, c.code_client, cl.nom AS client_nom, cl.prenom AS client_prenom, 
               cl.email AS client_email, c.nom AS produit_nom, c.prix, c.qty, c.total, c.date_commande
        FROM commandes c
        JOIN clients cl ON c.code_client = cl.code_client
        ORDER BY c.date_commande DESC
    ");
}

$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h2 class="list-title">Liste des commandes</h2>
    <form method="get" class="filter-form">
        <input type="text" name="q" placeholder="Rechercher par un client..."
            value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">Rechercher</button>

        <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
            <a href="liste_commandes.php" class="clear-btn">Effacer</a>
        <?php endif; ?>
    </form>
    <table class="list-table">
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Email</th>
            <th>Produit</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Total</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($commandes as $o): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= $o['client_nom'] . ' ' . $o['client_prenom'] ?></td>
            <td><?= $o['client_email'] ?></td>
            <td><?= $o['produit_nom'] ?></td>
            <td><?= $o['prix'] ?></td>
            <td><?= $o['qty'] ?></td>
            <td><?= $o['total'] ?></td>
            <td><?= $o['date_commande'] ?></td>
            <td>
                <a href="modifier_commande.php?id=<?= $o['id'] ?>" class="table-btn">Modifier</a>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?= $o['id'] ?>">
                    <button type="submit" class="table-btn delete-btn">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>