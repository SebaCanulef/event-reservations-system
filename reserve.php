<?php
session_start();
include 'includes/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener el ID del evento desde la URL
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    header("Location: index.php");
    exit;
}
$event_id = (int)$_GET['event_id'];

// Obtener detalles del evento
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: index.php");
    exit;
}

// Procesar la reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT reserved, capacity FROM events WHERE id = ? FOR UPDATE");
        $stmt->execute([$event_id]);
        $event_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event_data['reserved'] < $event_data['capacity']) {
            $stmt = $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $event_id]);

            $stmt = $pdo->prepare("UPDATE events SET reserved = reserved + 1 WHERE id = ?");
            $stmt->execute([$event_id]);

            $pdo->commit();
            $success = "¡Reserva realizada con éxito!";
        } else {
            $pdo->rollBack();
            $error = "No hay plazas disponibles.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error al procesar la reserva: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .header {
            background-color: #343a40;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .card-reserva {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Reserva de Evento</h1>
</div>

<div class="container mt-5">
    <div class="card-reserva mx-auto" style="max-width: 600px;">
        <h3 class="mb-3 text-primary"><?php echo htmlspecialchars($event['name']); ?></h3>
        <p><i class="bi bi-calendar3"></i> Fecha: <?php echo $event['date']; ?></p>
        <p><i class="bi bi-geo-alt"></i> Ubicación: <?php echo $event['location']; ?></p>
        <p><i class="bi bi-people-fill"></i> Plazas disponibles: <strong><?php echo $event['capacity'] - $event['reserved']; ?></strong></p>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?><br>
                Detalles de tu reserva:<br>
                Evento: <strong><?php echo htmlspecialchars($event['name']); ?></strong><br>
                Fecha: <?php echo $event['date']; ?><br>
                Ubicación: <?php echo $event['location']; ?><br>
                ¡Te esperamos!
            </div>
            <a href="index.php" class="btn btn-primary">Volver al Dashboard</a>

        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>

        <?php elseif ($event['reserved'] >= $event['capacity']): ?>
            <div class="alert alert-warning">No hay plazas disponibles para este evento.</div>
            <a href="index.php" class="btn btn-secondary">Volver</a>

        <?php else: ?>
            <form method="POST">
                <button type="submit" class="btn btn-success w-100 mb-2">Confirmar Reserva</button>
                <a href="index.php" class="btn btn-outline-secondary w-100">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
