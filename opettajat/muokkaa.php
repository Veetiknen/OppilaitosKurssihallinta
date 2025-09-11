<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['id'])) die("Opettaja ID puuttuu.");
$id = (int)$_GET['id'];

// Hae opettaja
$kysely = $yhteys->prepare("SELECT * FROM opettajat WHERE tunnusnumero = :id");
$kysely->bindParam(':id', $id);
$kysely->execute();
$opettaja = $kysely->fetch();
if (!$opettaja) die("Opettajaa ei löytynyt.");

renderHeader("Muokkaa Opettajaa");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $etunimi = $_POST['etunimi'];
    $sukunimi = $_POST['sukunimi'];
    $aine = $_POST['aine'];
    $update = $yhteys->prepare("UPDATE opettajat SET etunimi=:etunimi, sukunimi=:sukunimi, aine=:aine WHERE tunnusnumero=:id");
    $update->bindParam(':etunimi', $etunimi);
    $update->bindParam(':sukunimi', $sukunimi);
    $update->bindParam(':aine', $aine);
    $update->bindParam(':id', $id);
    $update->execute();
    echo "<p>Opettaja päivitetty!</p>";
}

?>

<form method="post">
    <label>Etunimi:<br><input type="text" name="etunimi" value="<?= htmlspecialchars($opettaja['etunimi']) ?>"></label><br>
    <label>Sukunimi:<br><input type="text" name="sukunimi" value="<?= htmlspecialchars($opettaja['sukunimi']) ?>"></label><br>
    <label>Aine:<br><input type="text" name="aine" value="<?= htmlspecialchars($opettaja['aine']) ?>"></label><br>
    <button class="btn" type="submit">Tallenna</button>
</form>
<p><a href="lista.php">Takaisin listaan</a></p>

<?php renderFooter(); ?>
