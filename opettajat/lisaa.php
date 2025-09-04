<?php
require '../yhteys.php'; // Varmista polku, muuta tämä tarvittaessa

// Lomakkeen lähetys
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $etunimi = $_POST['etunimi'] ?? '';
    $sukunimi = $_POST['sukunimi'] ?? '';
    $aine = $_POST['aine'] ?? '';

    if (!empty($etunimi) && !empty($sukunimi) && !empty($aine)) {
        try {
            $sql = "INSERT INTO opettajat (etunimi, sukunimi, aine) VALUES (?, ?, ?)";
            $stmt = $yhteys->prepare($sql);
            $stmt->execute([$etunimi, $sukunimi, $aine]);

            // Uudelleenohjaus takaisin opettajalistaukseen
            header("Location: index.php"); // Vaihda tiedostonimi, jos ei ole index.php
            exit;
        } catch (PDOException $e) {
            die("Virhe lisättäessä opettajaa: " . $e->getMessage());
        }
    } else {
        $virhe = "Kaikki kentät ovat pakollisia.";
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää opettaja</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input[type="text"] { padding: 5px; width: 300px; }
        button { padding: 6px 12px; }
    </style>
</head>
<body>
<h2>Lisää opettaja</h2>

<?php if (!empty($virhe)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($virhe); ?></p>
<?php endif; ?>

<form method="post" action="lisaa.php">
    <p>
        <label>Etunimi:<br>
        <input type="text" name="etunimi" required></label>
    </p>
    <p>
        <label>Sukunimi:<br>
        <input type="text" name="sukunimi" required></label>
    </p>
    <p>
        <label>Aine:<br>
        <input type="text" name="aine" required></label>
    </p>
    <p>
        <button type="submit">Lisää opettaja</button>
    </p>
</form>

<p><a href="index.php">Takaisin listaan</a></p>
</body>
</html>
