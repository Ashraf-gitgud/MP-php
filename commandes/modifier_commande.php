<?php
require '../db/base.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

$id = $_GET['id'] ?? '';
if (!$id) {
    $message = "Commande non spécifiée.";
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_client = $_POST['code_client'] ?? '';
    $produit_id  = $_POST['produit_id'] ?? '';
    $qty         = $_POST['qty'] ?? 0;

    if ($code_client && $produit_id && $qty > 0) {

        $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT nom, prix, stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            $message = "Produit introuvable.";
        } elseif ($qty > $prod['stock'] + $old['qty']) {
            $message = "Stock insuffisant.";
        } else {
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE nom = ?");
            $stmt->execute([$old['qty'], $old['nom']]);

            $total = $prod['prix'] * $qty;
            $stmt = $pdo->prepare("
                UPDATE commandes
                SET code_client = ?, nom = ?, prix = ?, qty = ?, total = ?
                WHERE id = ?
            ");
            $stmt->execute([$code_client, $prod['nom'], $prod['prix'], $qty, $total, $id]);

            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$qty, $produit_id]);

            $dt = strtotime($old['date_commande']);
            $filename = 'facture/commande_'.date('dmY&His', $dt).'.txt';

            $stmt = $pdo->prepare("SELECT nom, prenom, email FROM clients WHERE code_client = ?");
            $stmt->execute([$code_client]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            $content = "===== FACTURE COMMANDE #".$old['code_client']." =====\n";
            $content .= "Date: ".date('d/m/Y H:i:s', $dt)."\n\n";
            $content .= "CLIENT\n";
            $content .= "Code: ".$code_client."\n";
            $content .= "Nom: ".$client['nom']." ".$client['prenom']."\n";
            $content .= "Email: ".$client['email']."\n\n";
            $content .= "PRODUIT\n";
            $content .= "Nom: ".$prod['nom']."\n";
            $content .= "Prix unitaire: ".$prod['prix']."\n";
            $content .= "Quantité: ".$qty."\n";
            $content .= "--------------------------\n";
            $content .= "Montant total: ".$total."\n";
            $content .= "===========================\n";

            file_put_contents($filename, $content);

            $message = "Commande mise à jour.";
        }
    } else {
        $message = "Tous les champs sont obligatoires et quantité > 0.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$clients = $pdo->query("SELECT code_client, nom, prenom FROM clients ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$produits = $pdo->query("SELECT id, nom, stock FROM produits ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
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

    <form class="form-card" method="post">
        <h2 class="form-title">Modifier Commande</h2>
        <?php if (!empty($message)): ?>
            <div class="form-message"><?= $message ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label>Client:</label>
            <select class="form-input" name="code_client" required>
                <option value="">--Sélectionner--</option>
                <?php foreach ($clients as $c): 
                    $sel = ($c['code_client'] == $order['code_client']) ? 'selected' : '';
                ?>
                    <option value="<?= $c['code_client'] ?>" <?= $sel ?>><?= $c['nom'] ?> <?= $c['prenom'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Produit:</label>
            <select class="form-input" name="produit_id" required>
                <option value="">--Sélectionner--</option>
                <?php foreach ($produits as $p): 
                    $sel = ($p['nom'] == $order['nom']) ? 'selected' : '';
                ?>
                    <option value="<?= $p['id'] ?>" <?= $sel ?>><?= $p['nom'] ?> (Stock: <?= $p['stock'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Quantité: </label>
            <input class="form-input" type="number" name="qty" value="<?= $order['qty'] ?>" min="1" required>
        </div>

        <button class="form-btn" type="submit">Mettre à jour</button>
        <a href="../index.php" class="table-btn delete-btn">Annuler</a>
    </form>
</body>
</html>