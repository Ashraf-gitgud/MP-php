<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code   = isset($_POST['code_client']) ? trim($_POST['code_client']) : '';
    $nom    = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $email  = isset($_POST['email']) ? trim($_POST['email']) : '';
    $tele   = isset($_POST['tele']) ? trim($_POST['tele']) : '';

    if ($code && $nom && $prenom && $email) {
        $stmt = $pdo->prepare("
            INSERT INTO clients (code_client, nom, prenom, email, tele)
            VALUES (:code, :nom, :prenom, :email, :tele)
        ");
        try {
            $stmt->execute([
                ':code'   => $code,
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':email'  => $email,
                ':tele'   => $tele
            ]);
            echo "Client ajouté.";
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
    <title>Ajouter un client</title>
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
        <h2>Ajouter un client</h2>
        <form method="post">
            <label>Code client:</label>
            <input type="text" name="code_client" required>
            
            <label>Nom:</label>
            <input type="text" name="nom" required>
            
            <label>Prénom:</label>
            <input type="text" name="prenom" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Téléphone:</label>
            <input type="text" name="tele">
            
            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>