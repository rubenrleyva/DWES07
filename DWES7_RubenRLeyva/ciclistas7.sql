-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-04-2016 a las 13:32:17
-- Versión del servidor: 5.6.20
-- Versión de PHP: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ciclistas7`
--
CREATE DATABASE IF NOT EXISTS `ciclistas7` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ciclistas7`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

DROP TABLE IF EXISTS `carrera`;
CREATE TABLE IF NOT EXISTS `carrera` (
`cod_carrera` int(11) NOT NULL,
  `nombre` varchar(255) CHARACTER SET latin1 NOT NULL,
  `fecha` date NOT NULL,
  `punto_inicio` int(11) NOT NULL,
  `punto_fin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `punto_paso`
--

DROP TABLE IF EXISTS `punto_paso`;
CREATE TABLE IF NOT EXISTS `punto_paso` (
  `latitud` decimal(10,7) NOT NULL,
  `longitud` decimal(10,7) NOT NULL,
  `altura` decimal(10,7) NOT NULL,
  `cod_carrera` int(11) NOT NULL,
`cod_punto` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=9 ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
 ADD PRIMARY KEY (`cod_carrera`);

--
-- Indices de la tabla `punto_paso`
--
ALTER TABLE `punto_paso`
 ADD PRIMARY KEY (`cod_punto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
MODIFY `cod_carrera` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `punto_paso`
--
ALTER TABLE `punto_paso`
MODIFY `cod_punto` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
