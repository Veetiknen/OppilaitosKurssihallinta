<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['opiskelija'])) {
    die("Opiskelijaa ei valittu");
}

$opiskelija_id = (int)$_GET['opiskelija'];
$viikko = $_GET['viikko'] ?? date('Y-W');

// Haetaan opiskelijan tiedot
$sql = "SELECT etunimi, sukunimi, vuosikurssi FROM opiskelijat WHERE opiskelija_numero = ?";
$kysely = $yhteys->prepare($sql);
$kysely->execute([$opiskelija_id]);
$opiskelija = $kysely->fetch();
if (!$opiskelija) die("Opiskelijaa ei löytynyt");

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

// Haetaan opiskelijan kaikki kurssit ja sessiot
$sql = "SELECT s.viikonpaiva, s.aloitus, s.lopetus,
               k.nimi AS kurssi_nimi, k.alkupäivä, k.loppupäivä,
               t.nimi AS tila_nimi,
               o.etunimi AS opettaja_etunimi, o.sukunimi AS opettaja_sukunimi
        FROM kurssikirjautumisilla kk
        JOIN kurssit k ON kk.kurssi = k.id
        JOIN kurssisessiot s ON s.kurssi_id = k.id
        JOIN tilat t ON k.tila = t.id
        JOIN opettajat o ON k.opettaja = o.tunnusnumero
        WHERE kk.opiskelija = ?";
$kysely = $yhteys->prepare($sql);
$kysely->execute([$opiskelija_id]);
$sessiot = $kysely->fetchAll(PDO::FETCH_ASSOC);

$viikonpaivat = ['ma'=>'Ma', 'ti'=>'Ti', 'ke'=>'Ke', 'to'=>'To', 'pe'=>'Pe'];

function onkoKurssiKaynnissa($alkupaiva, $loppupaiva, $viikon_alku, $viikon_loppu) {
    $kurssi_alku = new DateTime($alkupaiva);
    $kurssi_loppu = new DateTime($loppupaiva);
    return !($kurssi_loppu < $viikon_alku || $kurssi_alku > $viikon_loppu);
}

renderHeader("Viikkonäkymä - " . htmlspecialchars($opiskelija['etunimi'] . ' ' . $opiskelija['sukunimi']));
?>

<h2>Opiskelijan viikkonäkymä</h2>
<p><strong>Nimi:</strong> <?= htmlspecialchars($opiskelija['etunimi'] . ' ' . $opiskelija['sukunimi']) ?></p>
<p><strong>Vuosikurssi:</strong> <?= htmlspecialchars($opiskelija['vuosikurssi']) ?></p>
<p><strong>Viikko:</strong> <?= $viikkonro ?>/<?= $vuosi ?> (<?= $viikon_alku->format('d.m.Y') ?> - <?= $viikon_loppu->format('d.m.Y') ?>)</p>

<div class="week-navigation">
    <a href="?opiskelija=<?= $opiskelija_id ?>&viikko=<?= $edellinen_viikko->format('Y-W') ?>" class="btn">« Edellinen viikko</a>
    <a href="?opiskelija=<?= $opiskelija_id ?>&viikko=<?= date('Y-W') ?>" class="btn">Tämä viikko</a>
    <a href="?opiskelija=<?= $opiskelija_id ?>&viikko=<?= $seuraava_viikko->format('Y-W') ?>" class="btn">Seuraava viikko »</a>
</div>

<table class="schedule-table">
    <thead>
        <tr>
            <th class="time-header">Aika</th>
            <?php 
            $paiva_counter = 0;
            foreach ($viikonpaivat as $lyh => $pv): 
                $paivan_pvm = clone $viikon_alku;
                $paivan_pvm->modify("+{$paiva_counter} days");
            ?>
                <th class="day-header"><?= $pv ?><br><small><?= $paivan_pvm->format('d.m') ?></small></th>
            <?php 
                $paiva_counter++;
            endforeach; 
            ?>
        </tr>
    </thead>
    <tbody>
        <?php for ($h = 8; $h <= 17; $h++): ?>
            <tr>
                <th class="time-header"><?= sprintf('%02d:00', $h) ?></th>
                <?php foreach ($viikonpaivat as $lyh => $pv): ?>
                    <td>
                        <?php 
                        $cell_sessiot = [];
                        foreach ($sessiot as $s) {
                            if ($s['viikonpaiva'] === $lyh &&
                                $s['aloitus'] <= $h &&
                                $s['lopetus'] > $h &&
                                onkoKurssiKaynnissa($s['alkupäivä'], $s['loppupäivä'], $viikon_alku, $viikon_loppu) &&
                                $s['aloitus'] == $h) {
                                $cell_sessiot[] = $s;
                            }
                        }

                        $maara = count($cell_sessiot);
                        if ($maara > 0):
                            foreach ($cell_sessiot as $index => $s):
                                $kesto = $s['lopetus'] - $s['aloitus'];
                                $leveys = (100 / $maara) - 2;
                                $left = $index * (100 / $maara);
                        ?>
                            <div class="student-session-block" style="
                                left: <?= $left ?>%;
                                width: <?= $leveys ?>%;
                                height: <?= ($kesto * 60) - 8 ?>px;
                            ">
                                <div class="student-session-title"><?= htmlspecialchars($s['kurssi_nimi']) ?></div>
                                <div class="student-session-time"><?= $s['aloitus'] ?>:00-<?= $s['lopetus'] ?>:00</div>
                                <div class="student-session-room">
                                    Opettaja: <?= htmlspecialchars($s['opettaja_etunimi'] . " " . $s['opettaja_sukunimi']) ?><br>
                                    Tila: <?= htmlspecialchars($s['tila_nimi']) ?>
                                </div>
                            </div>
                        <?php endforeach;
                        endif;
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<a href="nayta.php?id=<?= $opiskelija_id ?>" class="btn">« Takaisin opiskelijaan</a>

<?php renderFooter(); ?>
