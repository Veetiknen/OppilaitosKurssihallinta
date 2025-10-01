<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['id'])) die("Kurssi ID puuttuu.");
$id = (int)$_GET['id'];

// Haetaan kurssin tiedot
$kysely = $yhteys->prepare("SELECT * FROM kurssit WHERE id = :id");
$kysely->bindParam(':id', $id, PDO::PARAM_INT);
$kysely->execute();
$kurssi = $kysely->fetch();
if (!$kurssi) die("Kurssia ei löytynyt.");

// Päivitys kun lomake lähetetään
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nimi = $_POST['nimi'] ?? '';
    $kuvaus = $_POST['kuvaus'] ?? '';
    $alkupaiva = $_POST['alkupaiva'] ?? '';
    $loppupaiva = $_POST['loppupaiva'] ?? '';
    $opettaja = $_POST['opettaja'] ?? '';
    $tila = $_POST['tila'] ?? '';
    
    if ($nimi && $kuvaus && $alkupaiva && $loppupaiva && $opettaja && $tila) {
        $update = $yhteys->prepare("
        UPDATE kurssit 
        SET nimi = :nimi, kuvaus = :kuvaus, alkupäivä = :alkupaiva, loppupäivä = :loppupaiva, opettaja = :opettaja, tila = :tila 
        WHERE id = :id
        ");
        $update->bindParam(':nimi', $nimi);
        $update->bindParam(':kuvaus', $kuvaus);
        $update->bindParam(':alkupaiva', $alkupaiva);
        $update->bindParam(':loppupaiva', $loppupaiva);
        $update->bindParam(':opettaja', $opettaja);
        $update->bindParam(':tila', $tila);
        $update->bindParam(':id', $id, PDO::PARAM_INT);
        $update->execute();
        
        header("Location: lista.php");
        exit;
        
    } else {
        echo "<p style='color:red'>Kaikki kentät ovat pakollisia.</p>";
    }
}

// Haetaan opettajat ja tilat pudotusvalikoita varten
$opettajat = $yhteys->query("SELECT tunnusnumero, CONCAT(etunimi, ' ', sukunimi) AS nimi FROM opettajat WHERE tunnusnumero != 0")->fetchAll();
$tilat = $yhteys->query("SELECT id, nimi FROM tilat WHERE id !=0")->fetchAll();

renderHeader("Muokkaa kurssia: " . htmlspecialchars($kurssi['nimi']));
?>

<form method="post">
    <label>Kurssin nimi:<br>
        <input type="text" name="nimi" value="<?= htmlspecialchars($kurssi['nimi']) ?>">
    </label><br>

    <label>Kuvaus:<br>
        <input type="text" name="kuvaus" value="<?= htmlspecialchars($kurssi['kuvaus']) ?>">
    </label><br>

    <label>Alkupäivä:<br>
        <input type="date" name="alkupaiva" value="<?= htmlspecialchars($kurssi['alkupäivä']) ?>">
    </label><br>

    <label>Loppupäivä:<br>
        <input type="date" name="loppupaiva" value="<?= htmlspecialchars($kurssi['loppupäivä']) ?>">
    </label><br>

    <label>Opettaja:<br>
        <select name="opettaja">
            <option value="">-- Valitse opettaja --</option>
            <?php foreach ($opettajat as $o): ?>
                <option value="<?= $o['tunnusnumero'] ?>" <?= $kurssi['opettaja'] == $o['tunnusnumero'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($o['nimi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Tila:<br>
        <select name="tila">
            <option value="">-- Valitse tila --</option>
            <?php foreach ($tilat as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $kurssi['tila'] == $t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nimi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <button class="btn" type="submit">Tallenna</button>
</form>

<p><a href="lista.php" class="btn">Takaisin kurssilistaan</a></p>

<?php renderFooter(); ?>
