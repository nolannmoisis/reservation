<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $heure = $_POST["heure"];
    $nombre_personnes = $_POST["nombre_personnes"];
    $user_id = $_SESSION["user_id"];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
    $stmt->execute([$date, $heure]);
    $existing = $stmt->fetchColumn();

    if ($existing == 0) {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, date_reservation, heure, nombre_personnes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $date, $heure, $nombre_personnes]);
        $message = "✅ Réservation confirmée avec succès !";
    } else {
        $error = "⚠️ Ce créneau est déjà réservé. Veuillez en choisir un autre.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container py-5">
    <h2 class="text-center text-success">Réservez votre table</h2>
    <div class="card p-4 text-white mx-auto shadow-lg bg-dark" style="max-width: 600px;">
        <?php if (isset($message)) : ?>
            <p class="text-success text-center fw-bold"><?= $message ?></p>
            <div class="text-center mt-3">
                <a href="profil.php" class="btn btn-green mx-2"><i class="bi bi-person"></i> Retour au profil</a>
                <a href="index.php" class="btn btn-secondary mx-2"><i class="bi bi-house-door"></i> Retour à l'accueil</a>
            </div>
        <?php elseif (isset($error)) : ?>
            <p class="text-danger text-center fw-bold"><?= $error ?></p>
        <?php else : ?>
            <form action="reservation.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="text" class="form-control" id="datePicker" name="date" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Heure</label>
                    <select class="form-control" name="heure" required>
                        <option value="12:00">12:00</option>
                        <option value="13:00">13:00</option>
                        <option value="19:00">19:00</option>
                        <option value="20:00">20:00</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre de personnes</label>
                    <input type="number" class="form-control" name="nombre_personnes" min="1" max="10" required>
                </div>
                <button type="submit" class="btn btn-green w-100">Réserver</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#datePicker", {
        dateFormat: "Y-m-d",
        minDate: "today",
        locale: "fr"
    });
</script>

</body>
</html>
