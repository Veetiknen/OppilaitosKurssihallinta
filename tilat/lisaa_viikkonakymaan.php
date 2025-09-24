<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['tilat'])) {
    die("Tilaa ei valittu");
}

$tila_id = (int)$_GET['tilat'];

// Haetaan tila
$sql = "SELECT id, nimi, kapasiteetti FROM tilat WHERE id = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$tila_id]);
$tila = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tila) {
    die("Tilaa ei löytynyt");
}

// --- Sessioiden poisto ---
if (isset($_GET['poista'])) {
    $poista_id = (int)$_GET['poista'];
    $sql = "DELETE s FROM kurssisessiot s
            JOIN kurssit k ON s.kurssi_id = k.id
            WHERE s.id = ? AND k.tila = ?";
    $stmt = $yhteys->prepare($sql);
    $stmt->execute([$poista_id, $tila_id]);
    header("Location: lisaa_viikkonakymaan.php?tilat=" . $tila_id);
    exit;
}

renderHeader("Lisää sessio - " . htmlspecialchars($tila['nimi']));

$viikonpaivat = ['ma' => 'Maanantai', 'ti' => 'Tiistai', 'ke' => 'Keskiviikko', 'to' => 'Torstai', 'pe' => 'Perjantai'];

// Haetaan tämän tilan kurssit lomakkeeseen
$sql = "SELECT id, nimi FROM kurssit WHERE tila = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$tila_id]);
$kurssit = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lomakkeen käsittely
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lisaa'])) {
    $kurssi_id = (int)$_POST['kurssi_id'];
    $viikonpaiva = $_POST['viikonpaiva'];
    $aloitus = (int)$_POST['aloitus'];
    $lopetus = (int)$_POST['lopetus'];

    if (!in_array($viikonpaiva, array_keys($viikonpaivat))) {
        echo "<p class='warning'>Virheellinen viikonpäivä.</p>";
    } elseif ($aloitus >= $lopetus) {
        echo "<p class='warning'>Lopetusajan täytyy olla myöhemmin kuin aloitusajan.</p>";
    } else {
        $sql = "INSERT INTO kurssisessiot (kurssi_id, viikonpaiva, aloitus, lopetus) VALUES (?, ?, ?, ?)";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([$kurssi_id, $viikonpaiva, $aloitus, $lopetus]);
        echo "<p class='success'>Sessio lisätty onnistuneesti!</p>";
    }
}

// Nykyiset sessiot
$sql = "SELECT s.id, s.viikonpaiva, s.aloitus, s.lopetus, k.nimi AS kurssi_nimi, o.etunimi, o.sukunimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        JOIN opettajat o ON k.opettaja = o.tunnusnumero
        WHERE k.tila = ?
        ORDER BY FIELD(s.viikonpaiva,'ma','ti','ke','to','pe'), s.aloitus";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$tila_id]);
$sessiot = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post" class="session-form">
    <label>Kurssi:
        <select name="kurssi_id" required>
            <?php foreach ($kurssit as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nimi']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Viikonpäivä:
        <select name="viikonpaiva" required>
            <option value="">Valitse</option>
            <?php foreach ($viikonpaivat as $key => $nimi): ?>
                <option value="<?= $key ?>"><?= $nimi ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Aloitus:
        <select name="aloitus" required>
            <?php for ($h = 8; $h <= 16; $h++): ?>
                <option value="<?= $h ?>"><?= $h ?>:00</option>
            <?php endfor; ?>
        </select>
    </label>

    <label>Lopetus:
        <select name="lopetus" required>
            <?php for ($h = 9; $h <= 17; $h++): ?>
                <option value="<?= $h ?>"><?= $h ?>:00</option>
            <?php endfor; ?>
        </select>
    </label>

    <button type="submit" name="lisaa" class="btn">Lisää sessio</button>
</form>

<h2>Nykyiset sessiot tilassa <?= htmlspecialchars($tila['nimi']) ?></h2>
<table class="sessions-table">
    <thead>
        <tr>
            <th>Kurssi</th>
            <th>Viikonpäivä</th>
            <th>Aika</th>
            <th>Opettaja</th>
            <th>Toiminnot</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($sessiot)): ?>
            <tr><td colspan="5">Ei sessioita</td></tr>
        <?php else: ?>
            <?php foreach ($sessiot as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['kurssi_nimi']) ?></td>
                    <td><?= $viikonpaivat[$s['viikonpaiva']] ?? $s['viikonpaiva'] ?></td>
                    <td><?= sprintf('%02d:00 - %02d:00', $s['aloitus'], $s['lopetus']) ?></td>
                    <td><?= htmlspecialchars($s['etunimi'] . ' ' . $s['sukunimi']) ?></td>
                    <td>
                        <a href="lisaa_viikkonakymaan.php?tilat=<?= $tila_id ?>&poista=<?= $s['id'] ?>" 
                           class="btn" 
                           onclick="return confirm('Poistetaanko tämä sessio?')">Poista</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<a href="viikkonakyma.php?tilat=<?= $tila_id ?>" class="btn back-link">&laquo; Takaisin viikkonäkymään</a>

<?php renderFooter(); ?>
