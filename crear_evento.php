<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$error = null;

// Procesar creación del evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $capacity = (int)$_POST['capacity'];

    $today = date('Y-m-d');

    if ($date < $today) {
        $error = "No se puede crear un evento en una fecha pasada.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (name, date, capacity) VALUES (?, ?, ?)");
        $stmt->execute([$name, $date, $capacity]);
        header("Location: admin.php?created=true");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Evento</title>
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
        <h1>Crear Nuevo Evento</h1>
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
        <div class="card shadow-sm p-4">
            <h3 class="mb-4 text-primary">Formulario de creación de evento</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Evento</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacidad</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                </div>
                <button type="submit" name="create" class="btn btn-primary">Crear Evento</button>
                <a href="admin.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
