<?php
// Requerir el autoloader de Composer para MongoDB
require_once __DIR__ . '/vendor/autoload.php';

// --- CONEXIÓN POSTGRESQL (CON LA URL EXACTA DE TU CAPTURA) ---
$render_db_url = 'postgresql://taller5_db_user:B0XnABjx8W5EmahyWTLdrwWc9XBPQilI@dpg-d8f2ho58nd3s73fgjbu0-a.oregon-postgres.render.com/taller5_db';

try {
    $dbparts = parse_url($render_db_url);
    
    $host = $dbparts['host'];
    $dbname = ltrim($dbparts['path'], '/');
    $user = $dbparts['user'];
    $pass = $dbparts['pass'];

    // Armamos la conexión segura obligatoria para Render
    $dsn = "pgsql:host=$host;port=5432;dbname=$dbname;sslmode=require";
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Error de conexión a PostgreSQL: " . $e->getMessage());
}

// --- CONEXIÓN MONGODB ATLAS ---
$mongo_uri = 'mongodb+srv://johancardenas619_db_user:jsYUeGjOjpsixQq2@cluster2.z9s2amn.mongodb.net/?appName=Cluster2';

try {
    $mongoClient = new MongoDB\Client($mongo_uri);
    $mongoDB = $mongoClient->taller_servidores;
    $coleccionEstudiantes = $mongoDB->estudiantes;
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}
?>
