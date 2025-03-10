<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Obtener eventos disponibles
$stmt = $pdo->query("SELECT * FROM events WHERE reserved < capacity");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas de Eventos</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eventos Disponibles</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Cerrar sesión</a></p>
        <?php else: ?>
            <p><a href="login.php">Inicia sesión</a> o <a href="register.php">regístrate</a> para reservar.</p>
        <?php endif; ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <p class="card-text">Fecha: <?php echo $event['date']; ?></p>
                            <p class="card-text">Plazas: <?php echo $event['capacity'] - $event['reserved']; ?> disponibles</p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="reserve.php?event_id=<?php echo $event['id']; ?>" class="btn btn-primary">Reservar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
