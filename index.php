<?php
// index.php
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Opintohallinta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            /* Taustakuva ja sen asetukset */
            background-image: url('https://emojimix.app/italian/1_13.jpg');
            background-size: cover; /* Peittää koko taustan */
            background-repeat: no-repeat;
            background-position: center center;
            /* Halutessasi voit lisätä myös varjon tai taustavärin */
            color: #fff; /* Esim. valkoinen teksti, koska taustakuva on värikäs */
        }
        h1 { color: #f0f0f0; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #a0d8f0; font-weight: bold; }
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
