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
            $message = "Client ajouté.";
        } catch (PDOException $e) {
            $message = "Un erreur est survenue";
        }
    } else {
         $message = "Remplissez les champs obligatoires.";
    }
}
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

<form class="form-card" method="post">
    <h2 class="form-title">Ajouter un client</h2>
    <?php if (!empty($message)): ?>
    <div class="form-message"><?= $message ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label>Code:</label>
        <input class="form-input" type="text" name="code_client" required>
    </div>
    <div class="form-group">
        <label>Nom:</label>
        <input class="form-input" type="text" name="nom" required>
    </div>
    <div class="form-group">
        <label>Prénom:</label>
        <input class="form-input" type="text" name="prenom" required>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input class="form-input" type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Tel:</label>
        <input class="form-input" type="text" name="tele">
    </div>
    <button class="form-btn" type="submit">Ajouter</button>
</form>

</body>
</html>