<?php
require '../yhteys.php';

$sql_lause = "SELECT * FROM opiskelijat";

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
    <title>Opiskelijat</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 80%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; color: #2980b9; }
        a:hover { text-decoration: underline; }
    </style>
</head>

<p>
    <a href="../index.php" 
       style="display:inline-block; padding:8px 16px; background:#2980b9; color:#fff; text-decoration:none; border-radius:5px;">
       ⬅ Takaisin etusivulle
    </a>
</p>

<body>
<h2>Opiskelijat</h2>
<p><a href="lisaa.php">Lisää opiskelija</a></p>

<table>
<tr>
    <th>Tunnusnumero</th>
    <th>Nimi</th>
    <th>Toiminnot</th>
</tr>

<?php foreach($tulos as $rivi): ?>
<tr>
    <td><?= htmlspecialchars($rivi['opiskelija_numero']) ?></td>
    <td><?= htmlspecialchars($rivi['etunimi'] . ' ' . $rivi['sukunimi']) ?></td>
    <td>
        <a href="nayta.php?id=<?= $rivi['opiskelija_numero'] ?>">Näytä</a> |
        <a href="muokkaa.php?id=<?= $rivi['opiskelija_numero'] ?>">Muokkaa</a> |
        <a href="poista.php?id=<?= $rivi['opiskelija_numero'] ?>" 
           onclick="return confirm('Haluatko varmasti poistaa tämän opiskelijan?');">Poista</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
</body>
</html>
