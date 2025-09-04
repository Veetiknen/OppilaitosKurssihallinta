<?php
require '../yhteys.php';

$sql_lause = "SELECT id, nimi, kuvaus FROM kurssit";

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
        table { border-collapse: collapse; width: 70%; }
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
    <th>Toiminnot</th>
</tr>

<?php foreach($tulos as $rivi): ?>
<tr>
    <td><?= htmlspecialchars($rivi['nimi']) ?></td>
    <td><?= htmlspecialchars($rivi['kuvaus']) ?></td>
    <td>
        <a href="nayta.php?id=<?= $rivi['id'] ?>">Näytä</a> |
        <a href="muokkaa.php?id=<?= $rivi['id'] ?>">Muokkaa</a> |
        <a href="poista.php?id=<?= $rivi['id'] ?>" onclick="return confirm('Haluatko varmasti poistaa tämän kurssin?');">Poista</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
