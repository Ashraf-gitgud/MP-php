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

<form method="post">
    Code: <input type="text" name="code_client" required><br>
    Nom: <input type="text" name="nom" required><br>
    Prénom: <input type="text" name="prenom" required><br>
    Email: <input type="email" name="email" required><br>
    Tel: <input type="text" name="tele"><br>
    <button type="submit">Ajouter</button>
</form>