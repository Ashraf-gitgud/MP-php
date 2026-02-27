<?php
require '../db/base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_code'])) {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE code_client = ?");
    $stmt->execute([$_POST['delete_code']]);
    header("Location: liste_clients.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM clients ORDER BY nom, prenom");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Liste des clients</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>Code</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Actions</th>
      </tr>";

foreach ($clients as $c) {
    echo "<tr>";
    echo "<td>".$c['code_client']."</td>";
    echo "<td>".$c['nom']."</td>";
    echo "<td>".$c['prenom']."</td>";
    echo "<td>".$c['email']."</td>";
    echo "<td>".$c['tele']."</td>";

    echo "<td>";

    echo "<a href='modifier_client.php?code_client=".$c['code_client']."'><button>Modifier</button></a> ";

    // DELETE → POST form
    echo "<form method='post' style='display:inline;'>
            <input type='hidden' name='delete_code' value='".$c['code_client']."'>
            <button type='submit'>Supprimer</button>
          </form>";

    echo "</td>";
    echo "</tr>";
}

echo "</table>";
?>