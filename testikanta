-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 01:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projekti`
--

-- --------------------------------------------------------

--
-- Table structure for table `kurssikirjautumisilla`
--

CREATE TABLE `kurssikirjautumisilla` (
  `id` int(11) NOT NULL,
  `opiskelija` int(30) NOT NULL,
  `kurssi` int(30) NOT NULL,
  `Kirjautumispäivä` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kurssit`
--

CREATE TABLE `kurssit` (
  `id` int(11) NOT NULL,
  `nimi` varchar(30) NOT NULL,
  `kuvaus` varchar(30) NOT NULL,
  `alkupäivä` date NOT NULL,
  `loppupäivä` date NOT NULL,
  `opettaja` int(30) NOT NULL,
  `tila` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opettajat`
--

CREATE TABLE `opettajat` (
  `tunnusnumero` int(30) NOT NULL,
  `etunimi` varchar(30) NOT NULL,
  `sukunimi` varchar(30) NOT NULL,
  `aine` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opiskelijat`
--

CREATE TABLE `opiskelijat` (
  `opiskelija_numero` int(11) NOT NULL,
  `etunimi` varchar(30) NOT NULL,
  `sukunimi` varchar(30) NOT NULL,
  `syntymäpäivä` date NOT NULL,
  `vuosikurssi` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tilat`
--

CREATE TABLE `tilat` (
  `id` int(30) NOT NULL,
  `nimi` varchar(30) NOT NULL,
  `kapasiteetti` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kurssikirjautumisilla`
--
ALTER TABLE `kurssikirjautumisilla`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opiskelija` (`opiskelija`),
  ADD KEY `kurssi` (`kurssi`);

--
-- Indexes for table `kurssit`
--
ALTER TABLE `kurssit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tila` (`tila`),
  ADD KEY `opettaja` (`opettaja`);

--
-- Indexes for table `opettajat`
--
ALTER TABLE `opettajat`
  ADD PRIMARY KEY (`tunnusnumero`);

--
-- Indexes for table `opiskelijat`
--
ALTER TABLE `opiskelijat`
  ADD PRIMARY KEY (`opiskelija_numero`);

--
-- Indexes for table `tilat`
--
ALTER TABLE `tilat`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kurssikirjautumisilla`
--
ALTER TABLE `kurssikirjautumisilla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kurssit`
--
ALTER TABLE `kurssit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opettajat`
--
ALTER TABLE `opettajat`
  MODIFY `tunnusnumero` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opiskelijat`
--
ALTER TABLE `opiskelijat`
  MODIFY `opiskelija_numero` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tilat`
--
ALTER TABLE `tilat`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kurssikirjautumisilla`
--
ALTER TABLE `kurssikirjautumisilla`
  ADD CONSTRAINT `kurssikirjautumisilla_ibfk_1` FOREIGN KEY (`opiskelija`) REFERENCES `opiskelijat` (`opiskelija_numero`),
  ADD CONSTRAINT `kurssikirjautumisilla_ibfk_2` FOREIGN KEY (`kurssi`) REFERENCES `kurssit` (`id`);

--
-- Constraints for table `kurssit`
--
ALTER TABLE `kurssit`
  ADD CONSTRAINT `kurssit_ibfk_1` FOREIGN KEY (`tila`) REFERENCES `tilat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
