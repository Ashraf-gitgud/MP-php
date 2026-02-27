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
        $error = "Nom d'utilisateur ou mot de passe invalide";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<div class="login-container">
    <h2 class="form-title" style="border:none">Connexion</h2>

    <?php if (isset($error)): ?>
        <p class="form-error"><?= $error ?></p>
    <?php endif; ?>

    <form class="form-card" method="POST">
        <div class="form-group">
            <input class="form-input" type="text" name="usr" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="form-group">
            <input class="form-input" type="password" name="pw" placeholder="Mot de passe" required>
        </div>
        <button class="form-btn" type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>