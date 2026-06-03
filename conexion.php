<?php
// Requerir el autoloader de Composer para MongoDB
require_once __DIR__ . '/vendor/autoload.php';

// --- CONEXIÓN POSTGRESQL ---
$pg_host = getenv('PG_HOST') ?: 'localhost';
$pg_port = getenv('PG_PORT') ?: '5432';
$pg_dbname = getenv('PG_DBNAME') ?: 'taller_db';
$pg_user = getenv('PG_USER') ?: 'postgres';
$pg_password = getenv('PG_PASSWORD') ?: 'tu_contraseña';

try {
    $dsn = "pgsql:host=$pg_host;port=$pg_port;dbname=$pg_dbname;sslmode=require";
    $pdo = new PDO($dsn, $pg_user, $pg_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión a PostgreSQL: " . $e->getMessage());
}

// --- CONEXIÓN MONGODB ATLAS ---
$mongo_uri = getenv('MONGO_URI') ?: 'mongodb://localhost:27017';

try {
    $mongoClient = new MongoDB\Client($mongo_uri);
    // Seleccionamos la base de datos y la colección
    $mongoDB = $mongoClient->taller_servidores;
    $coleccionEstudiantes = $mongoDB->estudiantes;
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}
?>
