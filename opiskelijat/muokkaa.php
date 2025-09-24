<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['id'])) {
    die("Opiskelijaa ei valittu.");
}

$id = (int)$_GET['id'];

// Hae opiskelijan nykyiset tiedot
try {
    $sql = "SELECT * FROM opiskelijat WHERE opiskelija_numero = :id";
    $kysely = $yhteys->prepare($sql);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();
    $opiskelija = $kysely->fetch();
    if (!$opiskelija) {
        die("Opiskelijaa ei löytynyt.");
    }
} catch (PDOException $e) {
    die("Virhe haettaessa opiskelijaa: " . $e->getMessage());
}

// Käsittele lomakkeen lähetys
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $etunimi = $_POST['etunimi'] ?? '';
    $sukunimi = $_POST['sukunimi'] ?? '';
    $syntymapaiva = $_POST['syntymapaiva'] ?? '';
    $vuosikurssi = $_POST['vuosikurssi'] ?? '';

    if (!empty($etunimi) && !empty($sukunimi) && !empty($syntymapaiva) && !empty($vuosikurssi)) {
        try {
            $sql = "UPDATE opiskelijat 
                    SET etunimi = :etunimi, sukunimi = :sukunimi, syntymäpäivä = :syntymapaiva, vuosikurssi = :vuosikurssi
                    WHERE opiskelija_numero = :id";
            $kysely = $yhteys->prepare($sql);
            $kysely->bindParam(':etunimi', $etunimi);
            $kysely->bindParam(':sukunimi', $sukunimi);
            $kysely->bindParam(':syntymapaiva', $syntymapaiva);
            $kysely->bindParam(':vuosikurssi', $vuosikurssi, PDO::PARAM_INT);
            $kysely->bindParam(':id', $id, PDO::PARAM_INT);
            $kysely->execute();

            header("Location: lista.php");
            exit();
        } catch (PDOException $e) {
            $virhe = "Virhe päivitettäessä opiskelijaa: " . $e->getMessage();
        }
    } else {
        $virhe = "Kaikki kentät ovat pakollisia.";
    }
}

renderHeader("Muokkaa opiskelijaa");
?>

<h2>Muokkaa opiskelijaa: <?= htmlspecialchars($opiskelija['etunimi'] . ' ' . $opiskelija['sukunimi']) ?></h2>

<?php if (!empty($virhe)) : ?>
    <p style="color:red;"><?= htmlspecialchars($virhe) ?></p>
<?php endif; ?>

<form method="post">
    
    <label>Etunimi:<br><input type="text" name="etunimi" value="<?= htmlspecialchars($opiskelija['etunimi']) ?>"></label><br><br>
    <label>Sukunimi:<br><input type="text" name="sukunimi" value="<?= htmlspecialchars($opiskelija['sukunimi']) ?>"></label><br><br>
    <label>Syntymäpäivä:<br><input type="date" name="syntymapaiva" value="<?= htmlspecialchars($opiskelija['syntymäpäivä']) ?>"></label><br><br>
    <label>Vuosikurssi:<br><input type="number" name="vuosikurssi" min="1" max="3" value="<?= htmlspecialchars($opiskelija['vuosikurssi']) ?>" ></label><br><br>

    <button class="btn" type="submit">Tallenna muutokset</button>
</form>

<p><a class="btn" href="lista.php">Takaisin opiskelijalistaan</a></p>

<?php renderFooter(); ?>
