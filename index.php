<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - GreenBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">GreenBooking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="reservation.php">Réserver</a></li>
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                <?php else : ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<header class="hero">
    <div class="container">
        <h1>Réservez votre table en toute simplicité</h1>
        <p>Un service rapide, efficace et écologique 🌿</p>
        <a href="reservation.php" class="btn btn-green">Réserver maintenant</a>
    </div>
</header>

<footer>
    <p>&copy; 2025 GreenBooking | Suivez-nous sur <a href="#" class="text-white">Instagram</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
