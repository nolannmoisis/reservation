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

$reservations = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY date_reservation DESC");
$reservations->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">GreenBooking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="reservation.php">Réserver</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-white bg-dark shadow-lg p-3">
                <h3 class="text-center text-success mb-3"><i class="bi bi-person-circle"></i> Mon Profil</h3>
                <p><strong>Nom :</strong> <?= htmlspecialchars($user["nom"]) ?></p>
                <p><strong>Prénom :</strong> <?= htmlspecialchars($user["prenom"]) ?></p>
                <p><strong>Date de naissance :</strong> <?= htmlspecialchars($user["date_naissance"]) ?></p>
                <p><strong>Adresse :</strong> <?= htmlspecialchars($user["adresse"]) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($user["telephone"]) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($user["email"]) ?></p>

                <a href="modifier_profil.php" class="btn btn-warning w-100 mb-2"><i class="bi bi-pencil"></i> Modifier</a>
                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                    <i class="bi bi-trash"></i> Supprimer mon compte
                </button>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3 bg-dark text-white shadow-lg">
                <h3 class="text-center text-success mb-3"><i class="bi bi-calendar-check"></i> Mes Réservations</h3>
                <table class="table table-dark table-striped text-center">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Personnes</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($reservation = $reservations->fetch()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation["date_reservation"]) ?></td>
                            <td><?= htmlspecialchars($reservation["heure"]) ?></td>
                            <td><?= htmlspecialchars($reservation["nombre_personnes"]) ?></td>
                            <td>
                                <a href="annuler.php?id=<?= $reservation['id'] ?>" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x-circle"></i> Annuler
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="text-center mt-3">
                    <a href="reservation.php" class="btn btn-green"><i class="bi bi-calendar-plus"></i> Faire une réservation</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Suppression du compte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center">
                    Êtes-vous sûr de vouloir supprimer votre compte ? <br>
                    <strong>Cette action est irréversible.</strong>
                </p>
                <form action="supprimer_compte.php" method="POST">
                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="bi bi-lock"></i> Confirmez votre mot de passe :</label>
                        <input type="password" class="form-control bg-secondary text-white" name="password" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary w-50 me-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-danger w-50">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<footer>
    <p>&copy; 2025 GreenBooking | Suivez-nous sur <a href="#" class="text-white">Instagram</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
