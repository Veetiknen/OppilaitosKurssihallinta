<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['kurssi'])) {
    die("Kurssia ei valittu");
}

$kurssi_id = (int)$_GET['kurssi'];

// Kurssin nimi
$sql_lause = "SELECT nimi FROM kurssit WHERE id=?";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$kurssi_id]);
$kurssi = $kysely->fetch();
if (!$kurssi) die("Kurssia ei löytynyt");

// --- Uuden session lisääminen ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lisaa'])) {
    $viikonpaiva = $_POST['viikonpaiva'] ?? '';
    $aloitus = (int)$_POST['aloitus'];
    $lopetus = (int)$_POST['lopetus'];

    if (in_array($viikonpaiva, ['ma','ti','ke','to','pe']) && $aloitus < $lopetus) {
        $kysely = $yhteys->prepare("INSERT INTO kurssisessiot (kurssi_id, viikonpaiva, aloitus, lopetus) VALUES (?,?,?,?)");
        $kysely->execute([$kurssi_id, $viikonpaiva, $aloitus, $lopetus]);
    }
}

// --- Session poistaminen ---
if (isset($_GET['poista']) && isset($_GET['kurssi'])) {
    $poista_id = (int)$_GET['poista'];
    $kurssi_id = (int)$_GET['kurssi'];

    $kysely = $yhteys->prepare("DELETE s FROM kurssisessiot s JOIN kurssit k ON s.kurssi_id = k.id WHERE s.id=? AND k.id=?");
    $kysely->execute([$poista_id, $kurssi_id]);
    header("Location: viikkonakyma.php?kurssi=" . $kurssi_id);
    exit;
}


// --- Hae nykyiset sessiot ---
$kysely = $yhteys->prepare("SELECT * FROM kurssisessiot WHERE kurssi_id=? ORDER BY 
    FIELD(viikonpaiva,'ma','ti','ke','to','pe'), aloitus");
$kysely->execute([$kurssi_id]);
$sessiot = $kysely->fetchAll(PDO::FETCH_ASSOC);

renderHeader("Aikataulu: " . htmlspecialchars($kurssi['nimi']));
?>

<h2>Aikataulu – <?= htmlspecialchars($kurssi['nimi']) ?></h2>

<h3>Lisää uusi sessio</h3>
<form method="post" style="margin-bottom:20px;">
    <label>Viikonpäivä:
        <select name="viikonpaiva" required>
            <option value="">Valitse</option>
            <option value="ma">Maanantai</option>
            <option value="ti">Tiistai</option>
            <option value="ke">Keskiviikko</option>
            <option value="to">Torstai</option>
            <option value="pe">Perjantai</option>
        </select>
    </label>
    <label>Aloitus:
        <select name="aloitus" required>
            <?php for($i=8;$i<=16;$i++): ?>
                <option value="<?= $i ?>"><?= $i ?>:00</option>
            <?php endfor; ?>
        </select>
    </label>
    <label>Lopetus:
        <select name="lopetus" required>
            <?php for($i=9;$i<=17;$i++): ?>
                <option value="<?= $i ?>"><?= $i ?>:00</option>
            <?php endfor; ?>
        </select>
    </label>
    <button type="submit" name="lisaa" class="btn">Lisää</button>
</form>

<h3>Nykyiset sessiot</h3>
<table style="border-collapse: collapse; width: 100%;">
    <tr>
        <th style="border:1px solid #ddd;padding:5px;">Päivä</th>
        <th style="border:1px solid #ddd;padding:5px;">Aika</th>
        <th style="border:1px solid #ddd;padding:5px;">Toiminnot</th>
    </tr>
    <?php foreach($sessiot as $s): ?>
    <tr>
        <td style="border:1px solid #ddd;padding:5px;"><?= htmlspecialchars($s['viikonpaiva']) ?></td>
        <td style="border:1px solid #ddd;padding:5px;"><?= $s['aloitus'] ?>:00 - <?= $s['lopetus'] ?>:00</td>
        <td style="border:1px solid #ddd;padding:5px;">
            <a href="lisaa_viikkonakymaan.php?kurssi=<?= $kurssi_id ?>&poista=<?= $s['id'] ?>" class="btn" onclick="return confirm('Poistetaanko tämä sessio?')">Poista</a>
        </td>
    </tr>
    <?php endforeach; ?> 
</table>

<a href="viikkonakyma.php?kurssi=<?= $kurssi_id ?>" class="btn" style="margin-top:20px; display:inline-block;">Takaisin viikkonäkymään</a>

<?php renderFooter(); ?>
