<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Kurssia ei valittu.");
}

$kurssi_id = (int)$_GET['id'];

// Hae kurssin tiedot
try {
    $sql_lause = "SELECT * FROM kurssit WHERE id = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $kurssi_id, PDO::PARAM_INT);
    $kysely->execute();
    $kurssi = $kysely->fetch();
    if (!$kurssi) {
        die("Kurssia ei löytynyt.");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Hae opettajat ja tilat lomaketta varten
try {
    $opettajat = $yhteys->query("SELECT tunnusnumero, CONCAT(etunimi, ' ', sukunimi) AS nimi FROM opettajat")->fetchAll();
    $tilat = $yhteys->query("SELECT id, nimi FROM tilat")->fetchAll();
} catch (PDOException $e) {
    die("Virhe haettaessa opettajia tai tiloja: " . $e->getMessage());
}

// Käsittele lomakkeen POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nimi = $_POST['nimi'] ?? '';
    $kuvaus = $_POST['kuvaus'] ?? '';
    $alkupaiva = $_POST['alkupaiva'] ?? '';
    $loppupaiva = $_POST['loppupaiva'] ?? '';
    $opettaja = $_POST['opettaja'] ?? '';
    $tila = $_POST['tila'] ?? '';

    if (!empty($nimi) && !empty($kuvaus) && !empty($alkupaiva) && !empty($loppupaiva) && !empty($opettaja) && !empty($tila)) {
        try {
            $sql = "UPDATE kurssit 
                    SET nimi = :nimi, kuvaus = :kuvaus, alkupäivä = :alkupaiva, loppupäivä = :loppupaiva, opettaja = :opettaja, tila = :tila
                    WHERE id = :id";
            $kysely = $yhteys->prepare($sql);
            $kysely->bindParam(':nimi', $nimi);
            $kysely->bindParam(':kuvaus', $kuvaus);
            $kysely->bindParam(':alkupaiva', $alkupaiva);
            $kysely->bindParam(':loppupaiva', $loppupaiva);
            $kysely->bindParam(':opettaja', $opettaja);
            $kysely->bindParam(':tila', $tila);
            $kysely->bindParam(':id', $kurssi_id, PDO::PARAM_INT);
            $kysely->execute();

            header("Location: lista.php");
            exit;

        } catch (PDOException $e) {
            echo "Virhe muokattaessa kurssia: " . $e->getMessage();
        }
    } else {
        echo "Kaikki kentät ovat pakollisia.";
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Muokkaa kurssia</title>
</head>
<body>
<h2>Muokkaa kurssia: <?= htmlspecialchars($kurssi['nimi']) ?></h2>

<form method="post">
    <label>Kurssin nimi:<br><input type="text" name="nimi" value="<?= htmlspecialchars($kurssi['nimi']) ?>"></label><br><br>
    <label>Kuvaus:<br><input type="text" name="kuvaus" value="<?= htmlspecialchars($kurssi['kuvaus']) ?>"></label><br><br>
    <label>Alkupäivä:<br><input type="date" name="alkupaiva" value="<?= htmlspecialchars($kurssi['alkupäivä']) ?>"></label><br><br>
    <label>Loppupäivä:<br><input type="date" name="loppupaiva" value="<?= htmlspecialchars($kurssi['loppupäivä']) ?>"></label><br><br>
    <label>Opettaja:<br>
        <select name="opettaja" required>
            <option value="">-- Valitse opettaja --</option>
            <?php foreach($opettajat as $o): ?>
                <option value="<?= $o['tunnusnumero'] ?>" <?= $kurssi['opettaja'] == $o['tunnusnumero'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($o['nimi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Tila:<br>
        <select name="tila" required>
            <option value="">-- Valitse tila --</option>
            <?php foreach($tilat as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $kurssi['tila'] == $t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nimi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <button type="submit">Tallenna muutokset</button>
</form>

<p><a href="lista.php">Takaisin kurssilistaan</a></p>
</body>
</html>
