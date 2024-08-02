-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2021 a las 18:22:28
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `informes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `table_nalcauses`
--

CREATE TABLE `table_nalcauses` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `qualities_id` int(11) NOT NULL,
  `causa` int(11) NOT NULL,
  `muestra` int(11) NOT NULL,
  `valor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `table_nalcauses`
--

INSERT INTO `table_nalcauses` (`id`, `fecha`, `qualities_id`, `causa`, `muestra`, `valor`) VALUES
(22, '2021-01-27 13:43:33', 14, 1, 1, 1),
(23, '2021-01-27 13:43:33', 14, 8, 1, 1),
(26, '2021-01-27 14:56:03', 17, 1, 10, 10),
(27, '2021-01-27 15:08:55', 23, 1, 10, 10);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `table_nalcauses`
--
ALTER TABLE `table_nalcauses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_qualities_id` (`qualities_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `table_nalcauses`
--
ALTER TABLE `table_nalcauses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `table_nalcauses`
--
ALTER TABLE `table_nalcauses`
  ADD CONSTRAINT `table_nalcauses_ibfk_1` FOREIGN KEY (`qualities_id`) REFERENCES `table_qualities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
