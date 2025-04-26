<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Redirigir a admin si ya inició sesión y tiene rol admin
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: http://localhost/event-reservations/admin.php");
    exit;
}

// Construir filtros dinámicos
$where = "WHERE reserved < capacity AND date >= CURDATE()";
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

$stmt = $pdo->prepare("SELECT * FROM events $where");
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalEvents = count($events);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Eventos</title>
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
        .card-summary {
            background: white;
            border-left: 5px solid #0d6efd;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 1rem;
            border-radius: 8px;
        }
        .card-summary i {
            font-size: 1.5rem;
            color: #0d6efd;
        }
        .event-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>Reserva de Eventos</h1>
    </div>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <span class="navbar-text">
                <?php if (isset($_SESSION['user_id'])): ?>
                    Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> |
                    <a href="logout.php">Cerrar sesión</a>
                <?php else: ?>
                    <a href="login.php">Iniciar sesión</a> | <a href="register.php">Registrarse</a>
                <?php endif; ?>
            </span>
        </div>
    </nav>

    <div class="container">
        <!-- Filtros -->
        <form method="GET" class="row g-3 justify-content-center my-4">
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
                <button class="btn btn-primary w-100" type="submit">Filtrar</button>
            </div>
        </form>

        <!-- Resumen -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card-summary d-flex align-items-center">
                    <i class="bi bi-calendar-event me-3"></i>
                    <div>
                        <h5 class="mb-0">Eventos Disponibles</h5>
                        <small class="text-muted"><?php echo $totalEvents; ?> eventos próximos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Eventos -->
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <p class="card-text"><i class="bi bi-calendar3"></i> Fecha: <?php echo $event['date']; ?></p>
                            <p class="card-text"><i class="bi bi-geo-alt"></i> Ubicación: <?php echo $event['location']; ?></p>
                            <p class="card-text"><i class="bi bi-people-fill"></i> Plazas disponibles: <strong><?php echo $event['capacity'] - $event['reserved']; ?></strong></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="reserve.php?event_id=<?php echo $event['id']; ?>" class="btn btn-outline-primary w-100">Reservar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
