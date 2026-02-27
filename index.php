<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: connexion/login.php');
        exit();
    }
    require 'db/base.php';
    $user = $_SESSION['user'];

    $stmt = $pdo->query("SELECT COUNT(DISTINCT code_client) AS total_clients FROM commandes");
    $total_clients = $stmt->fetch(PDO::FETCH_ASSOC)['total_clients'];

    $stmt = $pdo->query("SELECT COUNT(DISTINCT nom) AS total_products FROM commandes");
    $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

    $stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM commandes");
    $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <h1><a href="index.php" class="header-link">Panneau d'administration</a></h1>
    
    <nav class="navbar">
        <div class="nav-left">
            <div class="dropdown">
                <button class="dropbtn">Clients ▼</button>
                <div class="dropdown-content">
                    <a href="clients/liste_clients.php"> Liste des clients</a>
                    <a href="clients/ajouter_client.php"> Ajouter un client</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Produits ▼</button>
                <div class="dropdown-content">
                    <a href="produits/liste_produits.php">Liste des produits</a>
                    <a href="produits/ajouter_produit.php"> Ajouter un produit</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Commandes ▼</button>
                <div class="dropdown-content">
                    <a href="commandes/liste_commandes.php"> Liste des commandes</a>
                    <a href="commandes/ajouter_commande.php"> Nouvelle commande</a>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <a href="connexion/logout.php" class="power-btn">Déconnexion</a>
        </div>
    </nav>

    <div class="content">
    <h1 class="welcome">Bienvenue, <?=$user?>!</h1>

    <div class="stats">
        <div class="stat-card">
            <h3>Total des clients qui ont passé des commandes</h3>
            <p><?= $total_clients ?></p>
        </div>
        <div class="stat-card">
            <h3>Total de produits différents commandés</h3>
            <p><?= $total_products ?></p>
        </div>
        <div class="stat-card">
            <h3>Total des commandes</h3>
            <p><?= $total_orders ?></p>
        </div>
    </div>
</div>

</body>
</html>