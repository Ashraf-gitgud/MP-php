<?php
require '../db/base.php';

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (!$id) { echo "Commande non spécifiée."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_client = isset($_POST['code_client']) ? $_POST['code_client'] : '';
    $produit_id  = isset($_POST['produit_id']) ? $_POST['produit_id'] : '';
    $qty         = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

    if ($code_client && $produit_id && $qty > 0) {

        $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE nom = ?");
        $stmt->execute([$old['qty'], $old['nom']]);

        $stmt = $pdo->prepare("SELECT nom, prix, stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($qty > $prod['stock']) { echo "Stock insuffisant."; exit; }
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

        $content = "===== FACTURE COMMANDE #".$old.['code']." =====\n";
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

        header("Location: liste_commandes.php");
        exit;
    } else {
        echo "Tous les champs sont obligatoires et quantité > 0.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$clients = $pdo->query("SELECT code_client, nom, prenom FROM clients ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$produits = $pdo->query("SELECT id, nom, stock FROM produits ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Modifier commande</h2>
<form method="post">
    Client: 
    <select name="code_client" required>
        <option value="">--Sélectionner--</option>
        <?php foreach ($clients as $c) {
            $sel = ($c['code_client'] == $order['code_client']) ? 'selected' : '';
            echo '<option value="'.$c['code_client'].'" '.$sel.'>'.$c['nom'].' '.$c['prenom'].'</option>';
        } ?>
    </select><br>

    Produit: 
    <select name="produit_id" required>
        <option value="">--Sélectionner--</option>
        <?php foreach ($produits as $p) {
            $sel = ($p['nom'] == $order['nom']) ? 'selected' : '';
            echo '<option value="'.$p['id'].'" '.$sel.'>'.$p['nom'].' (Stock: '.$p['stock'].')</option>';
        } ?>
    </select><br>

    Quantité: <input type="number" name="qty" value="<?= $order['qty'] ?>" min="1" required><br>
    <button type="submit">Mettre à jour</button>
</form>