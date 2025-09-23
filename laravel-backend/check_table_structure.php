<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');
    
    echo "KARENDERIAS TABLE STRUCTURE:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE karenderias');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }
    
    echo PHP_EOL . "USERS TABLE STRUCTURE:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE users');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }
    
    echo PHP_EOL . "ALICA'S KARENDERIA RECORD:" . PHP_EOL;
    $stmt = $pdo->query("SELECT * FROM karenderias WHERE email = 'alica@gmail.com'");
    $karenderia = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($karenderia) {
        foreach ($karenderia as $key => $value) {
            echo "$key: $value" . PHP_EOL;
        }
    } else {
        echo "No karenderia found for alica@gmail.com" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}