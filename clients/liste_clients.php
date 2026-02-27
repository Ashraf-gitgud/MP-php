<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../connexion/login.php');
    exit();
}
require '../db/base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_code'])) {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE code_client = ?");
    $stmt->execute([$_POST['delete_code']]);
    header("Location: clients-liste.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM clients ORDER BY nom, prenom");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des clients</title>
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
        <h2>Liste des clients</h2>
        <table>
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
                    <a href='modifier_client.php?code_client=<?= $c['code_client'] ?>'>Modifier</a>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='delete_code' value='<?= $c['code_client'] ?>'>
                        <button type='submit'>Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>