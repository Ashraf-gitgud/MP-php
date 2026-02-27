<?php
require '../db/base.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../connexion/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("SELECT qty, nom FROM commandes WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE nom = ?");
        $stmt->execute([$order['qty'], $order['nom']]);
    }

    $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    header("Location: liste_commandes.php");
    exit;
}

$stmt = $pdo->query("
    SELECT c.id, c.code_client, cl.nom AS client_nom, cl.prenom AS client_prenom, 
           cl.email AS client_email, c.nom AS produit_nom, c.prix, c.qty, c.total, c.date_commande
    FROM commandes c
    JOIN clients cl ON c.code_client = cl.code_client
    ORDER BY c.date_commande DESC
");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Liste des commandes</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>ID</th>
        <th>Client</th>
        <th>Email</th>
        <th>Produit</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Total</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>";

foreach ($commandes as $o) {
    echo "<tr>";
    echo "<td>".$o['id']."</td>";
    echo "<td>".$o['client_nom']." ".$o['client_prenom']."</td>";
    echo "<td>".$o['client_email']."</td>";
    echo "<td>".$o['produit_nom']."</td>";
    echo "<td>".$o['prix']."</td>";
    echo "<td>".$o['qty']."</td>";
    echo "<td>".$o['total']."</td>";
    echo "<td>".$o['date_commande']."</td>";

    echo "<td>";
    echo "<a href='modifier_commande.php?id=".$o['id']."'>Modifier</a> ";

    echo "<form method='post' style='display:inline;'>
            <input type='hidden' name='delete_id' value='".$o['id']."'>
            <button type='submit'>Supprimer</button>
          </form>";
    echo "</td>";

    echo "</tr>";
}
echo "</table>";
?>