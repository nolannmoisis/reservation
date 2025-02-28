<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $email = htmlspecialchars($_POST["email"]);
    $telephone = htmlspecialchars($_POST["telephone"]);
    $adresse = htmlspecialchars($_POST["adresse"]);
    $password_confirm = $_POST["password_confirm"];
    $new_password = $_POST["new_password"];

    $stmt = $pdo->prepare("SELECT mot_de_passe FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $userData = $stmt->fetch();

    if (!password_verify($password_confirm, $userData["mot_de_passe"])) {
        $error = "Mot de passe incorrect. Veuillez réessayer.";
    } else {
        if (!empty($new_password)) {
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$new_password_hashed, $user_id]);
        }

        $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $telephone, $adresse, $user_id]);

        header("Location: profil.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-8 col-lg-6">
        <div class="card p-4 text-white bg-dark shadow-lg">
            <h2 class="text-center text-success mb-4"><i class="bi bi-person"></i> Modifier mon profil</h2>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <form action="modifier_profil.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person"></i> Nom</label>
                            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($user["nom"]) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person"></i> Prénom</label>
                            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($user["prenom"]) ?>" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-house-door"></i> Adresse</label>
                    <input type="text" class="form-control" name="adresse" value="<?= htmlspecialchars($user["adresse"]) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-telephone"></i> Téléphone</label>
                            <input type="tel" class="form-control" name="telephone" value="<?= htmlspecialchars($user["telephone"]) ?>" required>
                        </div>
                    </div>
                </div>
                <h5 class="text-warning mt-3">Changer de mot de passe (optionnel) :</h5>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-lock"></i> Nouveau mot de passe</label>
                    <input type="password" class="form-control" name="new_password">
                </div>
                <h5 class="text-danger mt-3">Confirmer avec votre mot de passe actuel :</h5>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-key"></i> Mot de passe actuel</label>
                    <input type="password" class="form-control" name="password_confirm" required>
                </div>
                <button type="submit" class="btn btn-green w-100"><i class="bi bi-check-circle"></i> Mettre à jour</button>
            </form>
            <div class="text-center mt-3">
                <a href="profil.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
