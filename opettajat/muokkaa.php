<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Opettajaa ei valittu.");
}

$opettaja_id = (int)$_GET['id'];

// Hae opettajan tiedot
try {
    $sql = "SELECT * FROM opettajat WHERE tunnusnumero = :id";
    $kysely = $yhteys->prepare($sql);
    $kysely->bindParam(':id', $opettaja_id, PDO::PARAM_INT);
    $kysely->execute();
    $opettaja = $kysely->fetch();
    if (!$opettaja) {
        die("Opettajaa ei löytynyt.");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Käsittele lomakkeen POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etunimi = $_POST['etunimi'] ?? '';
    $sukunimi = $_POST['sukunimi'] ?? '';
    $aine = $_POST['aine'] ?? '';

    if (!empty($etunimi) && !empty($sukunimi) && !empty($aine)) {
        try {
            $sql = "UPDATE opettajat
                    SET etunimi = :etunimi, sukunimi = :sukunimi, aine = :aine
                    WHERE tunnusnumero = :id";
            $kysely = $yhteys->prepare($sql);
            $kysely->bindParam(':etunimi', $etunimi);
            $kysely->bindParam(':sukunimi', $sukunimi);
            $kysely->bindParam(':aine', $aine);
            $kysely->bindParam(':id', $opettaja_id, PDO::PARAM_INT);
            $kysely->execute();

            header("Location: lista.php");
            exit;

        } catch (PDOException $e) {
            echo "Virhe muokattaessa opettajaa: " . $e->getMessage();
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
    <title>Muokkaa opettajaa</title>
</head>
<body>
<h2>Muokkaa opettajaa: <?= htmlspecialchars($opettaja['etunimi'] . ' ' . $opettaja['sukunimi']) ?></h2>

<form method="post">
    <label>Etunimi:<br>
        <input type="text" name="etunimi" value="<?= htmlspecialchars($opettaja['etunimi']) ?>" required>
    </label><br><br>

    <label>Sukunimi:<br>
        <input type="text" name="sukunimi" value="<?= htmlspecialchars($opettaja['sukunimi']) ?>" required>
    </label><br><br>

    <label>Aine:<br>
        <input type="text" name="aine" value="<?= htmlspecialchars($opettaja['aine']) ?>" required>
    </label><br><br>

    <button type="submit">Tallenna muutokset</button>
</form>

<p><a href="lista.php">Takaisin opettajalistaan</a></p>
</body>
</html>
