# KurssiHallinta

Kuvitteellinen oppilaitoksen kurssienhallintajärjestelmä.  
Sovelluksella voi hallita opiskelijoita, opettajia, kursseja, tiloja ja kurssi-ilmoittautumisia.  
Järjestelmä sisältää CRUD-toiminnot (luonti, luku, muokkaus ja poisto) sekä viikkokalenterin, jossa näkyvät kurssien opetussessiot.

## ⚙️ Ominaisuudet

- Opiskelijoiden, opettajien, kurssien, tilojen ja ilmoittautumisten hallinta (CRUD)
- Kurssi-, opiskelija-, opettaja- ja tilakohtaiset näkymät
- Viikkokalenterinäkymä, joka näyttää kurssien opetussessiot

---

## 🧱 Teknologiat

- PHP
- MySQL
- XAMPP-kehitysympäristö

---

## 📦 Asennus

1. Kloonaa projekti

   ```bash
   git clone <repon-osoite>
   cd <repon-kansio>
   ```

2. Käynnistä XAMPPin Apache ja MySQL

3. Avaa selaimessa:
    [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

4. Luo uusi user account ja anna tälle kaikki oikeudet.

5. Luo MySQL-tietokanta ja tuo (import) mukana tuleva `tietokanta.sql`-tiedosto.

6. Luo `.env`-tiedosto projektin juureen ja syötä oikeat tiedot käyttäjänimeen ja salasanaan (luotiin kohdassa 4):

   ```bash
   DB_USERNAME=käyttäjänimi
   DB_PASSWORD=salasana
   DB_NAME=projekti
   ```

7. Avaa selaimessa:  
   [http://localhost/<repon-kansio>]

---

## 🖥️ Käyttö

- Lisää, muokkaa ja poista opiskelijoita, opettajia, kursseja, tiloja ja ilmoittautumisia hallintanäkymistä
- Katso kurssinäkymästä kurssin tiedot ja ilmoittautuneet opiskelijat
- Katso opiskelijanäkymästä opiskelijan tiedot ja hänen kurssinsa
- Katso opettajanäkymästä opettajan tiedot ja hänen kurssinsa
- Katso tilanäkymästä tilan tiedot, sen kurssit ja niiden osallistujamäärät
- Viikkokalenterissa voit selata viikon aikatauluja valitun opettajan, opiskelijan, kurssin tai tilan mukaan

---

## 📌 Kehittäjille

### Muutosten tekeminen ja lähettäminen

```bash
git add .
git commit -m "kuvaile muutoksesi"
git push origin main
```

### Uusien muutosten hakeminen

```bash
git pull origin main
```

---
