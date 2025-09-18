<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['opettajat'])) {
    die("Opettajaa ei valittu");
}

$opettaja_id = (int)$_GET['opettajat'];
$viikko = $_GET['viikko'] ?? date('Y-W');

// Haetaan opettajan tiedot
$sql = "SELECT etunimi, sukunimi, aine FROM opettajat WHERE tunnusnumero = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$opettaja_id]);
$opettaja = $stmt->fetch();
if (!$opettaja) {
    die("Opettajaa ei löytynyt");
}

// Viikon käsittely
list($vuosi, $viikkonro) = explode('-', $viikko);
$viikon_alku = new DateTime();
$viikon_alku->setISODate($vuosi, $viikkonro);
$viikon_loppu = clone $viikon_alku;
$viikon_loppu->modify('+4 days');

$edellinen_viikko = clone $viikon_alku;
$edellinen_viikko->modify('-1 week');
$seuraava_viikko = clone $viikon_alku;
$seuraava_viikko->modify('+1 week');

// Haetaan kaikki opettajan kurssit ja sessiot
$sql = "SELECT s.viikonpaiva, s.aloitus, s.lopetus,
               k.nimi AS kurssi_nimi, k.alkupäivä, k.loppupäivä,
               t.nimi AS tila_nimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        JOIN tilat t ON k.tila = t.id
        WHERE k.opettaja = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$opettaja_id]);
$sessiot = $stmt->fetchAll(PDO::FETCH_ASSOC);

$viikonpaivat = ['ma'=>'Ma', 'ti'=>'Ti', 'ke'=>'Ke', 'to'=>'To', 'pe'=>'Pe'];

function onkoKurssiKaynnissa($alkupaiva, $loppupaiva, $viikon_alku, $viikon_loppu) {
    $kurssi_alku = new DateTime($alkupaiva);
    $kurssi_loppu = new DateTime($loppupaiva);
    return !($kurssi_loppu < $viikon_alku || $kurssi_alku > $viikon_loppu);
}

renderHeader("Viikkonäkymä: " . htmlspecialchars($opettaja['etunimi'] . ' ' . $opettaja['sukunimi']));
?>

<h2>Viikkonäkymä – <?= htmlspecialchars($opettaja['etunimi'] . ' ' . $opettaja['sukunimi']) ?></h2>
<p><strong>Aine:</strong> <?= htmlspecialchars($opettaja['aine']) ?></p>
<p><strong>Viikko:</strong> <?= $viikkonro ?>/<?= $vuosi ?> (<?= $viikon_alku->format('d.m.Y') ?> - <?= $viikon_loppu->format('d.m.Y') ?>)</p>

<div style="margin-bottom: 20px; text-align: center;">
    <a href="?opettajat=<?= $opettaja_id ?>&viikko=<?= $edellinen_viikko->format('Y-W') ?>" class="btn">&laquo; Edellinen viikko</a>
    <a href="?opettajat=<?= $opettaja_id ?>&viikko=<?= date('Y-W') ?>" class="btn">Tämä viikko</a>
    <a href="?opettajat=<?= $opettaja_id ?>&viikko=<?= $seuraava_viikko->format('Y-W') ?>" class="btn">Seuraava viikko &raquo;</a>
</div>

<table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="width: 80px; border: 1px solid #ddd; padding: 5px; background: #f5f5f5;">Aika</th>
            <?php 
            $paiva_counter = 0;
            foreach ($viikonpaivat as $lyh => $pv): 
                $paivan_pvm = clone $viikon_alku;
                $paivan_pvm->modify("+{$paiva_counter} days");
            ?>
                <th style="border: 1px solid #ddd; padding: 5px; background: #f5f5f5;">
                    <?= $pv ?><br><small><?= $paivan_pvm->format('d.m') ?></small>
                </th>
            <?php 
                $paiva_counter++;
            endforeach; 
            ?>
        </tr>
    </thead>
    <tbody>
        <?php for ($h = 8; $h <= 16; $h++): ?>
            <tr>
                <th style="border: 1px solid #ddd; padding: 5px; background: #f9f9f9;">
                    <?= sprintf('%02d:00', $h) ?>
                </th>
                <?php foreach ($viikonpaivat as $lyh => $pv): ?>
                    <td style="border: 1px solid #ddd; padding: 2px; height: 60px; vertical-align: top; position: relative;">
                        <?php 
                        foreach ($sessiot as $s) {
                            if ($s['viikonpaiva'] === $lyh && 
                                $s['aloitus'] <= $h && 
                                $s['lopetus'] > $h &&
                                onkoKurssiKaynnissa($s['alkupäivä'], $s['loppupäivä'], $viikon_alku, $viikon_loppu)) {
                                $kesto = $s['lopetus'] - $s['aloitus'];
                                $alkaa_tassa = $s['aloitus'] == $h;
                                
                                if ($alkaa_tassa):
                        ?>
                            <div style="
                                background: linear-gradient(135deg, #ffe7ba, #ffd591); 
                                padding: 3px 5px; 
                                margin: 1px; 
                                border-radius: 4px; 
                                font-size: 0.85em; 
                                border-left: 4px solid #cc6600;
                                position: absolute;
                                top: 2px;
                                left: 2px;
                                right: 2px;
                                height: <?= ($kesto * 60) - 8 ?>px;
                                overflow: hidden;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            ">
                                <div style="font-weight: bold;"><?= htmlspecialchars($s['kurssi_nimi']) ?></div>
                                <div style="font-size: 0.9em;"><?= $s['aloitus'] ?>:00-<?= $s['lopetus'] ?>:00</div>
                                <div style="font-size: 0.8em; color: #333;">Tila: <?= htmlspecialchars($s['tila_nimi']) ?></div>
                            </div>
                        <?php 
                                endif;
                            }
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<a href="lisaa_viikkonakymaan.php?opettajat=<?= $opettaja_id ?>" class="btn" style="margin-top: 20px;">&laquo; Muokkaa aikataulua</a>
<a href="nayta.php?id=<?= $opettaja_id ?>" class="btn" style="margin-top: 20px;">&laquo; Takaisin opettajaan</a>

<?php renderFooter(); ?>
