<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

$code = isset($_GET['code_client']) ? $_GET['code_client'] : '';
if (!$code) { echo "Client non spécifié."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $email  = isset($_POST['email']) ? trim($_POST['email']) : '';
    $tele   = isset($_POST['tele']) ? trim($_POST['tele']) : '';

    if ($nom && $prenom && $email) {
        $stmt = $pdo->prepare("
            UPDATE clients 
            SET nom = ?, prenom = ?, email = ?, tele = ?
            WHERE code_client = ?
        ");
        $stmt->execute([$nom, $prenom, $email, $tele, $code]);
        header("Location: liste_clients.php");
        exit;
    } else {
        echo "Tous les champs obligatoires doivent être remplis.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM clients WHERE code_client = ?");
$stmt->execute([$code]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$client) { echo "Client introuvable."; exit; }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier client</title>
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
        <h2>Modifier Client</h2>
        <form method="post">
            <label>Nom:</label>
            <input type="text" name="nom" value="<?= $client['nom'] ?>" required>
            
            <label>Prénom:</label>
            <input type="text" name="prenom" value="<?= $client['prenom'] ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?= $client['email'] ?>" required>
            
            <label>Téléphone:</label>
            <input type="text" name="tele" value="<?= $client['tele'] ?>">
            
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>