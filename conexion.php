<?php
// Requerir el autoloader de Composer para MongoDB
require_once __DIR__ . '/vendor/autoload.php';

// --- CONEXIÓN POSTGRESQL ---
$pg_host = 'dpg-d8f2ho58nd3s73fgjbu0-a.oregon-postgres.render.com';
$pg_port = '5432';
$pg_dbname = 'taller5_db';
$pg_user = 'taller5_db_user';
// Contraseña directa con el número 1 al final
$pg_password = 'B0XnABjx8W5EmahyWTLdrwWc9XBPQi1I';

try {
    // Se incluye sslmode=require directamente en el DSN
    $dsn = "pgsql:host=$pg_host;port=$pg_port;dbname=$pg_dbname;sslmode=require";
    $pdo = new PDO($dsn, $pg_user, $pg_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión a PostgreSQL: " . $e->getMessage());
}

// --- CONEXIÓN MONGODB ATLAS ---
// Enlace directo de tu Cluster2
$mongo_uri = 'mongodb+srv://johancardenas619_db_user:jsYUeGjOjpsixQq2@cluster2.z9s2amn.mongodb.net/?appName=Cluster2';

try {
    $mongoClient = new MongoDB\Client($mongo_uri);
    // Seleccionamos la base de datos y la colección
    $mongoDB = $mongoClient->taller_servidores;
    $coleccionEstudiantes = $mongoDB->estudiantes;
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}
?>
