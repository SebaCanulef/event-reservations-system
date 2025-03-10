<?php
session_start();
include 'includes/db.php';

// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Crear evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $capacity = (int)$_POST['capacity'];
    $stmt = $pdo->prepare("INSERT INTO events (name, date, capacity) VALUES (?, ?, ?)");
    $stmt->execute([$name, $date, $capacity]);
    $success = "Evento creado.";
}

// Eliminar evento
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Evento eliminado.";
}

// Obtener todos los eventos
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Panel de Administración</h1>
        <p><a href="index.php" class="btn btn-secondary">Volver</a> | <a href="logout.php">Cerrar sesión</a></p>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Editar evento -->
        <?php
        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $edit_id = (int)$_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$edit_id]);
            $event_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
                $name = $_POST['name'];
                $date = $_POST['date'];
                $capacity = (int)$_POST['capacity'];
                $stmt = $pdo->prepare("UPDATE events SET name = ?, date = ?, capacity = ? WHERE id = ?");
                $stmt->execute([$name, $date, $capacity, $edit_id]);
                $success = "Evento actualizado.";
                header("Location: admin.php"); // Evita reenvío del formulario
                exit;
            }
        ?>
            <h3>Editar Evento</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event_to_edit['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo $event_to_edit['date']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacidad</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo $event_to_edit['capacity']; ?>" min="1" required>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                <a href="admin.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php } ?>

        <!-- Formulario para crear evento -->
        <h3>Crear Evento</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacidad</label>
                <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
            </div>
            <button type="submit" name="create" class="btn btn-primary">Crear</button>
        </form>

        <!-- Lista de eventos -->
        <h3 class="mt-5">Eventos Existentes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
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
                        <td><?php echo $event['capacity']; ?></td>
                        <td><?php echo $event['reserved']; ?></td>
                        <td>
                            <a href="admin.php?edit=<?php echo $event['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="admin.php?delete=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>