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

<h2>Modifier Client</h2>
<form method="post">
    Nom: <input type="text" name="nom" value="<?= $client['nom'] ?>" required><br>
    Prénom: <input type="text" name="prenom" value="<?= $client['prenom'] ?>" required><br>
    Email: <input type="email" name="email" value="<?= $client['email'] ?>" required><br>
    Téléphone: <input type="text" name="tele" value="<?= $client['tele'] ?>"><br>
    <button type="submit">Mettre à jour</button>
</form>