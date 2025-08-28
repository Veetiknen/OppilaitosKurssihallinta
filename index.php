<?php
// index.php
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Opintohallinta</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        h1 { color: #2c3e50; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #2980b9; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Opintohallinta - Etusivu</h1>
    <p>Valitse alla oleva linkki hallinnoitavaan kohteeseen:</p>
    <ul>
        <li><a href="opettajat/lista.php">Opettajat</a></li>
        <li><a href="opiskelijat/lista.php">Opiskelijat</a></li>
        <li><a href="kurssit/lista.php">Kurssit</a></li>
        <li><a href="tilat/lista.php">Tilat</a></li>
    </ul>
</body>
</html>
