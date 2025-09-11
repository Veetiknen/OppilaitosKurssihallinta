<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Tilan ID puuttuu.");
}

$tila_id = (int)$_GET['id'];

try {
    // Päivitä kurssien tila tilaan 0 ennen tilan poistamista
    $paivita = $yhteys->prepare("UPDATE kurssit SET tila = 0 WHERE tila = :id");
    $paivita->bindParam(':id', $tila_id, PDO::PARAM_INT);
    $paivita->execute();

    // Poista tila
    $poista = $yhteys->prepare("DELETE FROM tilat WHERE id = :id AND id != 0");
    $poista->bindParam(':id', $tila_id, PDO::PARAM_INT);
    $poista->execute();

    header("Location: lista.php");
    exit;

} catch (PDOException $e) {
    die("Virhe poistettaessa tilaa: " . $e->getMessage());
}
