<?php
require '../yhteys.php';


$sql_lause = "SELECT * FROM tilat";

try {
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->execute();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}
$tulos = $kysely->fetchAll();
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tilat</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 80%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; color: #2980b9; }
        a:hover { text-decoration: underline; }
    </style>
</head>

<p>
    <a href="../index.php" 
       style="display:inline-block; padding:8px 16px; background:#2980b9; color:#fff; text-decoration:none; border-radius:5px;">
       ⬅ Takaisin etusivulle
    </a>
</p>

<body>
<h2>Tilat</h2>
<p><a href="lisaa.php">Lisää Tila</a></p>

<table>
<tr>
    <th>ID</th>
    <th>Nimi</th>
    <th>Kapasiteetti</th>
    <th>Toiminnot</th>
</tr>

<?php foreach($tulos as $rivi): ?>
<tr>
    <td><?php echo htmlspecialchars($rivi['id']); ?></td>
    <td><?php echo htmlspecialchars($rivi['nimi']); ?></td>
    <td><?php echo htmlspecialchars($rivi['kapasiteetti']); ?></td>
    <td>
        <a href="muokkaa.php?id=<?php echo $rivi['id']; ?>">Muokkaa</a> |
        <a href="poista.php?id=<?php echo $rivi['id']; ?>" onclick="return confirm('Haluatko varmasti poistaa tämän tilan?');">Poista</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
</body>
</html>