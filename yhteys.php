<?php
// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
  
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
                
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

try {
    // Load .env file
    loadEnv('.env');
    
    $servername = 'localhost';
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME']
    
    if (!$username || !$password) {
        throw new Exception('Database credentials not found in .env file');
    }
    
    $yhteys = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Yhteys muodostettu<br>";
    
} catch(PDOException $e) {
    echo "Ei yhteyttä tietokantaan!<br> " . $e->getMessage();
} catch(Exception $e) {
    echo "Virhe: " . $e->getMessage();
}
?>