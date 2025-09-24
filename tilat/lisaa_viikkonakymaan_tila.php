<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['tilat'])) {
    die("Tilaa ei valittu");
}

$tila_id = (int)$_GET['tilat'];

// Haetaan tila
$sql = "SELECT id, nimi FROM tilat WHERE id = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$tila_id]);
$tila = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tila) {
    die("Tilaa ei löytynyt");
}

renderHeader("Lisää viikkonäkymään - " . htmlspecialchars($tila['nimi']));

// Jos lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kurssi_id = (int)$_POST['kurssi_id'];
    $viikonpaiva = $_POST['viikonpaiva'];
    $aloitus = (int)$_POST['aloitus'];
    $lopetus = (int)$_POST['lopetus'];

    if ($aloitus >= $lopetus) {
        echo "<p style='color:red;'>Lopetusajan täytyy olla myöhemmin kuin aloitusajan.</p>";
    } else {
        $sql = "INSERT INTO kurssisessiot (kurssi_id, viikonpaiva, aloitus, lopetus) VALUES (?, ?, ?, ?)";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([$kurssi_id, $viikonpaiva, $aloitus, $lopetus]);
        echo "<p style='color:green;'>Sessio lisätty onnistuneesti!</p>";
    }
}

// Haetaan tämän tilan kurssit
$sql = "SELECT id, nimi FROM kurssit WHERE tila = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$tila_id]);
$kurssit = $stmt->fetchAll(PDO::FETCH_ASSOC);

$viikonpaivat = [
    'ma' => 'Maanantai',
    'ti' => 'Tiistai',
    'ke' => 'Keskiviikko',
    'to' => 'Torstai',
    'pe' => 'Perjantai'
];
?>

<h2>Lisää sessio tilaan: <?= htmlspecialchars($tila['nimi']) ?></h2>

<form method="post">
    <div>
        <label>Kurssi:</label><br>
        <select name="kurssi_id" required>
            <?php foreach ($kurssit as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nimi']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label>Viikonpäivä:</label><br>
        <select name="viikonpaiva" required>
            <?php foreach ($viikonpaivat as $key => $nimi): ?>
                <option value="<?= $key ?>"><?= $nimi ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label>Aloitus:</label><br>
        <select name="aloitus" required>
            <?php for ($h = 8; $h <= 16; $h++): ?>
                <option value="<?= $h ?>"><?= sprintf('%02d:00', $h) ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div>
        <label>Lopetus:</label><br>
        <select name="lopetus" required>
            <?php for ($h = 9; $h <= 17; $h++): ?>
                <option value="<?= $h ?>"><?= sprintf('%02d:00', $h) ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div style="margin-top: 10px;">
        <button type="submit" class="btn">Lisää sessio</button>
    </div>
</form>

<a href="viikkonakyma.php?tilat=<?= $tila_id ?>" class="btn" style="margin-top: 20px;">&laquo; Takaisin viikkonäkymään</a>

<?php renderFooter(); ?>
