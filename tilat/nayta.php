<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Tilan ID puuttuu.");
}

$tila_id = (int)$_GET['id'];

// Hae tilan tiedot
try {
    $sql_lause = "SELECT * FROM tilat WHERE id = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $tila_id, PDO::PARAM_INT);
    $kysely->execute();
    $tila = $kysely->fetch();
    if (!$tila) {
        die("Tila ei löytynyt.");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Hae kurssit jotka pidetään tässä tilassa
try {
    $sql = "SELECT k.id, k.nimi, k.alkupäivä, k.loppupäivä,
                   CONCAT(o.etunimi, ' ', o.sukunimi) AS opettaja,
                   COUNT(ck.opiskelija) AS osallistujia
            FROM kurssit k
            JOIN opettajat o ON k.opettaja = o.tunnusnumero
            LEFT JOIN kurssikirjautumisilla ck ON k.id = ck.kurssi
            WHERE k.tila = :tila_id
            GROUP BY k.id, k.nimi, k.alkupäivä, k.loppupäivä, o.etunimi, o.sukunimi";
    $kysely = $yhteys->prepare($sql);
    $kysely->bindParam(':tila_id', $tila_id, PDO::PARAM_INT);
    $kysely->execute();
    $kurssit = $kysely->fetchAll();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Tarkistetaan onko ylityksiä
$onkoYlityksia = false;
foreach ($kurssit as $k) {
    if ($k['osallistujia'] > $tila['kapasiteetti']) {
        $onkoYlityksia = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tila: <?= htmlspecialchars($tila['nimi']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        h2, h3 {
            margin-top: 0;
        }

        a {
            text-decoration: none;
            color: #0070c0;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #e2e9f7;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .warning {
            color: #d8000c;
            background-color: #ffd2d2;
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            background-color: #0070c0;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #005a8f;
        }

        .info-pair {
            margin-bottom: 10px;
        }

        .info-pair strong {
            display: inline-block;
            width: 120px;
        }
    </style>
</head>
<body>

<main>
    <div class="card">
        <h2>Tila: <?= htmlspecialchars($tila['nimi']) ?></h2>
        <div class="info-pair"><strong>ID:</strong> <?= htmlspecialchars($tila['id']) ?></div>
        <div class="info-pair"><strong>Kapasiteetti:</strong> <?= htmlspecialchars($tila['kapasiteetti']) ?></div>
    </div>

    <div class="card">
        <h3>Kurssit tässä tilassa</h3>

        <?php if (count($kurssit) > 0): ?>
        <table>
            <tr>
                <th>Nimi</th>
                <th>Opettaja</th>
                <th>Alkupäivä</th>
                <th>Loppupäivä</th>
                <th>Osallistujat</th>
                <?php if ($onkoYlityksia): ?>
                    <th>Huomio</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($kurssit as $k): ?>
            <tr>
                <td><?= htmlspecialchars($k['nimi']) ?></td>
                <td><?= htmlspecialchars($k['opettaja']) ?></td>
                <td><?= htmlspecialchars($k['alkupäivä']) ?></td>
                <td><?= htmlspecialchars($k['loppupäivä']) ?></td>
                <td><?= htmlspecialchars($k['osallistujia']) ?></td>
                <?php if ($onkoYlityksia): ?>
                    <td>
                        <?php if ($k['osallistujia'] > $tila['kapasiteetti']): ?>
                            <span class="warning">Ylittää kapasiteetin!</span>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Ei kursseja tässä tilassa.</p>
        <?php endif; ?>

        <a href="lista.php" class="btn">⬅ Takaisin tila listaan</a>
        <a href="viikkonakyma.php?tilat=<?= $tila['id'] ?>" class="btn">📅 Näytä viikkonäkymä</a>
    </div>
</main>

</body>
</html>
