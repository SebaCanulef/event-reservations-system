<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE date < CURDATE() ORDER BY date DESC");
$stmt->execute();
$pastEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Eventos</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .dashboard-header {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            text-align: center;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>Historial de Eventos</h1>
    </div>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <span class="navbar-text">
                Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> |
                <a href="admin.php">Volver al panel</a> |
                <a href="logout.php">Cerrar sesión</a>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4 text-primary">Eventos Pasados</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Fecha</th>
                                <th>Capacidad</th>
                                <th>Reservados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastEvents as $event): ?>
                                <tr>
                                    <td><?php echo $event['id']; ?></td>
                                    <td><?php echo htmlspecialchars($event['name']); ?></td>
                                    <td><?php echo $event['date']; ?></td>
                                    <td><?php echo $event['capacity']; ?></td>
                                    <td><?php echo $event['reserved']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pastEvents)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay eventos pasados registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
