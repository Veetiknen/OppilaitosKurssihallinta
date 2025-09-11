<?php
// template.php
function renderHeader($title = "Kouluprojekti") {
    ?>
<!DOCTYPE html>
<html lang="fi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($title) ?></title>
<style>
    body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f8; color: #333; }
    a { text-decoration: none; color: #0070c0; } a:hover { text-decoration: underline; }
    nav { background-color: #dde6f1; width: 220px; height: 100vh; position: fixed; top: 0; left: 0; padding-top: 60px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
    nav ul { list-style: none; padding: 0; }
    nav li { padding: 10px 20px; }
    nav li:hover { background-color: #c7d9f0; }
    main { margin-left: 240px; padding: 20px; }
    table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #e2e9f7; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .btn { background-color: #0070c0; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
    .btn:hover { background-color: #005a8f; }
    .warning { color: red; font-weight: bold; }
    form input, form select { padding: 5px; margin-bottom: 10px; width: 300px; }
</style>
</head>
<body>

<nav>
    <ul>
        <li><a href="../index.php">Etusivu</a></li>
        <li><a href="../opettajat/lista.php">Opettajat</a></li>
        <li><a href="../opiskelijat/lista.php">Opiskelijat</a></li>
        <li><a href="../kurssit/lista.php">Kurssit</a></li>
        <li><a href="../tilat/lista.php">Tilat</a></li>
    </ul>
</nav>
<main>
<h1><?= htmlspecialchars($title) ?></h1>
<?php
}

function renderFooter() {
    ?>
</main>
</body>
</html>
<?php
}
