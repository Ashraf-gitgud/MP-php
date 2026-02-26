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
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Login</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <input type="text" name="usr" placeholder="Username" required>
    <input type="password" name="pw" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
</body>
</html>