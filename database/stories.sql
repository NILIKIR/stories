-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 05. bře 2022, 23:51
-- Verze serveru: 10.4.21-MariaDB
-- Verze PHP: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `stories`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `charakters`
--

CREATE TABLE `charakters` (
  `ID_CHARAKTER` int(11) NOT NULL,
  `NAME_CHARAKTER` text DEFAULT NULL,
  `PROPERTIES_CHARAKTER` text DEFAULT NULL,
  `ID_USER` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `charakters`
--

INSERT INTO `charakters` (`ID_CHARAKTER`, `NAME_CHARAKTER`, `PROPERTIES_CHARAKTER`, `ID_USER`) VALUES
(1, 'TEST', NULL, 1),
(2, 'TEST', NULL, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `conditions`
--

CREATE TABLE `conditions` (
  `ID_CONDITION` int(11) NOT NULL,
  `CONDITION` int(11) NOT NULL,
  `COUNT` int(11) NOT NULL,
  `ID_ITEM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `conditions`
--

INSERT INTO `conditions` (`ID_CONDITION`, `CONDITION`, `COUNT`, `ID_ITEM`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `conditions_par`
--

CREATE TABLE `conditions_par` (
  `ID_CONDITIONS_PAR` int(11) NOT NULL,
  `ID_CONDITION` int(11) NOT NULL,
  `ID_JUMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `conditions_par`
--

INSERT INTO `conditions_par` (`ID_CONDITIONS_PAR`, `ID_CONDITION`, `ID_JUMP`) VALUES
(1, 1, 6),
(2, 2, 7),
(3, 1, 8),
(4, 1, 9),
(5, 2, 10),
(6, 1, 12),
(7, 2, 13);

-- --------------------------------------------------------

--
-- Struktura tabulky `debug`
--

CREATE TABLE `debug` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `value` text DEFAULT NULL,
  `property` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `debug`
--

INSERT INTO `debug` (`id`, `name`, `value`, `property`) VALUES
(860, 'CONDITION has valid conditions current condition', '{\"condition_type\":\"1\",\"id_item_needed\":\"1\",\"count_item_needed\":\"1\"}', ''),
(861, 'CONDITION is valid', '', ''),
(862, 'CONDITION has valid conditions current condition', '{\"condition_type\":\"2\",\"id_item_needed\":\"1\",\"count_item_needed\":\"1\"}', ''),
(863, 'CONDITION is valid', '', ''),
(864, 'CONDITION has valid conditions current condition', '{\"condition_type\":\"1\",\"id_item_needed\":\"1\",\"count_item_needed\":\"1\"}', ''),
(865, 'CONDITION is not valid', '', ''),
(866, 'CONDITION has valid conditions current condition', '{\"condition_type\":\"2\",\"id_item_needed\":\"1\",\"count_item_needed\":\"1\"}', ''),
(867, 'CONDITION is not valid', '', ''),
(868, 'CONDITION has valid conditions current condition', '{\"condition_type\":\"1\",\"id_item_needed\":\"1\",\"count_item_needed\":\"1\"}', ''),
(869, 'CONDITION is not valid', '', ''),
(870, 'CONDITION has valid conditions current condition', '{\"condition_type\":\"2\",\"id_item_needed\":\"1\",\"count_item_needed\":\"1\"}', ''),
(871, 'ITEM actualize inventory query database', 'successfull', ''),
(872, 'ITEM actualize inventory query database', 'successfull', ''),
(873, 'ITEM actualize inventory query database', 'successfull', ''),
(874, 'ITEM actualize inventory query database', 'successfull', ''),
(875, 'ITEM actualize inventory query database', 'successfull', ''),
(876, 'ITEM actualize inventory query database', 'successfull', ''),
(877, 'ITEM actualize inventory query database', 'successfull', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `items`
--

CREATE TABLE `items` (
  `ID_ITEM` int(11) NOT NULL,
  `NAME_ITEM` text NOT NULL,
  `PROPERTIES_ITEM` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `items`
--

INSERT INTO `items` (`ID_ITEM`, `NAME_ITEM`, `PROPERTIES_ITEM`) VALUES
(1, 'Vidlička', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `items_par_charakter`
--

CREATE TABLE `items_par_charakter` (
  `ID_ITEMS_PAR_CHARAKTER` int(11) NOT NULL,
  `ID_CHARAKTER` int(11) NOT NULL,
  `ID_ITEM` int(11) NOT NULL,
  `COUNT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `items_par_jump`
--

CREATE TABLE `items_par_jump` (
  `ID_ITEMS_PAR_JUMP` int(11) NOT NULL,
  `ID_ITEM` int(11) NOT NULL,
  `COUNT` int(11) NOT NULL,
  `ID_JUMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `items_par_jump`
--

INSERT INTO `items_par_jump` (`ID_ITEMS_PAR_JUMP`, `ID_ITEM`, `COUNT`, `ID_JUMP`) VALUES
(1, 1, 1, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `items_par_stories`
--

CREATE TABLE `items_par_stories` (
  `ID_ITEMS_PAR_STORIES` int(11) NOT NULL,
  `ID_ITEM` int(11) NOT NULL,
  `ID_STORY` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `items_par_stories`
--

INSERT INTO `items_par_stories` (`ID_ITEMS_PAR_STORIES`, `ID_ITEM`, `ID_STORY`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `jumps`
--

CREATE TABLE `jumps` (
  `ID_JUMP` int(11) NOT NULL,
  `LABEL` text NOT NULL,
  `ID_PARAGRAPH_TO` int(11) NOT NULL,
  `ID_PARAGRAPH_FROM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `jumps`
--

INSERT INTO `jumps` (`ID_JUMP`, `LABEL`, `ID_PARAGRAPH_TO`, `ID_PARAGRAPH_FROM`) VALUES
(1, 'Počkáš, až trochu víc vychladneš.', 2, 1),
(2, 'Sebereš ze stolu vidličku, co kdyby.', 2, 1),
(3, 'Seskočíš z okna', 4, 1),
(4, 'Mile se na babičku usměješ.', 3, 2),
(5, 'Utečeš skokem z okna', 4, 2),
(6, 'Máš vidličku a můžeš se bránit. Bohužel to proti zručné babičce nestačí a zde tvá hra končí.', 7, 3),
(7, 'Není to nic platné, babička si na bezbranném koblížku pošmákla.', 7, 3),
(8, 'Jsi vyzbrojen a nenecháš se jen tak očmuchávat. Vytáhneš vydličku a zasadíš ježkovi ránu.', 5, 4),
(9, 'Pohrozíš ježkovi vidličkou a ten couvne. Přeskočíš plot a kutálíš se dál, k lesu.', 6, 4),
(10, 'Výmluvy ti nepomohly, ježek si na tobě pošmáknul.', 7, 4),
(11, 'Bitka dopadla nerozhodně. Ježek se odkulhal a na tobě si smlsnul toulavý pes.', 7, 5),
(12, 'Přiskočíš blíže a lišku bodneš do oka.', 8, 6),
(13, 'Je mi líto, jen co začneš zpívat, liška po tobě skočí a sní tě.', 7, 6);

-- --------------------------------------------------------

--
-- Struktura tabulky `paragraphs`
--

CREATE TABLE `paragraphs` (
  `ID_PARAGRAPH` int(11) NOT NULL,
  `NAME_PARAGRAPH` text NOT NULL,
  `PARAGRAPH` text NOT NULL,
  `ID_STORY` int(11) NOT NULL,
  `ID_PARAGRAPH_TYPE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `paragraphs`
--

INSERT INTO `paragraphs` (`ID_PARAGRAPH`, `NAME_PARAGRAPH`, `PARAGRAPH`, `ID_STORY`, `ID_PARAGRAPH_TYPE`) VALUES
(1, 'Kuchyně', 'Stála na kopci chaloupka a v ní bydlel dědeček s babičkou. Jednoho dne se rozhodli, že si upečou koblížek. A to jsi teď ty. Hrozí ti nebezpečí, o kterém se ti ani nezdá. Babička tě dala vychladnout za okno.', 1, 1),
(2, 'Kuchyně 2', 'Už jsi docela vychladl a všimla si toho i babička. Jde k tobě a slintá.', 1, 2),
(3, 'Kuchyně 3', 'Babička tě chce sníst.', 1, 2),
(4, 'Zahrada', 'Pod oknem se zahrada pěkně svažuje a ty se krásně kutálíš. Kutálíš až do chvíle, než potkáš ježka. Diví se, co jsi zač a přivoní si k tobě.', 1, 2),
(5, 'Souboj s ježkem', 'Na kraji zahrady došlo k bitce. Ježek ti uštědřil několik pichlavých úderů, i ty jsi ježka zranil.', 1, 2),
(6, 'Les', 'Na kraji lesa tě už vyhlíží liška. Mlsně kouká a tak zastavíš opodál. liška chce, abys jí zaspíval hned u ouška.', 1, 2),
(7, 'Konec', 'Tak, a máš to za sebou.', 1, 3),
(8, 'Happyend', 'Liška zakňučela a zdrhla. Blahopřeji, dosáhl jsi šťastného konce. Co tě čeká v lese už není tento příběh.', 1, 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `stories`
--

CREATE TABLE `stories` (
  `ID_STORIES` int(11) NOT NULL,
  `STORY` text NOT NULL,
  `FINISHED` tinyint(1) NOT NULL,
  `PUBLISHED` tinyint(1) NOT NULL,
  `ANOTATION` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `stories`
--

INSERT INTO `stories` (`ID_STORIES`, `STORY`, `FINISHED`, `PUBLISHED`, `ANOTATION`) VALUES
(1, 'Koblížek', 1, 1, NULL),
(2, 'TEST', 1, 0, NULL),
(3, 'TEST', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `stories_par_charakter`
--

CREATE TABLE `stories_par_charakter` (
  `ID_STORIES_PAR_CHARAKTER` int(11) NOT NULL,
  `ID_STORY` int(11) NOT NULL,
  `ID_CHARAKTER` int(11) DEFAULT NULL,
  `ID_PARAGRAPH` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `stories_par_charakter`
--

INSERT INTO `stories_par_charakter` (`ID_STORIES_PAR_CHARAKTER`, `ID_STORY`, `ID_CHARAKTER`, `ID_PARAGRAPH`) VALUES
(6, 1, NULL, NULL),
(7, 2, NULL, NULL),
(8, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `ID_USER` int(11) NOT NULL,
  `LOGIN` text NOT NULL,
  `PASSWORD` text NOT NULL,
  `NAME_USER` text NOT NULL,
  `SURNAME_USER` text NOT NULL,
  `NICKNAME_USER` text NOT NULL,
  `EMAIL_USER` text NOT NULL,
  `CREATED` datetime NOT NULL DEFAULT current_timestamp(),
  `MODIFIED` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`ID_USER`, `LOGIN`, `PASSWORD`, `NAME_USER`, `SURNAME_USER`, `NICKNAME_USER`, `EMAIL_USER`, `CREATED`, `MODIFIED`) VALUES
(1, 'TEST', '', 'TEST', '', '', 'TEST@TEST.TEST', '2022-01-30 10:24:46', '2022-01-30 09:24:46'),
(2, 'Mike', '$2y$10$771nWMeeV5hvxgYU.hIPieulMZz.qoeEBCfWn9blezHk.kUQlCG6a', '', '', 'Mike', '', '2022-02-07 12:58:35', '2022-02-23 00:27:43');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `charakters`
--
ALTER TABLE `charakters`
  ADD PRIMARY KEY (`ID_CHARAKTER`),
  ADD KEY `ID_USER` (`ID_USER`);

--
-- Indexy pro tabulku `conditions`
--
ALTER TABLE `conditions`
  ADD PRIMARY KEY (`ID_CONDITION`),
  ADD KEY `ID_ITEM` (`ID_ITEM`);

--
-- Indexy pro tabulku `conditions_par`
--
ALTER TABLE `conditions_par`
  ADD PRIMARY KEY (`ID_CONDITIONS_PAR`),
  ADD KEY `ID_CONDITION` (`ID_CONDITION`),
  ADD KEY `ID_JUMP` (`ID_JUMP`);

--
-- Indexy pro tabulku `debug`
--
ALTER TABLE `debug`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`ID_ITEM`);

--
-- Indexy pro tabulku `items_par_charakter`
--
ALTER TABLE `items_par_charakter`
  ADD PRIMARY KEY (`ID_ITEMS_PAR_CHARAKTER`),
  ADD KEY `ID_CHARAKTER` (`ID_CHARAKTER`),
  ADD KEY `ID_ITEM` (`ID_ITEM`);

--
-- Indexy pro tabulku `items_par_jump`
--
ALTER TABLE `items_par_jump`
  ADD PRIMARY KEY (`ID_ITEMS_PAR_JUMP`),
  ADD KEY `ID_ITEM` (`ID_ITEM`),
  ADD KEY `ID_JUMP` (`ID_JUMP`);

--
-- Indexy pro tabulku `items_par_stories`
--
ALTER TABLE `items_par_stories`
  ADD PRIMARY KEY (`ID_ITEMS_PAR_STORIES`),
  ADD KEY `ID_ITEM` (`ID_ITEM`),
  ADD KEY `ID_STORIES` (`ID_STORY`);

--
-- Indexy pro tabulku `jumps`
--
ALTER TABLE `jumps`
  ADD PRIMARY KEY (`ID_JUMP`),
  ADD KEY `ID_PARAGRAPH_FROM` (`ID_PARAGRAPH_FROM`),
  ADD KEY `ID_PARAGRAPH_TO` (`ID_PARAGRAPH_TO`);

--
-- Indexy pro tabulku `paragraphs`
--
ALTER TABLE `paragraphs`
  ADD PRIMARY KEY (`ID_PARAGRAPH`),
  ADD KEY `ID_STORIES` (`ID_STORY`);

--
-- Indexy pro tabulku `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`ID_STORIES`);

--
-- Indexy pro tabulku `stories_par_charakter`
--
ALTER TABLE `stories_par_charakter`
  ADD PRIMARY KEY (`ID_STORIES_PAR_CHARAKTER`),
  ADD KEY `ID_CHARAKTER` (`ID_CHARAKTER`),
  ADD KEY `ID_STORIES` (`ID_STORY`),
  ADD KEY `ID_PARAGRAPH` (`ID_PARAGRAPH`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_USER`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `charakters`
--
ALTER TABLE `charakters`
  MODIFY `ID_CHARAKTER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `conditions`
--
ALTER TABLE `conditions`
  MODIFY `ID_CONDITION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `conditions_par`
--
ALTER TABLE `conditions_par`
  MODIFY `ID_CONDITIONS_PAR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pro tabulku `debug`
--
ALTER TABLE `debug`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=878;

--
-- AUTO_INCREMENT pro tabulku `items`
--
ALTER TABLE `items`
  MODIFY `ID_ITEM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `items_par_charakter`
--
ALTER TABLE `items_par_charakter`
  MODIFY `ID_ITEMS_PAR_CHARAKTER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pro tabulku `items_par_jump`
--
ALTER TABLE `items_par_jump`
  MODIFY `ID_ITEMS_PAR_JUMP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `items_par_stories`
--
ALTER TABLE `items_par_stories`
  MODIFY `ID_ITEMS_PAR_STORIES` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `jumps`
--
ALTER TABLE `jumps`
  MODIFY `ID_JUMP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pro tabulku `paragraphs`
--
ALTER TABLE `paragraphs`
  MODIFY `ID_PARAGRAPH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pro tabulku `stories`
--
ALTER TABLE `stories`
  MODIFY `ID_STORIES` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `stories_par_charakter`
--
ALTER TABLE `stories_par_charakter`
  MODIFY `ID_STORIES_PAR_CHARAKTER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `ID_USER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `charakters`
--
ALTER TABLE `charakters`
  ADD CONSTRAINT `charakters_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `users` (`ID_USER`);

--
-- Omezení pro tabulku `conditions`
--
ALTER TABLE `conditions`
  ADD CONSTRAINT `conditions_ibfk_1` FOREIGN KEY (`ID_ITEM`) REFERENCES `items` (`ID_ITEM`);

--
-- Omezení pro tabulku `conditions_par`
--
ALTER TABLE `conditions_par`
  ADD CONSTRAINT `conditions_par_ibfk_1` FOREIGN KEY (`ID_CONDITION`) REFERENCES `conditions` (`ID_CONDITION`),
  ADD CONSTRAINT `conditions_par_ibfk_4` FOREIGN KEY (`ID_JUMP`) REFERENCES `jumps` (`ID_JUMP`);

--
-- Omezení pro tabulku `items_par_charakter`
--
ALTER TABLE `items_par_charakter`
  ADD CONSTRAINT `items_par_charakter_ibfk_1` FOREIGN KEY (`ID_CHARAKTER`) REFERENCES `charakters` (`ID_CHARAKTER`),
  ADD CONSTRAINT `items_par_charakter_ibfk_2` FOREIGN KEY (`ID_ITEM`) REFERENCES `items` (`ID_ITEM`);

--
-- Omezení pro tabulku `items_par_jump`
--
ALTER TABLE `items_par_jump`
  ADD CONSTRAINT `items_par_jump_ibfk_1` FOREIGN KEY (`ID_ITEM`) REFERENCES `items` (`ID_ITEM`),
  ADD CONSTRAINT `items_par_jump_ibfk_2` FOREIGN KEY (`ID_JUMP`) REFERENCES `jumps` (`ID_JUMP`);

--
-- Omezení pro tabulku `items_par_stories`
--
ALTER TABLE `items_par_stories`
  ADD CONSTRAINT `items_par_stories_ibfk_1` FOREIGN KEY (`ID_ITEM`) REFERENCES `items` (`ID_ITEM`),
  ADD CONSTRAINT `items_par_stories_ibfk_2` FOREIGN KEY (`ID_STORY`) REFERENCES `stories` (`ID_STORIES`);

--
-- Omezení pro tabulku `jumps`
--
ALTER TABLE `jumps`
  ADD CONSTRAINT `jumps_ibfk_1` FOREIGN KEY (`ID_PARAGRAPH_FROM`) REFERENCES `paragraphs` (`ID_PARAGRAPH`),
  ADD CONSTRAINT `jumps_ibfk_2` FOREIGN KEY (`ID_PARAGRAPH_TO`) REFERENCES `paragraphs` (`ID_PARAGRAPH`);

--
-- Omezení pro tabulku `paragraphs`
--
ALTER TABLE `paragraphs`
  ADD CONSTRAINT `paragraphs_ibfk_1` FOREIGN KEY (`ID_STORY`) REFERENCES `stories` (`ID_STORIES`);

--
-- Omezení pro tabulku `stories_par_charakter`
--
ALTER TABLE `stories_par_charakter`
  ADD CONSTRAINT `stories_par_charakter_ibfk_1` FOREIGN KEY (`ID_CHARAKTER`) REFERENCES `charakters` (`ID_CHARAKTER`),
  ADD CONSTRAINT `stories_par_charakter_ibfk_2` FOREIGN KEY (`ID_STORy`) REFERENCES `stories` (`ID_STORIES`),
  ADD CONSTRAINT `stories_par_charakter_ibfk_3` FOREIGN KEY (`ID_PARAGRAPH`) REFERENCES `paragraphs` (`ID_PARAGRAPH`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
