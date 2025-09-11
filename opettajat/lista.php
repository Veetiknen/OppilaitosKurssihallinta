<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Opettajat");

$sql = "SELECT * FROM opettajat WHERE tunnusnumero != 0";
$kysely = $yhteys->prepare($sql);
$kysely->execute();
$opettajat = $kysely->fetchAll();
?>

<p><a class="btn" href="lisaa.php">Lisää Opettaja</a></p>

<table>
<tr>
    <th>Tunnusnumero</th>
    <th>Nimi</th>
    <th>Aine</th>
    <th>Toiminnot</th>
</tr>
<?php foreach($opettajat as $o): ?>
<tr>
    <td><?= $o['tunnusnumero'] ?></td>
    <td><?= htmlspecialchars($o['etunimi'].' '.$o['sukunimi']) ?></td>
    <td><?= htmlspecialchars($o['aine']) ?></td>
    <td>
        <a class="btn" href="muokkaa.php?id=<?= $o['tunnusnumero'] ?>">Muokkaa</a>
        <a class="btn" href="poista.php?id=<?= $o['tunnusnumero'] ?>" onclick="return confirm('Haluatko varmasti poistaa?')">Poista</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php renderFooter(); ?>
