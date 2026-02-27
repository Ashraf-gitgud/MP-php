<?php
require '../db/base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    header("Location: liste_produits.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM produits ORDER BY nom");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Liste des produits</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Stock</th>
        <th>Actions</th>
      </tr>";

foreach ($produits as $p) {
    echo "<tr>";
    echo "<td>".$p['id']."</td>";
    echo "<td>".$p['nom']."</td>";
    echo "<td>".$p['descr']."</td>";
    echo "<td>".$p['prix']."</td>";
    echo "<td>".$p['stock']."</td>";

    echo "<td>";
    echo "<a href='modifier_produit.php?id=".$p['id']."'>Modifier</a> ";

    echo "<form method='post' style='display:inline;'>
            <input type='hidden' name='delete_id' value='".$p['id']."'>
            <button type='submit'>Supprimer</button>
          </form>";
    echo "</td>";

    echo "</tr>";
}
echo "</table>";
?>