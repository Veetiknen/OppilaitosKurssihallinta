<?php
require '../yhteys.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Virheellinen tunnusnumero.");
}

$id = (int) $_GET['id'];

// Estetään "Tuntematon opettaja" (id=0) poistaminen
if ($id === 0) {
    die("Virhe: Tuntematon opettaja -riviä ei voi poistaa.");
}

try {
    // Tarkistetaan, onko opettajalla kursseja
    $sql_lause = "SELECT COUNT(*) FROM kurssit WHERE opettaja = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();
    $kurssiLkm = $kysely->fetchColumn();

    if ($kurssiLkm > 0 && !isset($_GET['confirm'])) {
        // Kysytään vahvistus jos kursseja löytyy
        echo "<script>
                if (confirm('Opettajalla on $kurssiLkm kurssia. Haluatko varmasti poistaa opettajan? Kurssien opettajaksi asetetaan Tuntematon opettaja.')) {
                    window.location.href = 'poista.php?id=$id&confirm=1';
                } else {
                    window.location.href = 'lista.php';
                }
              </script>";
        exit;
    }

    $yhteys->beginTransaction();

    if ($kurssiLkm > 0) {
        // Siirretään kurssit Tuntemattomalle opettajalle (id=0)
        $sql_update = "UPDATE kurssit SET opettaja = 0 WHERE opettaja = :id";
        $stmt1 = $yhteys->prepare($sql_update);
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
    }

    // Poistetaan opettaja
    $sql_poistoLause = "DELETE FROM opettajat WHERE tunnusnumero = :id";
    $kysely = $yhteys->prepare($sql_poistoLause);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();

    $yhteys->commit();

    header("Location: lista.php");
    exit;

} catch (PDOException $e) {
    $yhteys->rollBack();
    echo "VIRHE: " . $e->getMessage();
}
