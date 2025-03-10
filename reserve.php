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
    // Verificar si hay capacidad disponible (transacción para evitar race conditions)
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT reserved, capacity FROM events WHERE id = ? FOR UPDATE");
        $stmt->execute([$event_id]);
        $event_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event_data['reserved'] < $event_data['capacity']) {
            // Insertar la reserva
            $stmt = $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $event_id]);

            // Actualizar el conteo de reservas
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
</head>
<body>
    <div class="container mt-5">
        <h1>Reservar: <?php echo htmlspecialchars($event['name']); ?></h1>
        <p>Fecha: <?php echo $event['date']; ?></p>
        <p>Plazas disponibles: <?php echo $event['capacity'] - $event['reserved']; ?></p>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?><br>
                Detalles de tu reserva:<br>
                Evento: <?php echo htmlspecialchars($event['name']); ?><br>
                Fecha: <?php echo $event['date']; ?><br>
                ¡Te esperamos!
            </div>
        <a href="index.php" class="btn btn-primary">Volver</a>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($event['reserved'] >= $event['capacity']): ?>
            <div class="alert alert-warning">No hay plazas disponibles.</div>
        <?php else: ?>
            <form method="POST">
                <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>