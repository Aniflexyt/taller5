<?php
require_once 'conexion.php';

$mensaje = '';

// Procesar el formulario si se envió por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $programa = trim($_POST['programa']);

    $guardadoPG = false;
    $guardadoMongo = false;

    // 1. Guardar en PostgreSQL
    try {
        $sql = "INSERT INTO estudiantes (nombre, email, programa) VALUES (:nombre, :email, :programa)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre, 'email' => $email, 'programa' => $programa]);
        $guardadoPG = true;
    } catch (PDOException $e) {
        $errorPG = $e->getMessage();
    }

    // 2. Respaldar en MongoDB
    try {
        $documento = [
            'nombre' => $nombre,
            'email' => $email,
            'programa' => $programa,
            'fecha_registro' => new MongoDB\BSON\UTCDateTime()
        ];
        $resultado = $coleccionEstudiantes->insertOne($documento);
        if ($resultado->getInsertedCount() > 0) {
            $guardadoMongo = true;
        }
    } catch (Exception $e) {
        $errorMongo = $e->getMessage();
    }

    // Generar mensaje final
    if ($guardadoPG && $guardadoMongo) {
        $mensaje = "<div style='color: green;'>✅ Éxito: El registro fue almacenado en ambos soportes (PostgreSQL y MongoDB).</div>";
    } else {
        $mensaje = "<div style='color: red;'>⚠️ Error parcial o total:<br>";
        $mensaje .= $guardadoPG ? "PostgreSQL: Guardado exitoso.<br>" : "PostgreSQL: Error - $errorPG<br>";
        $mensaje .= $guardadoMongo ? "MongoDB: Guardado exitoso.<br>" : "MongoDB: Error - $errorMongo<br>";
        $mensaje .= "</div>";
    }
}

// Consultar datos de PostgreSQL
$estudiantesPG = [];
try {
    $stmt = $pdo->query("SELECT * FROM estudiantes ORDER BY id DESC");
    $estudiantesPG = $stmt->fetchAll();
} catch (PDOException $e) {
    $mensaje .= "<div style='color: red;'>Error al consultar PostgreSQL: " . $e->getMessage() . "</div>";
}

// Consultar datos de MongoDB
$estudiantesMongo = [];
try {
    $cursor = $coleccionEstudiantes->find([], ['sort' => ['fecha_registro' => -1]]);
    $estudiantesMongo = $cursor->toArray();
} catch (Exception $e) {
    $mensaje .= "<div style='color: red;'>Error al consultar MongoDB: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Estudiantes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .grid { display: flex; gap: 20px; }
        .col { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Estudiantes</h2>
        
        <?= $mensaje ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Programa/Ficha:</label>
                <input type="text" name="programa" required>
            </div>
            <button type="submit" name="registrar">Registrar y Respaldar</button>
        </form>

        <hr>

        <div class="grid">
            <div class="col">
                <h3>Datos en PostgreSQL</h3>
                <table>
                    <tr><th>ID</th><th>Nombre</th><th>Email</th></tr>
                    <?php foreach ($estudiantesPG as $est) : ?>
                        <tr>
                            <td><?= htmlspecialchars($est['id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($est['nombre'] ?? '') ?></td>
                            <td><?= htmlspecialchars($est['email'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="col">
                <h3>Respaldo en MongoDB Atlas</h3>
                <table>
                    <tr><th>Nombre</th><th>Programa</th></tr>
                    <?php foreach ($estudiantesMongo as $doc) : ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['nombre'] ?? '') ?></td>
                            <td><?= htmlspecialchars($doc['programa'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
