<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Kurssit");

$sql = "SELECT id, nimi, kuvaus FROM kurssit";
$kysely = $yhteys->prepare($sql);
$kysely->execute();
$kurssit = $kysely->fetchAll();
?>

<p><a class="btn" href="lisaa.php">Lisää Kurssi</a></p>

<table>
    <tr>
        <th>Nimi</th>
        <th>Kuvaus</th>
        <th>Toiminnot</th>
    </tr>
    <?php foreach($kurssit as $kurssi): ?>
    <tr>
        <td><?= htmlspecialchars($kurssi['nimi']) ?></td>
        <td><?= htmlspecialchars($kurssi['kuvaus']) ?></td>
        <td>
            <a class="btn" href="nayta.php?id=<?= $kurssi['id'] ?>">Näytä</a>
            <a class="btn" href="muokkaa.php?id=<?= $kurssi['id'] ?>">Muokkaa</a>
            <a class="btn" href="poista.php?id=<?= $kurssi['id'] ?>" 
               onclick="return confirm('Haluatko varmasti poistaa tämän kurssin?');">
               Poista
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php renderFooter(); ?>
