<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Opiskelijat");

$sql = "SELECT * FROM opiskelijat WHERE opiskelija_numero != 0";
$kysely = $yhteys->prepare($sql);
$kysely->execute();
$opiskelijat = $kysely->fetchAll();
?>

<p><a href="../index.html" class="btn">⬅ Takaisin etusivulle</a></p>
<p><a class="btn" href="lisaa.php">Lisää Opiskelija</a></p>

<table>
<tr>
    <th>Tunnusnumero</th>
    <th>Nimi</th>
    <th>Toiminnot</th>
</tr>
<?php foreach($opiskelijat as $o): ?>
<tr>
    <td><?= $o['opiskelija_numero'] ?></td>
    <td><?= htmlspecialchars($o['etunimi'].' '.$o['sukunimi']) ?></td>
    <td>
        <a class="btn" href="nayta.php?id=<?= $o['opiskelija_numero'] ?>">Näytä</a>
        <a class="btn" href="muokkaa.php?id=<?= $o['opiskelija_numero'] ?>">Muokkaa</a>
        <a class="btn" href="poista.php?id=<?= $o['opiskelija_numero'] ?>" onclick="return confirm('Haluatko varmasti poistaa tämän opiskelijan?')">Poista</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php renderFooter(); ?>
