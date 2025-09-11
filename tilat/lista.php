<?php
require '../yhteys.php';

$sql_lause = "SELECT * FROM tilat WHERE id != 0";

try {
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->execute();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}
$tulos = $kysely->fetchAll();
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tilat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        a {
            text-decoration: none;
            color: #0070c0;
        }

        a:hover {
            text-decoration: underline;
        }

        nav {
            background-color: #dde6f1;
            width: 220px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav li {
            padding: 10px 20px;
        }

        nav li:hover {
            background-color: #c7d9f0;
        }

        main {
            margin-left: 240px;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #e2e9f7;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            background-color: #0070c0;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }

        .btn:hover {
            background-color: #005a8f;
        }

        .top-link {
            display: inline-block;
            margin: 20px 0;
        }
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
    <p class="top-link">
        <a href="../index.php" class="btn">⬅ Takaisin etusivulle</a>
    </p>

    <h2>Tilat</h2>
    <p><a href="lisaa.php" class="btn">Lisää Tila</a></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Nimi</th>
            <th>Toiminnot</th>
        </tr>

        <?php foreach ($tulos as $rivi): ?>
            <tr>
                <td><?php echo htmlspecialchars($rivi['id']); ?></td>
                <td><?php echo htmlspecialchars($rivi['nimi']); ?></td>
                <td>
                    <a href="nayta.php?id=<?php echo $rivi['id']; ?>">Näytä</a> |
                    <a href="muokkaa.php?id=<?php echo $rivi['id']; ?>">Muokkaa</a> |
                    <a href="poista.php?id=<?php echo $rivi['id']; ?>"
                       onclick="return confirm('Haluatko varmasti poistaa tämän tilan?');">Poista</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

</body>
</html>
