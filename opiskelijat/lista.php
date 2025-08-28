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
<body>
<h2>Opiskelijat</h2>
<p><a href="lisaa.php">Lisää opiskelija</a></p>

<table>
<tr>
    <th>Tunnusnumero</th>
    <th>Nimi</th>
    <th>Syntymäpäivä</th>
    <th>Vuosikurssi</th>
    <th>Toiminnot</th>
</tr>

<?php foreach($tulos as $rivi): ?>
<tr>
    <td><?php echo htmlspecialchars($rivi['opiskelija_numero']); ?></td>
    <td><?php echo htmlspecialchars($rivi['etunimi'] . ' ' . $rivi['sukunimi']); ?></td>
    <td><?php echo htmlspecialchars($rivi['syntymäpäivä']); ?></td>
    <td><?php echo htmlspecialchars($rivi['vuosikurssi']); ?></td>
    <td>
        <a href="muokkaa.php?id=<?php echo $rivi['opiskelija_numero']; ?>">Muokkaa</a> |
        <a href="poista.php?id=<?php echo $rivi['opiskelija_numero']; ?>" onclick="return confirm('Haluatko varmasti poistaa tämän opiskelijan?');">Poista</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
</body>
</html>