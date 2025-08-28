<?php
require '../yhteys.php';


$sql_lause = "SELECT 
    k.id,
    k.nimi,
    k.kuvaus,
    k.alkupäivä,
    k.loppupäivä,
    CONCAT(o.etunimi, ' ', o.sukunimi) AS opettaja_nimi,
    t.nimi AS tila_nimi,
    GROUP_CONCAT(CONCAT(os.etunimi, ' ', os.sukunimi, ' (', os.vuosikurssi, ')') SEPARATOR ', ') AS opiskelijat
FROM kurssit k
JOIN opettajat o ON k.opettaja = o.tunnusnumero
JOIN tilat t ON k.tila = t.id
LEFT JOIN kurssikirjautumisilla kk ON kk.kurssi = k.id
LEFT JOIN opiskelijat os ON kk.opiskelija = os.opiskelija_numero
GROUP BY k.id, k.nimi, k.kuvaus, k.alkupäivä, k.loppupäivä, opettaja_nimi, tila_nimi
";



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
    <title>Kurssit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 90%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; color: #2980b9; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<h2>Kurssit</h2>
<p><a href="lisaa.php">Lisää kurssi</a></p>

<table>
<tr>
    <th>Nimi</th>
    <th>Kuvaus</th>
    <th>Opettaja</th>
    <th>Opiskelija (Vuosikurssi)</th>
    <th>Tila</th>
    <th>Alku</th>
    <th>Loppu</th>
    <th>Toiminnot</th>
</tr>

<?php foreach($tulos as $rivi): ?>
<tr>
    <td><?= htmlspecialchars($rivi['nimi']) ?></td>
    <td><?= htmlspecialchars($rivi['kuvaus']) ?></td>
    <td><?= htmlspecialchars($rivi['opettaja_nimi']) ?></td>
    <td><?= htmlspecialchars($rivi['opiskelijat'] ?? '') ?></td>
    <td><?= htmlspecialchars($rivi['tila_nimi']) ?></td>
    <td><?= $rivi['alkupäivä'] ?></td>
    <td><?= $rivi['loppupäivä'] ?></td>
    <td>
        <a href="muokkaa.php?id=<?= $rivi['id'] ?>">Muokkaa</a> |
        <a href="poista.php?id=<?= $rivi['id'] ?>" onclick="return confirm('Haluatko varmasti poistaa tämän kurssin?');">Poista</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>