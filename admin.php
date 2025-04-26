<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Eliminar evento y reservas asociadas
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Eliminar primero las reservas asociadas
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE event_id = ?");
    $stmt->execute([$id]);

    // Luego eliminar el evento
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);

    $success = "Evento eliminado correctamente. Recuerda notificar a los usuarios sobre la cancelación.";
}

// Filtros de búsqueda
$where = "WHERE date >= CURDATE()";
$params = [];

if (!empty($_GET['fecha'])) {
    $where .= " AND date = :fecha";
    $params[':fecha'] = $_GET['fecha'];
}

if (!empty($_GET['ubicacion'])) {
    $where .= " AND location LIKE :ubicacion";
    $params[':ubicacion'] = "%" . $_GET['ubicacion'] . "%";
}

if (!empty($_GET['tipo'])) {
    $where .= " AND type = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}

$stmt = $pdo->prepare("SELECT * FROM events $where ORDER BY date ASC");
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalEvents = count($events);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
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
        <h1>Panel de Administración</h1>
    </div>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <span class="navbar-text">
                Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> |
                <a href="index.php">Inicio</a> |
                <a href="logout.php">Cerrar sesión</a>
            </span>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-warning"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Botones superiores -->
        <div class="mb-4 d-flex justify-content-between flex-wrap gap-2">
            <a href="crear_evento.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Crear nuevo evento
            </a>
            <a href="historial_eventos.php" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Ver historial de eventos
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="row g-3 justify-content-center mb-4">
            <div class="col-md-3">
                <input type="date" name="fecha" class="form-control" value="<?php echo $_GET['fecha'] ?? ''; ?>" placeholder="Fecha">
            </div>
            <div class="col-md-3">
                <input type="text" name="ubicacion" class="form-control" value="<?php echo $_GET['ubicacion'] ?? ''; ?>" placeholder="Ubicación">
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-select">
                    <option value="">Tipo de Evento</option>
                    <option value="Conferencia" <?php if ($_GET['tipo'] ?? '' == 'Conferencia') echo 'selected'; ?>>Conferencia</option>
                    <option value="Taller" <?php if ($_GET['tipo'] ?? '' == 'Taller') echo 'selected'; ?>>Taller</option>
                    <option value="Seminario" <?php if ($_GET['tipo'] ?? '' == 'Seminario') echo 'selected'; ?>>Seminario</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Buscar</button>
            </div>
        </form>


        <!-- Tabla de eventos -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4 text-primary">Eventos Futuros</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Fecha</th>
                                <th>Ubicación</th>
                                <th>Tipo</th>
                                <th>Capacidad</th>
                                <th>Reservados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo $event['id']; ?></td>
                                    <td><?php echo htmlspecialchars($event['name']); ?></td>
                                    <td><?php echo $event['date']; ?></td>
                                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    <td><?php echo htmlspecialchars($event['type']); ?></td>
                                    <td><?php echo $event['capacity']; ?></td>
                                    <td><?php echo $event['reserved']; ?></td>
                                    <td>
                                        <a href="editar_evento.php?id=<?php echo $event['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="admin.php?delete=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este evento? Se eliminarán también sus reservas.');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($events)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No hay eventos que coincidan con la búsqueda.</td>
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
