<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['opettajat'])) {
    die("Opettajaa ei valittu");
}

$opettaja_id = (int)$_GET['opettajat'];

// Haetaan opettajan tiedot
$sql_lause = "SELECT etunimi, sukunimi, aine FROM opettajat WHERE tunnusnumero = ?";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$opettaja_id]);
$opettaja = $kysely->fetch();
if (!$opettaja) {
    die("Opettajaa ei löytynyt");
}

// --- Uuden session lisääminen ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lisaa'])) {
    $kurssi_id = (int)$_POST['kurssi_id'];
    $viikonpaiva = $_POST['viikonpaiva'] ?? '';
    $aloitus = (int)$_POST['aloitus'];
    $lopetus = (int)$_POST['lopetus'];

    // Tarkistetaan että kurssi kuuluu opettajalle
    $kysely = $yhteys->prepare("SELECT id FROM kurssit WHERE id=? AND opettaja=?");
    $kysely->execute([$kurssi_id, $opettaja_id]);
    if ($kysely->fetch() && in_array($viikonpaiva, ['ma','ti','ke','to','pe']) && $aloitus < $lopetus) {
        $kysely = $yhteys->prepare("INSERT INTO kurssisessiot (kurssi_id, viikonpaiva, aloitus, lopetus) VALUES (?,?,?,?)");
        $kysely->execute([$kurssi_id, $viikonpaiva, $aloitus, $lopetus]);
    }
}

// --- Session poistaminen ---
if (isset($_GET['poista'])) {
    $poista_id = (int)$_GET['poista'];
    $kysely = $yhteys->prepare("DELETE s FROM kurssisessiot s
                              JOIN kurssit k ON s.kurssi_id = k.id
                              WHERE s.id=? AND k.opettaja=?");
    $kysely->execute([$poista_id, $opettaja_id]);
    header("Location: lisaa_viikkonakymaan.php?opettajat=".$opettaja_id);
    exit;
}

// --- Haetaan opettajan kurssit ---
$sql_lause = "SELECT id, nimi FROM kurssit WHERE opettaja=? ORDER BY nimi";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$opettaja_id]);
$kurssit = $kysely->fetchAll(PDO::FETCH_ASSOC);

// --- Haetaan opettajan kurssien sessiot ---
$sql_lause = "SELECT s.id, s.viikonpaiva, s.aloitus, s.lopetus, k.nimi AS kurssi_nimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        WHERE k.opettaja=?
        ORDER BY FIELD(s.viikonpaiva,'ma','ti','ke','to','pe'), s.aloitus";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$opettaja_id]);
$sessiot = $kysely->fetchAll(PDO::FETCH_ASSOC);

renderHeader("Sessioiden hallinta – " . htmlspecialchars($opettaja['etunimi']." ".$opettaja['sukunimi']));
?>

<h2>Sessioiden hallinta – <?= htmlspecialchars($opettaja['etunimi']." ".$opettaja['sukunimi']) ?></h2>
<p><strong>Aine:</strong> <?= htmlspecialchars($opettaja['aine']) ?></p>

<h3>Lisää uusi sessio</h3>
<form method="post" style="margin-bottom:20px;">
    <label>Kurssi:
        <select name="kurssi_id" required>
            <option value="">Valitse kurssi</option>
            <?php foreach($kurssit as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nimi']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
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
        <th style="border:1px solid #ddd;padding:5px;">Kurssi</th>
        <th style="border:1px solid #ddd;padding:5px;">Päivä</th>
        <th style="border:1px solid #ddd;padding:5px;">Aika</th>
        <th style="border:1px solid #ddd;padding:5px;">Toiminnot</th>
    </tr>
    <?php foreach($sessiot as $s): ?>
    <tr>
        <td style="border:1px solid #ddd;padding:5px;"><?= htmlspecialchars($s['kurssi_nimi']) ?></td>
        <td style="border:1px solid #ddd;padding:5px;"><?= htmlspecialchars($s['viikonpaiva']) ?></td>
        <td style="border:1px solid #ddd;padding:5px;"><?= $s['aloitus'] ?>:00 - <?= $s['lopetus'] ?>:00</td>
        <td style="border:1px solid #ddd;padding:5px;">
            <a href="lisaa_viikkonakymaan.php?opettajat=<?= $opettaja_id ?>&poista=<?= $s['id'] ?>" class="btn"
               onclick="return confirm('Poistetaanko tämä sessio?')">Poista</a>
        </td>
    </tr>
    <?php endforeach; ?> 
</table>

<a href="viikkonakyma.php?opettajat=<?= $opettaja_id ?>" class="btn" style="margin-top:20px; display:inline-block;">Takaisin viikkonäkymään</a>

<?php renderFooter(); ?>
