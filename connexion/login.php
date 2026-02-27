<?php
session_start();
require '../db/base.php';

if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['usr'])){
        $usr = $_POST['usr'];
        }else{
            $usr = '';
        }
    if (isset($_POST['pw'])){
    $pw = $_POST['pw'];
        }else{
    $pw = '';
        }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE usr = ?");
    $stmt->execute([$usr]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($pw, $user['pw'])){
        $_SESSION['user'] = $usr;
        header("Location: ../index.php");
        exit;
    }else{
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content" style="max-width: 400px; margin: 100px auto;">
        <h2 style="text-align: center;">Connexion</h2>
        
        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label>Nom d'utilisateur:</label>
            <input type="text" name="usr" placeholder="Username" required>
            
            <label>Mot de passe:</label>
            <input type="password" name="pw" placeholder="Password" required>
            
            <button type="submit" style="width: 100%;">Se connecter</button>
        </form>
    </div>
</body>
</html>