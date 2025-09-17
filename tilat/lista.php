<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Tilat");

try {
    $sql_lause = "SELECT * FROM tilat WHERE id != 0";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->execute();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}
$tulos = $kysely->fetchAll();
?>

<p><a href="../index.php" class="btn">⬅ Takaisin etusivulle</a></p>
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
<?php renderFooter(); ?>

