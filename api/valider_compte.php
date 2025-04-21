<?php
require 'config.php';

$activation_success = false;
$message = "";

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND token = ? AND compte_active = 0");
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET compte_active = 1, token = NULL WHERE email = ?");
        $stmt->execute([$email]);

        $activation_success = true;
        $message = "✅ Votre compte a été activé avec succès ! Vous allez être redirigé vers la page de connexion...";
    } else {
        $message = "❌ Lien invalide ou compte déjà activé.";
    }
} else {
    $message = "❌ Paramètres manquants.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activation du compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1abc9c, #2ecc71);
            color: white;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .card {
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2 class="<?= $activation_success ? 'text-success' : 'text-danger' ?>">
        <?= $message ?>
    </h2>
    <p>
        <?php if ($activation_success): ?>
            <a href="login.php" class="btn btn-green mt-3">Se connecter</a>
            <script>
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 3000);
            </script>
        <?php else: ?>
            <a href="index.php" class="btn btn-secondary mt-3">Retour à l'accueil</a>
        <?php endif; ?>
    </p>
</div>

</body>
</html>
