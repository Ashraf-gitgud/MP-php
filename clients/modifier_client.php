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
        try{
        $stmt->execute([$nom, $prenom, $email, $tele, $code]);
        header("Location: liste_clients.php");
        exit;
        }catch(PDOException $e){
        $message = "Un erreur est survenue";
        }
    } else {
        $message = "Un erreur est survenue";
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
    <h2 class="form-title">Modifier Client</h2>
    <?php if (!empty($message)): ?>
    <div class="form-message"><?= $message ?></div>
    <?php endif; ?>
        <div class="form-group">
            <label>Nom:</label>
            <input class="form-input" type="text" name="nom" value="<?= $client['nom'] ?>" required>
        </div>
        <div class="form-group">
            <label>Prénom:</label>
            <input class="form-input" type="text" name="prenom" value="<?= $client['prenom'] ?>" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input class="form-input" type="email" name="email" value="<?= $client['email'] ?>" required>
        </div>
        <div class="form-group">
            <label>Téléphone:</label>
            <input class="form-input" type="text" name="tele" value="<?= $client['tele'] ?>">
        </div>
        <button class="form-btn" type="submit">Mettre à jour</button>
        <a href="../index.php" class="table-btn delete-btn" type="submit">Annuler</a>
    </form>


</body>
</html>