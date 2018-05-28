-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Versión del servidor: 5.1.58
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `taquillaVirtualCine`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Boleta`
--

CREATE TABLE IF NOT EXISTS `Pelicula` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `funcion` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `Boleta`
--

INSERT INTO `Pelicula` (`id`, `nombre`, `funcion`) VALUES
(1, 'Los Vengadores', 'Sabado 3 de la tarde'),
(2, 'Capitan America', 'Sabado 6 de la tarde'),
(3, 'Los juegos del hambre', 'Sabado 9 de la noche');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Reserva`
--

CREATE TABLE IF NOT EXISTS `Reserva` (
  `nro_reserva` int(6) NOT NULL AUTO_INCREMENT,
  `total` int(9) NOT NULL,
  `fecha_reserva` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `puntuacion_servicio` int(1) NOT NULL DEFAULT '0',
  `usuario` int(6) NOT NULL,
  `pelicula` int(6) NOT NULL,
  PRIMARY KEY (`nro_reserva`),
  KEY `fk_Orden_Usuario1_idx` (`usuario`), 
  KEY `fk_Orden_Pelicula1_idx` (`pelicula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `Reserva`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipo_Boleta`
--

CREATE TABLE IF NOT EXISTS `Tipo_Boleta` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `tipo_funcion` varchar(30) NOT NULL,
  `precio` int(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo_UNIQUE` (`tipo_funcion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `Tipo_Boleta`
--

INSERT INTO `Tipo_Boleta` (`id`, `tipo_funcion`, `precio`) VALUES
(1, '3D', 13000),
(2, '2D', 11000),
(3, '4D', 15000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE IF NOT EXISTS `Usuario` (
  `codigo` int(6) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `ciudad` varchar(45) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Usuario`
--

INSERT INTO `Usuario` (`codigo`, `cedula`, `nombre`, `direccion`, `telefono`, `ciudad`) VALUES
(1, '1042708194', 'Pepito Perez', 'Cra 84 a # 49 a - 23', '555 55 55', 'Medellin'),
(2, '1214714151', 'Juliana Julion', 'Cra 9 b # 49 a - 25', '777 77 77', 'Medellin');

--
-- Filtros para las tablas descargadas (dump)
--
--
-- Filtros para la tabla `Reserva`
--
ALTER TABLE `Reserva`
  ADD CONSTRAINT `fk_Orden_Usuario1` FOREIGN KEY (`usuario`) REFERENCES `Usuario` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Orden_Pelicula1` FOREIGN KEY (`pelicula`) REFERENCES `Pelicula` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

