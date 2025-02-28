<?php
require 'config.php';
require 'mail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $password_confirm = isset($_POST["password_confirm"]) ? $_POST["password_confirm"] : "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ L'adresse email n'est pas valide.";
    }

    elseif (!checkdnsrr(substr(strrchr($email, "@"), 1), "MX")) {
        $error = "❌ Ce domaine email n'existe pas.";
    }

    elseif (preg_match('/@(yopmail\.com|tempmail\.com|10minutemail\.com|mailinator\.com)$/', $email)) {
        $error = "❌ Les emails temporaires ne sont pas autorisés.";
    }

    elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "❌ Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.";
    }

    elseif ($password !== $password_confirm) {
        $error = "❌ Les mots de passe ne correspondent pas.";
    }

    if (!isset($error)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(50));

        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, mot_de_passe, compte_active, token) VALUES (?, ?, ?, ?, 0, ?)");
        if ($stmt->execute([$nom, $prenom, $email, $password_hash, $token])) {
            $sujet = "Activation de votre compte - GreenBooking";
            $lien_activation = "http://localhost/reservation/valider_compte.php?email=$email&token=$token";
            $message = "
                <h2>Bienvenue sur GreenBooking, $prenom !</h2>
                <p>Veuillez cliquer sur le lien ci-dessous pour activer votre compte :</p>
                <p><a href='$lien_activation'>Activer mon compte</a></p>
            ";
            envoyerMail($email, $sujet, $message);

            header("Location: login.php?inscription=success");
            exit();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-8 col-lg-6">
        <div class="card p-3 text-white">
            <h2 class="text-center text-success mb-3"><i class="bi bi-person-plus"></i> Inscription</h2>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <form action="register.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label"><i class="bi bi-person"></i> Nom</label>
                            <input type="text" class="form-control" name="nom" required maxlength="50">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label"><i class="bi bi-person"></i> Prénom</label>
                            <input type="text" class="form-control" name="prenom" required maxlength="50">
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label"><i class="bi bi-calendar"></i> Date de naissance</label>
                    <input type="text" class="form-control" id="date_naissance" name="date_naissance" required>
                </div>
                <div class="mb-2">
                    <label class="form-label"><i class="bi bi-house-door"></i> Adresse</label>
                    <textarea class="form-control" name="adresse" rows="1" required maxlength="255"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label"><i class="bi bi-telephone"></i> Téléphone</label>
                            <input type="tel" class="form-control" name="telephone" pattern="[0-9]{10}" required maxlength="10" placeholder="0601020304">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                            <input type="email" class="form-control" name="email" required maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label"><i class="bi bi-lock"></i> Mot de passe</label>
                    <input type="password" class="form-control" name="password" required maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit" class="btn btn-green w-100"><i class="bi bi-check-circle"></i> S'inscrire</button>
            </form>
            <p class="text-center mt-2">Déjà inscrit ? <a href="login.php" class="text-success">Se connecter</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#date_naissance", {
        dateFormat: "Y-m-d",
        maxDate: new Date().toISOString().split("T")[0],
        altInput: true,
        altFormat: "d F Y",
        locale: "fr"
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        form.addEventListener("submit", function (event) {
            const email = document.querySelector("#email").value;
            const password = document.querySelector("#password").value;
            const confirmPassword = document.querySelector("#password_confirm").value;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const tempEmailRegex = /@(yopmail\.com|tempmail\.com|10minutemail\.com|mailinator\.com)$/;

            if (!emailRegex.test(email)) {
                alert("❌ L'adresse email n'est pas valide.");
                event.preventDefault();
            } else if (tempEmailRegex.test(email)) {
                alert("❌ Les emails temporaires ne sont pas autorisés.");
                event.preventDefault();
            } else if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                alert("❌ Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.");
                event.preventDefault();
            } else if (password !== confirmPassword) {
                alert("❌ Les mots de passe ne correspondent pas.");
                event.preventDefault();
            }
        });
    });
</script>


</body>
</html>
