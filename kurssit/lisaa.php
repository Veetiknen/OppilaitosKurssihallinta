<?php
require '../yhteys.php';

// Haetaan aineet (opettajien aine-kentistä distinct)
try {
    $aineet = $yhteys->query("SELECT DISTINCT aine FROM opettajat WHERE tunnusnumero != 0")->fetchAll();
    $opettajat = $yhteys->query("SELECT tunnusnumero, CONCAT(etunimi, ' ', sukunimi) AS nimi, aine FROM opettajat WHERE tunnusnumero != 0")->fetchAll();
    $tilat = $yhteys->query("SELECT id, nimi FROM tilat")->fetchAll();
} catch (PDOException $e) {
    die("Virhe haettaessa tietoja: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nimi = $_POST["nimi"] ?? '';
    $kuvaus = $_POST["kuvaus"] ?? '';
    $alkupaiva = $_POST["alkupaiva"] ?? '';
    $loppupaiva = $_POST["loppupaiva"] ?? '';
    $opettaja = $_POST["opettaja"] ?? '';
    $tila = $_POST["tila"] ?? '';
    $aine = $_POST["aine"] ?? '';

    if (!empty($nimi) && !empty($kuvaus) && !empty($alkupaiva) && !empty($loppupaiva) && !empty($opettaja) && !empty($tila) && !empty($aine)) {
        try {
            $sql_lause = "INSERT INTO kurssit 
                (nimi, kuvaus, alkupäivä, loppupäivä, opettaja, tila)
                VALUES (:nimi, :kuvaus, :alkupaiva, :loppupaiva, :opettaja, :tila)";
            $kysely = $yhteys->prepare($sql_lause);
            $kysely->bindParam(':nimi', $nimi);
            $kysely->bindParam(':kuvaus', $kuvaus);
            $kysely->bindParam(':alkupaiva', $alkupaiva);
            $kysely->bindParam(':loppupaiva', $loppupaiva);
            $kysely->bindParam(':opettaja', $opettaja);
            $kysely->bindParam(':tila', $tila);
            $kysely->execute();

            header("Location: lista.php");
            exit;
        } catch (PDOException $e) {
            echo "Virhe lisättäessä kurssia: " . $e->getMessage();
        }
    } else {
        echo "Kaikki kentät ovat pakollisia";
    }
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää kurssi</title>
</head>
<body>
    <h2>Lisää uusi kurssi</h2>
    <form method="post">
        <label>Aine:<br>
            <select name="aine" required>
                <option value="">-- Valitse aine --</option>
                <?php foreach($aineet as $a): ?>
                    <option value="<?= htmlspecialchars($a['aine']) ?>"><?= htmlspecialchars($a['aine']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Kurssin nimi:<br><input type="text" name="nimi" required></label><br><br>
        <label>Kuvaus:<br><input type="text" name="kuvaus" required></label><br><br>
        <label>Alkupäivä:<br><input type="date" name="alkupaiva" required></label><br><br>
        <label>Loppupäivä:<br><input type="date" name="loppupaiva" required></label><br><br>

        <label>Opettaja:<br>
            <select name="opettaja" required>
                <option value="">-- Valitse opettaja --</option>
                <?php foreach($opettajat as $o): ?>
                    <option value="<?= $o['tunnusnumero'] ?>">
                        <?= htmlspecialchars($o['nimi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Tila:<br>
            <select name="tila" required>
                <option value="">-- Valitse tila --</option>
                <?php foreach($tilat as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nimi']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Lisää kurssi</button>
    </form>
    <p><a href="lista.php">Takaisin kurssilistaan</a></p>
</body>
</html>
