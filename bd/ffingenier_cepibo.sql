-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-02-2017 a las 21:42:50
-- Versión del servidor: 10.0.27-MariaDB-cll-lve
-- Versión de PHP: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ffingenier_cepibo`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_material`(IN `usuario_reg_e` VARCHAR(50), IN `id_almacen_e` INT, IN `codigo_e` VARCHAR(20), IN `nombre_e` VARCHAR(250), IN `descripcion_e` VARCHAR(350), IN `stock_minimo_e` DECIMAL(10,2), IN `unidad_medida_e` VARCHAR(20), IN `stock_e` DECIMAL(10,2), IN `stock_requerido_e` INT, IN `tipo_e` VARCHAR(30), OUT `salida` TEXT)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
    set salida = 'Ocurrió un error. [db]';
END;
set @now = now();
START TRANSACTION;
set @codigo = codigo_e; set @val = (select count(id) from materiales where nombre = upper(trim(nombre_e)));

if (@val = 0) then
	INSERT INTO materiales(codigo,nombre,descripcion,stock_minimo,stock,unidad_medida,stock_requerido,id_almacen,tipo,created_at)
    VALUES
    (upper(@codigo),upper(trim(nombre_e)),upper(descripcion_e),stock_minimo_e,stock_e,unidad_medida_e,stock_requerido_e,id_almacen_e,upper(tipo_e),@now);
    set @val = (select count(id) from materiales where nombre = upper(trim(nombre_e)) and created_at = @now);
    if (@val = 1) then
		COMMIT;
        set salida = 'OK';
	else
		set salida = 'Ocurrió un error, inténtelo más tarde.';
    end if;
else
	set salida = concat('El ',tipo_e,' ya se encuentra registrado.');
end if;

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_productor`(IN `usuario_reg_e` VARCHAR(50), IN `nombres_e` VARCHAR(150), IN `apellidos_e` VARCHAR(150), IN `dni_e` VARCHAR(8), IN `genero_e` CHAR(1), OUT `salida` TEXT)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
    set salida = 'Ocurrió un error. [db]';
END;

START TRANSACTION;

set @now = now();

set @val = (select count(id) from productores where trim(dni) = trim(dni_e));
if (@val = 0) then
	set @codigo = (select concat('',lpad(ifnull(Max(substring(codigo,1)),0)+1,10,'0')) from productores);
	INSERT INTO productores(nombres,apellidos,dni,genero,codigo,created_at)
    VALUES
    (upper(nombres_e),upper(apellidos_e),dni_e,upper(genero_e),@codigo,@now);
    set @id_productor = (select id from productores where trim(dni) = trim(dni_e) and created_at = @now);
    if (@id_productor <> '') then
		COMMIT;
                set salida = 'OK';
	else 
		set salida = 'Ocurrió un error, inténtelo más tarde.';
    end if;
else
	set salida = 'El dni ya está registrado.';
end if;

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_productor_asociacion`(IN `id_productor_e` INT, IN `id_asociacion_e` INT)
BEGIN

set @val = (select count(id) from productor_asociacion 
where id_productor = id_productor_e and id_asociacion = id_asociacion_e and activo = '1');
if (@val = 0) then
	INSERT INTO productor_asociacion(id_productor,id_asociacion)
	VALUES (id_productor_e,id_asociacion_e);
end if;

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_productor_terreno`(IN `id_productor_e` INT, IN `id_terreno_e` INT, IN `id_asociacion_e` INT, IN `observacion_e` TEXT, IN `condicion_e` VARCHAR(20), IN `documentacion_e` VARCHAR(350), IN `url_docs_e` TEXT, OUT `salida` TEXT)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
    set salida = 'ERROR';
END;
START TRANSACTION;

set @now = now();
INSERT INTO productor_terreno(id_productor,id_asociacion,id_terreno,observacion,condicion,documentacion,url_docs,created_at)
VALUES
(id_productor_e,id_asociacion_e,id_terreno_e,upper(observacion_e),upper(condicion_e),upper(documentacion_e),url_docs_e,@now);
set @val = (select count(*) from productor_terreno where id_productor = id_productor_e and id_terreno = id_terreno_e and condicion = condicion_e and created_at = @now);
if (@val > 0) then
	COMMIT;
	set salida = 'OK';
else 
	set salida = 'ERROR';
end if;

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_terreno`(IN `usuario_reg_e` VARCHAR(50), IN `id_productor_e` INT, IN `id_asociacion_e` INT, IN `area_total_e` DECIMAL(10,2), IN `area_cultivo_e` DECIMAL(10,2), IN `area_desarrollo_e` DECIMAL(10,2), IN `referencia_e` VARCHAR(250), IN `certificacion_e` VARCHAR(250), IN `condicion_e` VARCHAR(20), IN `documentacion_e` VARCHAR(350), IN `observacion_e` TEXT, OUT `salida` TEXT)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	ROLLBACK;
    set salida = 'Ocurrió un error. [db]';
END;

START TRANSACTION;
set @now = now();

set @codigo = (select concat('T',lpad(ifnull(Max(substring(codigo,2)),0)+1,10,'0')) from terrenos);

INSERT INTO terrenos(codigo,area_total,area_cultivo,area_desarrollo,referencia,certificacion,created_at)
VALUES
(@codigo,area_total_e,area_cultivo_e,area_desarrollo_e,upper(referencia_e),upper(certificacion_e),@now);

set @id_terreno = (select id from terrenos where area_total = area_total_e and created_at = @now);
if (@id_terreno <> '') then
	CALL sp_registrar_productor_terreno(id_productor_e, @id_terreno,id_asociacion_e, observacion_e, condicion_e, documentacion_e, '',@s);
    if (@s = 'OK') then
		COMMIT;
                CALL sp_registrar_productor_asociacion(id_productor_e, id_asociacion_e);
        set salida = 'OK';
	else 
		set salida = 'Ocurrió un error.';
    end if;
else
	set salida = 'Ocurrió un error. Inténtelo más tarde.';
end if;

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_trabajador`(IN `usuario_reg_e` VARCHAR(50), IN `nombres_e` VARCHAR(50), IN `apellidos_e` VARCHAR(50), IN `dni_e` CHAR(8), IN `direccion_e` TEXT, IN `telefono_e` VARCHAR(20), IN `celular_e` VARCHAR(20), IN `email_e` VARCHAR(150), IN `genero_e` CHAR(1), IN `id_asociacion_e` INT, IN `id_cargo_e` INT, OUT `salida` TEXT)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
	set salida = 'Ocurrió un Error. [db]';
	ROLLBACK;
END;
START TRANSACTION;

set @now = now();

set @val = (SELECT count(*) FROM trabajador WHERE dni = dni_e);

if (@val = 0) then
	INSERT INTO trabajador(`usuario_reg`, `nombres`, `apellidos`, `dni`, `direccion`, `telefono`, `celular`, `email`, `genero`, created_at)
    VALUES (
    usuario_reg_e, upper(nombres_e), upper(apellidos_e), dni_e, upper(direccion_e), telefono_e, celular_e, email_e, upper(genero_e), @now
    );
    set @id_trabajador = (select id from trabajador where dni = dni_e and created_at = @now);
    if (@id_trabajador <> '') then
		COMMIT;
        set salida = 'OK';
		CALL sp_registrar_trabajador_asociacion(@id_trabajador,id_asociacion_e);
		CALL sp_registrar_trabajador_cargo(@id_trabajador,id_cargo_e);
	else 
		set salida = 'Ocurrió un error, inténtelo más tarde.';
    end if;
else
	set salida = 'El dni ya está registrado.';
end if;

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_trabajador_asociacion`(IN `id_trabajador_e` INT, IN `id_asociacion_e` INT)
BEGIN

INSERT INTO trabajador_asociacion(id_trabajador,id_asociacion)
VALUES (id_trabajador_e,id_asociacion_e);

END$$

CREATE DEFINER=`ffingenier`@`localhost` PROCEDURE `sp_registrar_trabajador_cargo`(IN `id_trabajador_e` INT, IN `id_cargo_e` INT)
BEGIN

set @val = (select count(*) from trabajador_cargo where id_trabajador = id_trabajador_e);

if (@val > 0) then
	UPDATE trabajador_cargo SET activo = '0', updated_at = now() WHERE id_trabajador = id_trabajador_e;
end if;

INSERT INTO trabajador_cargo(id_trabajador,id_cargo)
VALUES (id_trabajador_e,id_cargo_e);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE IF NOT EXISTS `almacen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `lat_lng` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `almacen`
--

INSERT INTO `almacen` (`id`, `nombre`, `lat_lng`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'ALMACEN CENTRAL', NULL, 1, '2016-11-21 20:20:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asociaciones`
--

CREATE TABLE IF NOT EXISTS `asociaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nombre_comercial` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ruc` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Volcado de datos para la tabla `asociaciones`
--

INSERT INTO `asociaciones` (`id`, `nombre`, `nombre_comercial`, `ruc`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'ABOSPA', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(2, 'APADISE', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(3, 'APBO Y PAE', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(4, 'APOCSUR', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(5, 'APPCHAQ', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(6, 'APROBO', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(7, 'SAN MIGUEL DE TANGARARA', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(8, 'CAPPBOSSA', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(9, 'GRUPO EMPRENDEDORES', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(10, 'AMPROBOH', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(11, 'APBOSANV-PR', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(12, 'ASPBOM', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(13, 'UBCHAB', NULL, NULL, 1, '2016-11-15 03:26:23', NULL),
(14, 'CIDEX S.A.C', NULL, NULL, 1, '2016-11-15 03:26:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asociacion_empacadora`
--

CREATE TABLE IF NOT EXISTS `asociacion_empacadora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_asociacion` int(11) NOT NULL,
  `id_empacadora` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `asociacion_empacadora`
--

INSERT INTO `asociacion_empacadora` (`id`, `id_asociacion`, `id_empacadora`, `estado`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 1, '2016-11-30 06:54:25', NULL),
(2, 6, 2, 1, '2016-11-30 06:54:25', NULL),
(3, 1, 5, 1, '2016-12-01 08:53:23', NULL),
(4, 10, 6, 1, '2016-12-07 00:32:14', NULL),
(5, 2, 7, 1, '2016-12-07 02:29:00', NULL),
(6, 3, 8, 1, '2016-12-07 02:29:09', NULL),
(7, 3, 9, 1, '2016-12-07 02:29:15', NULL),
(8, 3, 10, 1, '2016-12-07 02:29:58', NULL),
(9, 4, 11, 1, '2016-12-07 02:32:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE IF NOT EXISTS `cargos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'OBRERO', 1, '2016-11-15 02:59:08', NULL),
(2, 'JEFE DE CUADRILLA', 1, '2016-11-15 02:59:08', NULL),
(3, 'TRABAJADOR', 1, '2016-11-23 17:34:47', NULL),
(4, 'SA', 2, '2016-11-15 03:06:25', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'CLIENTE A', 1, '2016-11-28 18:47:14', NULL),
(2, 'CLIENTE X', 1, '2016-11-29 03:46:24', NULL),
(3, 'CLIENTE C', 1, '2016-11-29 04:20:28', NULL),
(4, 'CLIENTE D', 1, '2016-11-30 06:04:13', NULL),
(5, 'CLIENTE NUEVO', 1, '2016-12-01 18:23:39', NULL),
(6, 'CLIENTE NUEVO X', 1, '2016-12-07 02:27:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenedor`
--

CREATE TABLE IF NOT EXISTS `contenedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marca` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `modelo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payload` decimal(10,2) NOT NULL,
  `largo` decimal(10,2) DEFAULT NULL,
  `ancho` decimal(10,2) DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `certificacion` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `contenedor`
--

INSERT INTO `contenedor` (`id`, `numero`, `descripcion`, `marca`, `modelo`, `payload`, `largo`, `ancho`, `altura`, `certificacion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'CONTENEDOR A', '', '', '', '0.00', '0.00', '0.00', '0.00', '', 1, '2016-11-29 03:47:27', NULL),
(2, '734892-A', '', '', '', '0.00', '0.00', '0.00', '0.00', '', 1, '2016-11-29 16:35:45', NULL),
(3, 'CN-09326', '', '', '', '0.00', '0.00', '0.00', '0.00', '', 1, '2016-12-01 02:06:02', NULL),
(4, 'CONT-0001', '', '', '', '0.00', '0.00', '0.00', '0.00', '', 1, '2016-12-01 18:24:31', NULL),
(5, '982374', '', '', '', '0.00', '0.00', '0.00', '0.00', '', 1, '2016-12-07 02:27:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuadrillas`
--

CREATE TABLE IF NOT EXISTS `cuadrillas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_asociacion` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

--
-- Volcado de datos para la tabla `cuadrillas`
--

INSERT INTO `cuadrillas` (`id`, `id_asociacion`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'CUADRILLA 1', 1, '2016-11-22 05:27:09', NULL),
(2, 1, 'CUADRILLA 2', 1, '2016-11-22 05:27:09', NULL),
(3, 1, 'CUADRILLA 3', 1, '2016-11-22 05:27:09', NULL),
(4, 1, 'CUADRILLA 4', 1, '2016-11-22 05:27:09', NULL),
(5, 1, 'CUADRILLA 5', 1, '2016-11-22 05:27:09', NULL),
(6, 2, 'CUADRILLA 1', 1, '2016-11-22 05:27:45', NULL),
(7, 2, 'CUADRILLA 2', 1, '2016-11-22 05:27:45', NULL),
(8, 2, 'CUADRILLA 3', 1, '2016-11-22 05:27:45', NULL),
(9, 2, 'CUADRILLA 4', 1, '2016-11-22 05:27:45', NULL),
(10, 2, 'CUADRILLA 5', 1, '2016-11-22 05:27:45', NULL),
(11, 3, 'CUADRILLA 1', 1, '2016-11-22 05:27:45', NULL),
(12, 3, 'CUADRILLA 2', 1, '2016-11-22 05:27:45', NULL),
(13, 3, 'CUADRILLA 3', 1, '2016-11-22 05:27:45', NULL),
(14, 3, 'CUADRILLA 4', 1, '2016-11-22 05:27:45', NULL),
(15, 3, 'CUADRILLA 5', 1, '2016-11-22 05:27:45', NULL),
(16, 4, 'CUADRILLA 1', 1, '2016-11-23 16:02:35', NULL),
(17, 4, 'CUADRILLA 2', 1, '2016-11-23 16:02:46', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empacadoras`
--

CREATE TABLE IF NOT EXISTS `empacadoras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `empacadoras`
--

INSERT INTO `empacadoras` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'EMP 1', NULL, 1, '2016-11-16 06:01:18', NULL),
(2, 'EMP 2', NULL, 1, '2016-11-16 06:01:18', NULL),
(3, 'AMP 4', NULL, 1, '2016-12-01 08:49:29', NULL),
(4, 'EMP 3', NULL, 1, '2016-12-01 08:50:48', NULL),
(5, 'EMP 4', NULL, 1, '2016-12-01 08:53:23', NULL),
(6, 'EMPACADORA EJM1', NULL, 1, '2016-12-07 00:32:14', NULL),
(7, 'EMP X', NULL, 1, '2016-12-07 02:28:59', NULL),
(8, 'EMP X', NULL, 1, '2016-12-07 02:29:09', NULL),
(9, 'EMP X', NULL, 1, '2016-12-07 02:29:15', NULL),
(10, 'EMP X', NULL, 1, '2016-12-07 02:29:58', NULL),
(11, 'EMP X', NULL, 1, '2016-12-07 02:32:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_material`
--

CREATE TABLE IF NOT EXISTS `ingreso_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_material` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `origen` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proveedor` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8_unicode_ci,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `ingreso_material`
--

INSERT INTO `ingreso_material` (`id`, `id_material`, `cantidad`, `origen`, `proveedor`, `observacion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 2, '40.00', '', 'proveedor X', '', 1, '2016-11-22 04:45:46', NULL),
(2, 2, '10.00', '', 'proveedor y', '', 1, '2016-11-22 04:47:32', NULL),
(3, 2, '10.00', '', 'proveedor x', '', 1, '2016-11-22 04:50:12', NULL),
(4, 2, '10.00', '', 'proveedor x', '', 1, '2016-11-22 04:50:26', NULL),
(5, 2, '10.00', '', 'proveedor x', '', 1, '2016-11-22 04:50:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE IF NOT EXISTS `materiales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stock` decimal(10,2) NOT NULL,
  `stock_minimo` decimal(10,2) NOT NULL,
  `stock_requerido` int(11) NOT NULL DEFAULT '1',
  `unidad_medida` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `tipo` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'MATERIAL',
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `codigo`, `nombre`, `descripcion`, `stock`, `stock_minimo`, `stock_requerido`, `unidad_medida`, `id_almacen`, `tipo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'C0001', 'EJEMPLO', '', '-557.50', '40.00', 1, 'UND', 1, 'MATERIAL', 1, '2016-11-22 00:03:17', NULL),
(2, 'C0002', 'EJEMPLO 2', '', '-20.00', '0.00', 0, 'LTS', 1, 'INSUMO', 1, '2016-11-22 00:04:32', NULL),
(3, 'C0003', 'EJEMPLO 3', '', '-94.40', '0.00', 0, 'LTS', 1, 'INSUMO', 1, '2016-11-22 06:04:30', NULL),
(4, 'C0004', 'EJEMPLO 4', '', '-245.00', '40.00', 1, 'MLL', 1, 'INSUMO', 1, '2016-11-22 06:05:59', NULL),
(5, 'C0005', 'EJEMPLO 5', '', '-387.00', '120.00', 1, 'PAQUETE', 1, 'MATERIAL', 1, '2016-11-22 06:06:49', NULL),
(6, 'C0006', 'EJEMPLO 6', '', '-100.00', '200.00', 1, 'UND', 1, 'MATERIAL', 1, '2016-11-22 06:07:18', NULL),
(7, 'C0007', 'AGUA', '', '-40.00', '0.00', 0, 'LTS', 1, 'INSUMO', 1, '2016-12-14 08:18:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_tipo_caja`
--

CREATE TABLE IF NOT EXISTS `material_tipo_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo_caja` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `multiplo` decimal(10,2) NOT NULL,
  `calcular` int(11) NOT NULL DEFAULT '1',
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Volcado de datos para la tabla `material_tipo_caja`
--

INSERT INTO `material_tipo_caja` (`id`, `id_tipo_caja`, `id_material`, `multiplo`, `calcular`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1.50', 1, 1, '2016-11-22 06:20:53', NULL),
(2, 1, 2, '40.00', 0, 1, '2016-11-22 06:20:53', NULL),
(3, 1, 3, '0.12', 1, 1, '2016-11-22 06:20:53', NULL),
(4, 1, 4, '1.00', 1, 0, '2016-11-22 06:20:53', NULL),
(5, 1, 5, '1.00', 1, 1, '2016-11-22 06:20:53', NULL),
(6, 4, 1, '1.40', 1, 1, '2016-11-22 23:53:23', NULL),
(7, 4, 5, '0.01', 1, 1, '2016-11-22 23:53:28', NULL),
(8, 1, 4, '2.00', 1, 1, '2016-11-22 23:55:56', NULL),
(9, 1, 6, '40.00', 0, 1, '2016-11-22 23:56:23', NULL),
(10, 4, 6, '1.00', 1, 0, '2016-11-23 03:37:57', NULL),
(11, 4, 3, '65.00', 0, 1, '2016-11-30 03:29:25', NULL),
(12, 3, 2, '40.00', 0, 1, '2016-12-01 18:13:16', NULL),
(13, 3, 6, '1.00', 1, 1, '2016-12-01 18:13:35', NULL),
(14, 2, 7, '40.00', 0, 1, '2016-12-14 08:19:05', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_padre` int(11) DEFAULT '0',
  `descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `class_icon` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `nombre_pagina` varchar(350) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'javascript:void(0)',
  `url` varchar(350) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scope` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ADMIN',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `id_padre`, `descripcion`, `class_icon`, `orden`, `activo`, `nombre_pagina`, `url`, `scope`, `created_at`, `updated_at`) VALUES
(1, 0, 'Home', 'fa fa-home', 100, 1, 'home.php', NULL, 'ADMIN', '2016-11-14 22:11:53', NULL),
(2, 0, 'Producción', 'fa fa-industry', 200, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(3, 0, 'Almacén', 'fa fa-bars', 300, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(4, 0, 'Logística', 'fa fa-arrows-v', 400, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(5, 0, 'RR.HH.', 'fa fa-users', 500, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(6, 0, 'A.Técnica', 'fa fa-wrench', 600, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(7, 0, 'C.I.P.', 'fa fa-eye', 700, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(8, 0, 'Sistemas', 'fa fa-keyboard-o', 1000, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-14 22:14:13', NULL),
(9, 5, 'Ges. Personal', NULL, 510, 1, 'ges_personal.php', NULL, 'ADMIN', '2016-11-15 03:48:53', NULL),
(10, 5, 'Usuarios', NULL, 520, 1, 'usuarios.php', NULL, 'ADMIN', '2016-11-15 03:50:56', NULL),
(11, 0, 'Perfil', 'fa fa-user', 800, 1, 'mi_perfil.php', NULL, 'ADMIN', '2016-11-15 03:51:51', NULL),
(12, 0, 'Asociaciones', 'fa fa-circle-o-notch', 900, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-11-16 07:48:14', NULL),
(13, 12, 'Productor', '', 910, 1, 'productor.php', NULL, 'ADMIN', '2016-11-16 07:51:05', NULL),
(14, 12, 'Terrenos', NULL, 920, 1, 'terrenos.php', NULL, 'ADMIN', '2016-11-16 15:39:40', NULL),
(15, 3, 'Material / Insumo', NULL, 310, 1, 'materiales.php', NULL, 'ADMIN', '2016-11-21 20:34:37', NULL),
(16, 3, 'Ingreso Material', NULL, 320, 0, 'ingreso_material.php', '', 'ADMIN', '2016-11-22 04:54:03', NULL),
(17, 3, 'Salida Material', NULL, 330, 1, 'salida_material.php', '', 'ADMIN', '2016-11-22 04:54:03', NULL),
(18, 3, 'Tipo Caja', NULL, 340, 1, 'tipo_caja.php', NULL, 'ADMIN', '2016-11-22 20:40:31', NULL),
(19, 5, 'Cuadrillas', NULL, 530, 1, 'cuadrillas.php', NULL, 'ADMIN', '2016-11-23 15:27:49', NULL),
(20, 2, 'Packing', NULL, 210, 1, 'registro_packing.php', NULL, 'ADMIN', '2016-11-28 16:53:43', NULL),
(21, 12, 'Empacadoras', NULL, 930, 1, 'empacadoras.php', NULL, 'ADMIN', '2016-12-01 08:20:47', NULL),
(22, 0, 'Reportes', 'fa fa-area-chart', 1000, 1, 'javascript:void(0)', NULL, 'ADMIN', '2016-12-06 20:23:59', NULL),
(23, 22, 'Asociaciones', NULL, 1010, 1, 'rep_asociaciones.php', NULL, 'ADMIN', '2016-12-06 20:26:03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packing`
--

CREATE TABLE IF NOT EXISTS `packing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `f_llegada_contenedor` datetime NOT NULL,
  `f_inicio_llenado` datetime NOT NULL,
  `f_fin_llenado` datetime NOT NULL,
  `f_salida_contenedor` datetime NOT NULL,
  `nro_termoregistro` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nro_guia` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nro_semana` int(11) NOT NULL,
  `id_contenedor` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vapor` int(11) NOT NULL,
  `id_tipo_funda` int(11) NOT NULL,
  `id_puerto_origen` int(11) NOT NULL,
  `id_puerto_destino` int(11) NOT NULL,
  `usuario_reg` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '2',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `packing`
--

INSERT INTO `packing` (`id`, `codigo`, `f_llegada_contenedor`, `f_inicio_llenado`, `f_fin_llenado`, `f_salida_contenedor`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `id_contenedor`, `id_cliente`, `id_vapor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `usuario_reg`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'PK0000000001', '2016-11-29 12:23:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '93849', '9875', 48, 2, 2, 2, 2, 3, 4, 'admin', 2, '2016-11-29 19:10:10', NULL),
(2, 'PK0000000002', '2016-11-23 14:20:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '9878', '734', 47, 2, 3, 5, 3, 3, 4, 'admin', 2, '2016-11-29 19:21:02', NULL),
(3, 'PK0000000003', '2016-11-29 14:22:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '9879', '638', 48, 1, 2, 3, 2, 4, 3, 'admin', 2, '2016-11-29 19:23:00', NULL),
(4, 'PK0000000004', '2016-11-17 20:52:00', '2016-11-18 08:55:00', '2016-11-18 20:00:00', '2016-11-18 20:52:00', '90823', '30984', 46, 2, 3, 5, 3, 3, 2, 'admin', 1, '2016-11-29 19:25:54', '2016-11-30 20:53:55'),
(5, 'PK0000000005', '2016-11-29 14:27:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-11-29 14:27:00', 'j98', '09390j', 48, 1, 2, 2, 1, 4, 1, 'admin', 2, '2016-11-29 19:28:28', NULL),
(6, 'PK0000000006', '2016-11-29 21:05:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 't00928', 'g-0092', 48, 3, 1, 6, 1, 2, 1, 'admin', 2, '2016-12-01 02:06:32', NULL),
(7, 'PK0000000007', '2016-12-01 08:23:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '90349', '8394', 48, 4, 5, 7, 1, 3, 4, 'admin', 2, '2016-12-01 18:26:05', NULL),
(8, 'PK0000000008', '2016-12-01 13:44:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '9023874', '83945', 48, 2, 5, 4, 2, 3, 2, 'admin', 2, '2016-12-01 18:46:35', NULL),
(9, 'PK0000000009', '2016-12-05 21:26:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '92732', '38902', 49, 5, 6, 1, 3, 1, 2, 'admin', 2, '2016-12-07 02:27:47', NULL),
(10, 'PK0000000010', '2016-12-13 15:06:00', '2016-12-14 15:06:00', '2016-12-15 15:05:00', '2016-12-15 15:06:00', '30', '300', 50, 2, 4, 2, 1, 4, 3, 'admin', 2, '2016-12-18 20:08:30', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packing_list`
--

CREATE TABLE IF NOT EXISTS `packing_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_packing` int(11) NOT NULL,
  `id_productor_terreno` int(11) NOT NULL,
  `id_tipo_caja` int(11) NOT NULL,
  `id_asociacion_empacadora` int(11) NOT NULL,
  `f_corte` datetime NOT NULL,
  `nro_cajas` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

--
-- Volcado de datos para la tabla `packing_list`
--

INSERT INTO `packing_list` (`id`, `id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `estado`, `created_at`, `updated_at`) VALUES
(3, 4, 6, 4, 2, '2016-11-30 00:00:00', 12, 1, '2016-11-30 08:24:52', NULL),
(4, 4, 6, 6, 1, '2016-11-30 00:00:00', 12, 1, '2016-11-30 09:14:11', NULL),
(5, 4, 6, 5, 1, '2016-11-30 00:00:00', 12, 1, '2016-11-30 09:27:33', NULL),
(6, 4, 6, 5, 2, '2016-11-30 00:00:00', 12, 1, '2016-11-30 09:28:16', NULL),
(7, 4, 6, 5, 1, '2016-11-30 00:00:00', 2, 1, '2016-11-30 09:33:08', NULL),
(8, 4, 3, 4, 2, '2016-11-30 00:00:00', 1, 1, '2016-11-30 09:52:44', NULL),
(9, 2, 3, 6, 2, '2016-11-30 00:00:00', 23, 1, '2016-11-30 10:11:35', NULL),
(10, 2, 3, 1, 2, '2016-11-30 00:00:00', 36, 1, '2016-12-01 00:50:26', NULL),
(11, 2, 3, 1, 1, '2016-11-30 00:00:00', 20, 1, '2016-12-01 00:51:07', NULL),
(12, 3, 3, 3, 2, '2016-12-01 00:00:00', 13, 1, '2016-12-01 08:06:35', NULL),
(13, 3, 3, 3, 2, '2016-12-01 00:00:00', 50, 1, '2016-12-01 08:07:02', NULL),
(14, 7, 1, 1, 5, '2016-11-30 00:00:00', 15, 1, '2016-12-01 18:31:00', NULL),
(15, 7, 3, 1, 2, '2016-11-30 00:00:00', 31, 1, '2016-12-01 18:32:01', NULL),
(16, 6, 1, 1, 5, '2016-12-01 00:00:00', 20, 1, '2016-12-01 18:40:33', NULL),
(17, 8, 3, 3, 1, '2016-12-01 00:00:00', 15, 1, '2016-12-01 18:48:18', NULL),
(18, 5, 1, 3, 5, '2016-12-06 00:00:00', 9, 1, '2016-12-06 21:29:27', NULL),
(19, 5, 1, 3, 3, '2016-12-06 00:00:00', 16, 1, '2016-12-06 22:05:30', NULL),
(20, 5, 3, 4, 1, '2016-12-06 00:00:00', 44, 1, '2016-12-07 00:30:58', NULL),
(21, 5, 6, 3, 4, '2016-12-06 00:00:00', 31, 1, '2016-12-07 00:32:43', NULL),
(22, 9, 1, 1, 3, '2016-12-05 00:00:00', 54, 1, '2016-12-07 02:28:28', NULL),
(23, 9, 2, 1, 5, '2016-12-05 00:00:00', 30, 1, '2016-12-07 02:29:32', NULL),
(24, 9, 8, 1, 9, '2016-12-05 00:00:00', 40, 1, '2016-12-07 02:33:58', NULL),
(25, 3, 5, 4, 3, '2016-12-18 00:00:00', 14, 1, '2016-12-18 20:10:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packing_list_detalle`
--

CREATE TABLE IF NOT EXISTS `packing_list_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_packing_list` int(11) NOT NULL,
  `nro_pallet` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=92 ;

--
-- Volcado de datos para la tabla `packing_list_detalle`
--

INSERT INTO `packing_list_detalle` (`id`, `id_packing_list`, `nro_pallet`, `cantidad`, `activo`, `created_at`, `updated_at`) VALUES
(5, 3, 1, 1, 1, '2016-11-30 08:24:52', NULL),
(6, 3, 2, 2, 1, '2016-11-30 08:24:52', NULL),
(7, 3, 3, 4, 1, '2016-11-30 08:24:53', NULL),
(8, 3, 4, 19, 1, '2016-11-30 08:24:53', NULL),
(9, 4, 6, 2, 1, '2016-11-30 09:14:12', NULL),
(10, 4, 9, 19, 1, '2016-11-30 09:14:12', NULL),
(11, 4, 12, 7, 1, '2016-11-30 09:14:12', NULL),
(12, 5, 1, 10, 1, '2016-11-30 09:27:33', NULL),
(13, 5, 2, 12, 1, '2016-11-30 09:27:33', NULL),
(14, 5, 7, 9, 1, '2016-11-30 09:27:33', NULL),
(15, 5, 8, 9, 1, '2016-11-30 09:27:34', NULL),
(16, 6, 1, 12, 1, '2016-11-30 09:28:16', NULL),
(17, 7, 2, 2, 1, '2016-11-30 09:33:08', NULL),
(18, 8, 1, 1, 1, '2016-11-30 09:52:44', NULL),
(19, 9, 1, 10, 1, '2016-11-30 10:11:35', NULL),
(20, 9, 2, 12, 1, '2016-11-30 10:11:35', NULL),
(21, 9, 3, 1, 1, '2016-11-30 10:11:35', NULL),
(22, 10, 1, 1, 1, '2016-12-01 00:50:26', NULL),
(23, 10, 6, 7, 1, '2016-12-01 00:50:26', NULL),
(24, 10, 10, 19, 1, '2016-12-01 00:50:26', NULL),
(25, 10, 14, 9, 1, '2016-12-01 00:50:26', NULL),
(26, 11, 1, 1, 1, '2016-12-01 00:51:07', NULL),
(27, 11, 2, 1, 1, '2016-12-01 00:51:07', NULL),
(28, 11, 3, 1, 1, '2016-12-01 00:51:07', NULL),
(29, 11, 4, 1, 1, '2016-12-01 00:51:07', NULL),
(30, 11, 5, 1, 1, '2016-12-01 00:51:07', NULL),
(31, 11, 6, 1, 1, '2016-12-01 00:51:07', NULL),
(32, 11, 7, 1, 1, '2016-12-01 00:51:07', NULL),
(33, 11, 8, 1, 1, '2016-12-01 00:51:07', NULL),
(34, 11, 9, 1, 1, '2016-12-01 00:51:07', NULL),
(35, 11, 10, 1, 1, '2016-12-01 00:51:07', NULL),
(36, 11, 11, 1, 1, '2016-12-01 00:51:07', NULL),
(37, 11, 12, 1, 1, '2016-12-01 00:51:07', NULL),
(38, 11, 13, 1, 1, '2016-12-01 00:51:07', NULL),
(39, 11, 14, 1, 1, '2016-12-01 00:51:07', NULL),
(40, 11, 15, 1, 1, '2016-12-01 00:51:07', NULL),
(41, 11, 16, 1, 1, '2016-12-01 00:51:07', NULL),
(42, 11, 17, 1, 1, '2016-12-01 00:51:07', NULL),
(43, 11, 18, 1, 1, '2016-12-01 00:51:07', NULL),
(44, 11, 19, 1, 1, '2016-12-01 00:51:07', NULL),
(45, 11, 20, 1, 1, '2016-12-01 00:51:07', NULL),
(46, 12, 1, 10, 1, '2016-12-01 08:06:35', NULL),
(47, 12, 2, 2, 1, '2016-12-01 08:06:35', NULL),
(48, 12, 3, 1, 1, '2016-12-01 08:06:35', NULL),
(49, 13, 5, 1, 1, '2016-12-01 08:07:02', NULL),
(50, 13, 6, 1, 1, '2016-12-01 08:07:02', NULL),
(51, 13, 7, 3, 1, '2016-12-01 08:07:02', NULL),
(52, 13, 8, 45, 1, '2016-12-01 08:07:02', NULL),
(53, 14, 1, 10, 1, '2016-12-01 18:31:00', NULL),
(54, 14, 5, 5, 1, '2016-12-01 18:31:00', NULL),
(55, 15, 9, 20, 1, '2016-12-01 18:32:01', NULL),
(56, 15, 10, 10, 1, '2016-12-01 18:32:01', NULL),
(57, 15, 11, 1, 1, '2016-12-01 18:32:01', NULL),
(58, 16, 2, 10, 1, '2016-12-01 18:40:33', NULL),
(59, 16, 3, 10, 1, '2016-12-01 18:40:33', NULL),
(60, 17, 2, 10, 1, '2016-12-01 18:48:18', NULL),
(61, 17, 3, 1, 1, '2016-12-01 18:48:18', NULL),
(62, 17, 4, 1, 1, '2016-12-01 18:48:18', NULL),
(63, 17, 5, 3, 1, '2016-12-01 18:48:18', NULL),
(64, 18, 1, 1, 1, '2016-12-06 21:29:27', NULL),
(65, 18, 2, 3, 1, '2016-12-06 21:29:27', NULL),
(66, 18, 3, 3, 1, '2016-12-06 21:29:27', NULL),
(67, 18, 6, 2, 1, '2016-12-06 21:29:27', NULL),
(68, 19, 1, 10, 1, '2016-12-06 22:05:30', NULL),
(69, 19, 2, 1, 1, '2016-12-06 22:05:30', NULL),
(70, 19, 3, 2, 1, '2016-12-06 22:05:30', NULL),
(71, 19, 6, 3, 1, '2016-12-06 22:05:30', NULL),
(72, 20, 1, 24, 1, '2016-12-07 00:30:58', NULL),
(73, 20, 2, 10, 1, '2016-12-07 00:30:58', NULL),
(74, 20, 3, 10, 1, '2016-12-07 00:30:58', NULL),
(75, 21, 7, 10, 1, '2016-12-07 00:32:43', NULL),
(76, 21, 8, 10, 1, '2016-12-07 00:32:43', NULL),
(77, 21, 9, 11, 1, '2016-12-07 00:32:43', NULL),
(78, 22, 1, 10, 1, '2016-12-07 02:28:28', NULL),
(79, 22, 2, 11, 1, '2016-12-07 02:28:28', NULL),
(80, 22, 3, 12, 1, '2016-12-07 02:28:28', NULL),
(81, 22, 9, 21, 1, '2016-12-07 02:28:28', NULL),
(82, 23, 7, 10, 1, '2016-12-07 02:29:32', NULL),
(83, 23, 8, 10, 1, '2016-12-07 02:29:32', NULL),
(84, 23, 9, 10, 1, '2016-12-07 02:29:32', NULL),
(85, 24, 6, 10, 1, '2016-12-07 02:33:58', NULL),
(86, 24, 7, 10, 1, '2016-12-07 02:33:58', NULL),
(87, 24, 8, 10, 1, '2016-12-07 02:33:58', NULL),
(88, 24, 9, 5, 1, '2016-12-07 02:33:58', NULL),
(89, 24, 10, 5, 1, '2016-12-07 02:33:58', NULL),
(90, 25, 3, 5, 1, '2016-12-18 20:10:04', NULL),
(91, 25, 5, 9, 1, '2016-12-18 20:10:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pallets`
--

CREATE TABLE IF NOT EXISTS `pallets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nro` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

--
-- Volcado de datos para la tabla `pallets`
--

INSERT INTO `pallets` (`id`, `nro`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 14),
(15, 15),
(16, 16),
(17, 17),
(18, 18),
(19, 19),
(20, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_menu`
--

CREATE TABLE IF NOT EXISTS `permisos_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_menu` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

--
-- Volcado de datos para la tabla `permisos_menu`
--

INSERT INTO `permisos_menu` (`id`, `id_menu`, `id_usuario`, `usuario`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'admin', 0, '2016-11-14 23:07:19', '2016-11-14 23:23:05'),
(2, 2, 1, 'admin', 0, '2016-11-14 23:59:51', '2016-11-14 19:10:52'),
(3, 3, 1, 'admin', 0, '2016-11-15 00:00:46', '2016-11-16 11:09:26'),
(4, 4, 1, 'admin', 0, '2016-11-15 00:01:04', '2016-11-16 11:09:31'),
(5, 5, 1, 'admin', 1, '2016-11-15 00:01:05', NULL),
(6, 6, 1, 'admin', 0, '2016-11-15 00:01:05', '2016-11-16 11:09:33'),
(7, 7, 1, 'admin', 0, '2016-11-15 00:01:06', '2016-11-14 19:11:00'),
(8, 8, 1, 'admin', 0, '2016-11-15 00:01:10', '2016-11-16 11:09:37'),
(12, 2, 1, 'admin', 0, '2016-11-15 00:11:17', '2016-11-16 11:09:24'),
(13, 10, 1, 'admin', 1, '2016-11-15 03:52:42', '2016-11-16 15:47:07'),
(14, 11, 1, 'admin', 1, '2016-11-15 03:52:42', NULL),
(15, 1, 1, 'admin', 1, '2016-11-15 04:26:18', NULL),
(16, 9, 1, 'admin', 1, '2016-11-15 04:26:28', NULL),
(17, 7, 1, 'admin', 0, '2016-11-15 04:26:36', '2016-11-14 23:53:40'),
(18, 12, 1, 'admin', 1, '2016-11-16 07:48:42', NULL),
(19, 13, 1, 'admin', 1, '2016-11-16 07:51:51', NULL),
(20, 14, 1, 'admin', 1, '2016-11-16 16:09:15', NULL),
(21, 2, 1, 'admin', 0, '2016-11-21 18:40:44', '2016-11-22 22:40:46'),
(22, 3, 1, 'admin', 0, '2016-11-21 18:40:45', '2016-11-22 22:41:05'),
(23, 4, 1, 'admin', 0, '2016-11-21 18:40:46', '2016-11-22 22:41:54'),
(24, 6, 1, 'admin', 0, '2016-11-21 18:40:47', '2016-11-22 22:41:20'),
(25, 7, 1, 'admin', 0, '2016-11-21 18:40:47', '2016-11-22 22:41:21'),
(26, 8, 1, 'admin', 0, '2016-11-21 18:40:48', '2016-11-22 22:41:32'),
(27, 15, 1, 'admin', 1, '2016-11-21 20:34:48', NULL),
(28, 17, 1, 'admin', 1, '2016-11-22 04:55:17', NULL),
(29, 18, 1, 'admin', 1, '2016-11-22 20:47:54', NULL),
(30, 3, 1, 'admin', 1, '2016-11-23 03:41:57', NULL),
(31, 19, 1, 'admin', 1, '2016-11-23 15:28:08', NULL),
(32, 1, 2, 'admin', 1, '2016-11-23 18:56:57', NULL),
(33, 1, 3, 'admin', 1, '2016-11-23 18:59:19', NULL),
(34, 1, 0, 'admin', 1, '2016-11-23 18:59:33', NULL),
(35, 1, 5, 'admin', 1, '2016-11-23 19:01:29', NULL),
(36, 2, 1, 'admin', 1, '2016-11-28 16:49:34', NULL),
(37, 4, 1, 'admin', 0, '2016-11-28 16:49:35', '2016-11-28 11:51:09'),
(38, 20, 1, 'admin', 1, '2016-11-28 16:53:58', NULL),
(39, 21, 1, 'admin', 0, '2016-12-01 08:21:18', '2016-12-01 13:20:39'),
(40, 5, 5, 'admin', 1, '2016-12-01 18:16:50', NULL),
(41, 9, 5, 'admin', 1, '2016-12-01 18:16:55', NULL),
(42, 19, 5, 'admin', 1, '2016-12-01 18:16:57', NULL),
(43, 21, 1, 'admin', 1, '2016-12-01 18:21:00', NULL),
(44, 22, 1, 'admin', 1, '2016-12-06 20:26:11', NULL),
(45, 23, 1, 'admin', 1, '2016-12-06 20:26:11', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productores`
--

CREATE TABLE IF NOT EXISTS `productores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `apellidos` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `dni` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `genero` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `codigo` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `telefonos` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `productores`
--

INSERT INTO `productores` (`id`, `nombres`, `apellidos`, `dni`, `genero`, `codigo`, `telefonos`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'JUAN MANUEL', 'ZAPATA RUIZ', '98392372', 'M', '0000000001', NULL, 1, '2016-11-16 08:30:00', NULL),
(4, 'DIANA', 'DOMINGUEZ SAAVEDRA', '98392373', 'M', '0000000002', NULL, 1, '2016-11-16 08:35:24', NULL),
(5, 'DANIEL ', 'ZUÑIGA RUIZ', '03049857', 'M', '0000000003', NULL, 1, '2016-11-16 16:08:20', NULL),
(6, 'DIANA', 'SAAVEDRA SARANGO', '09388499', 'F', '0000000004', NULL, 1, '2016-11-21 18:57:08', NULL),
(7, 'PEDRO', 'ZAPATA RUIZ', '90398477', 'M', '0000000005', NULL, 1, '2016-12-07 02:33:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productor_asociacion`
--

CREATE TABLE IF NOT EXISTS `productor_asociacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_productor` int(11) NOT NULL,
  `id_asociacion` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `productor_asociacion`
--

INSERT INTO `productor_asociacion` (`id`, `id_productor`, `id_asociacion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2016-11-21 19:46:45', NULL),
(2, 1, 2, 1, '2016-11-21 19:47:11', NULL),
(3, 6, 6, 1, '2016-11-21 19:47:43', NULL),
(4, 6, 8, 1, '2016-11-21 19:48:19', NULL),
(5, 5, 10, 1, '2016-11-23 03:44:01', NULL),
(6, 4, 13, 1, '2016-12-01 18:22:21', NULL),
(7, 7, 4, 1, '2016-12-07 02:33:36', NULL),
(8, 7, 5, 1, '2016-12-18 20:11:32', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productor_terreno`
--

CREATE TABLE IF NOT EXISTS `productor_terreno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_productor` int(11) NOT NULL,
  `id_asociacion` int(11) NOT NULL,
  `id_terreno` int(11) NOT NULL,
  `observacion` text COLLATE utf8_unicode_ci,
  `condicion` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PROPIETARIO',
  `documentacion` varchar(350) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_docs` text COLLATE utf8_unicode_ci,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `productor_terreno`
--

INSERT INTO `productor_terreno` (`id`, `id_productor`, `id_asociacion`, `id_terreno`, `observacion`, `condicion`, `documentacion`, `url_docs`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, '', 'PROPIETARIO', '', '', 1, '2016-11-21 19:46:45', NULL),
(2, 1, 2, 5, '', 'ARRENDATARIO', '', '', 1, '2016-11-21 19:47:11', NULL),
(3, 6, 6, 6, '', 'PROPIETARIO', '', '', 1, '2016-11-21 19:47:42', NULL),
(4, 6, 8, 7, '', 'PROPIETARIO', '', '', 1, '2016-11-21 19:48:19', NULL),
(5, 1, 1, 8, '', 'ARRENDATARIO', '', '', 1, '2016-11-21 19:49:41', NULL),
(6, 5, 10, 9, '', 'PROPIETARIO', '', '', 1, '2016-11-23 03:44:01', NULL),
(7, 4, 13, 10, '', 'PROPIETARIO', '', '', 1, '2016-12-01 18:22:21', NULL),
(8, 7, 4, 11, '', 'PROPIETARIO', '', '', 1, '2016-12-07 02:33:36', NULL),
(9, 7, 5, 12, '', 'PROPIETARIO', '', '', 1, '2016-12-18 20:11:32', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puertos`
--

CREATE TABLE IF NOT EXISTS `puertos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `puertos`
--

INSERT INTO `puertos` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'PUERTO A', 1, '2016-11-29 17:00:56', NULL),
(2, 'PUERTO B', 1, '2016-11-29 17:15:50', NULL),
(3, 'PUERTO C', 1, '2016-11-29 17:15:50', NULL),
(4, 'PUERTO D', 1, '2016-11-29 17:20:37', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_material`
--

CREATE TABLE IF NOT EXISTS `salida_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuadrilla` int(11) NOT NULL,
  `id_tipo_caja` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `salida_material`
--

INSERT INTO `salida_material` (`id`, `id_cuadrilla`, `id_tipo_caja`, `cantidad`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '120.00', 1, '2016-11-22 07:48:14', NULL),
(2, 1, 2, '10.00', 1, '2016-11-22 16:39:37', NULL),
(3, 1, 2, '10.00', 1, '2016-11-22 16:39:53', NULL),
(4, 2, 1, '125.00', 1, '2016-11-22 16:47:19', NULL),
(5, 15, 4, '100.00', 1, '2016-11-23 03:38:52', NULL),
(6, 17, 4, '100.00', 1, '2016-12-01 18:11:45', NULL),
(7, 6, 2, '100.00', 1, '2016-12-14 08:19:39', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_material_detalle`
--

CREATE TABLE IF NOT EXISTS `salida_material_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_salida_material` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

--
-- Volcado de datos para la tabla `salida_material_detalle`
--

INSERT INTO `salida_material_detalle` (`id`, `id_salida_material`, `id_material`, `cantidad`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '180.00', 1, '2016-11-22 07:48:15', NULL),
(2, 1, 5, '120.00', 1, '2016-11-22 07:48:15', NULL),
(3, 1, 2, '40.00', 1, '2016-11-22 07:48:16', NULL),
(4, 1, 3, '14.40', 1, '2016-11-22 07:48:16', NULL),
(5, 1, 4, '120.00', 1, '2016-11-22 07:48:16', NULL),
(6, 4, 1, '187.50', 1, '2016-11-22 16:47:19', NULL),
(7, 4, 5, '125.00', 1, '2016-11-22 16:47:19', NULL),
(8, 4, 2, '40.00', 1, '2016-11-22 16:47:19', NULL),
(9, 4, 3, '15.00', 1, '2016-11-22 16:47:19', NULL),
(10, 4, 4, '125.00', 1, '2016-11-22 16:47:19', NULL),
(11, 5, 1, '140.00', 1, '2016-11-23 03:38:52', NULL),
(12, 5, 5, '140.00', 1, '2016-11-23 03:38:53', NULL),
(13, 5, 6, '100.00', 1, '2016-11-23 03:38:53', NULL),
(14, 6, 1, '140.00', 1, '2016-12-01 18:11:46', NULL),
(15, 6, 5, '2.00', 1, '2016-12-01 18:11:46', NULL),
(16, 6, 3, '65.00', 1, '2016-12-01 18:11:46', NULL),
(17, 7, 7, '40.00', 1, '2016-12-14 08:19:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_config`
--

CREATE TABLE IF NOT EXISTS `sys_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nombre_comercial` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ruc` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8_unicode_ci,
  `lat_lng` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefonos` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `key_` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `f_licence` datetime DEFAULT NULL,
  `py_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `sys_config`
--

INSERT INTO `sys_config` (`id`, `empresa`, `nombre_comercial`, `ruc`, `direccion`, `lat_lng`, `telefonos`, `email`, `website`, `key_`, `f_licence`, `py_name`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'CENTRAL PIURANA DE ASOCIACIONES DE PEQUEñOS PRODUCTORES DE BANANO ORGANICO', 'CEPIBO', '20525288871', 'Avenida jose de lama #1605 urbanizacion, Sta Rosa, Sullana', '-4.9028605,-80.7006845', '490087', NULL, NULL, NULL, '2100-12-30 23:59:59', 'py_cepibo', 1, '2016-11-14 22:34:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_logs`
--

CREATE TABLE IF NOT EXISTS `sys_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sql_` text COLLATE utf8_unicode_ci NOT NULL,
  `sql_success` tinyint(1) NOT NULL,
  `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `host` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `browser` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `f_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=346 ;

--
-- Volcado de datos para la tabla `sys_logs`
--

INSERT INTO `sys_logs` (`id`, `usuario`, `sql_`, `sql_success`, `ip`, `host`, `browser`, `f_registro`) VALUES
(1, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''17''', 1, '::1', 'Laptop-HP', '', '2016-11-15 04:53:40'),
(2, 'admin', 'CALL sp_registrar_trabajador(''admin'', ''maría luisa'', ''perez patiño'', ''11111111'', ''piura'', '''', ''908789878'', ''mperez@mail.com'', ''F'', ''10'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 03:22:06'),
(3, 'admin', 'CALL sp_registrar_trabajador(''admin'', ''maría luisa'', ''perez patiño'', ''11111111'', ''piura'', '''', ''908789878'', ''mperez@mail.com'', ''F'', ''10'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 03:59:15'),
(4, 'admin', 'CALL sp_registrar_trabajador(''admin'', ''maría luisa'', ''perez patiño'', ''11111111'', ''piura'', '''', ''908789878'', ''mperez@mail.com'', ''F'', ''10'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 04:00:26'),
(6, 'admin', 'CALL sp_registrar_trabajador(''admin'', ''juana luisa'', ''perez patiño'', ''22222222'', ''piura'', '''', ''908789878'', ''jperez@mail.com'', ''F'', ''9'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 04:05:15'),
(9, 'admin', 'CALL sp_registrar_trabajador(''admin'', ''luis alberto'', ''ruiz saavedra'', ''33333333'', '''', '''', ''938849992'', ''lruiz@mail.com'', ''M'', ''1'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 04:50:10'),
(10, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''12'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-16 07:48:43'),
(11, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''13'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-16 07:51:51'),
(12, 'admin', 'CALL sp_registrar_productor(''admin'', ''juan manuel'', ''zapata ruiz'', ''98392372'', ''M'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 08:22:49'),
(13, 'admin', 'CALL sp_registrar_productor(''admin'', ''juan manuel'', ''zapata ruiz'', ''98392372'', ''M'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 08:30:01'),
(15, 'admin', 'CALL sp_registrar_productor(''admin'', ''diana'', ''dominguez saavedra'', ''98392373'', ''M'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 08:34:10'),
(16, 'admin', 'CALL sp_registrar_productor(''admin'', ''diana'', ''dominguez saavedra'', ''98392373'', ''M'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 08:34:22'),
(17, 'admin', 'CALL sp_registrar_productor(''admin'', ''diana'', ''dominguez saavedra'', ''98392373'', ''M'', ''1'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 08:35:24'),
(18, 'admin', 'CALL sp_registrar_productor(''admin'', ''daniel '', ''zuñiga ruiz'', ''03049857'', ''M'', ''2'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:08:20'),
(19, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''14'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:09:15'),
(20, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''12''', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:09:24'),
(21, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''3''', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:09:26'),
(22, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:09:31'),
(23, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''6''', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:09:33'),
(24, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''8''', 1, '::1', 'Laptop-HP', '', '2016-11-16 16:09:37'),
(25, 'admin', 'CALL sp_registrar_terreno(''admin'', ''5'', ''1.3'', ''1.3'', ''0.0'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:22:32'),
(26, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''2.3'', ''2.3'', ''0.0'', ''sullana'', '''', ''ARRENDATARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:24:56'),
(27, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''1'', ''1'', '''', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:28:07'),
(28, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''1'', ''1'', ''0.0'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:32:07'),
(29, 'admin', 'CALL sp_registrar_terreno(''admin'', ''5'', ''2'', ''2'', ''0.0'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:48:47'),
(30, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''1'', ''1'', ''0.0'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:50:26'),
(31, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''1'', ''1'', ''0.0'', '''', '''', ''ARRENDATARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:51:16'),
(32, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''1'', ''1'', ''0.0'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 17:52:33'),
(33, 'admin', 'CALL sp_registrar_terreno(''admin'', ''1'', ''1'', ''1'', ''0.0'', ''sullana'', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-16 18:12:12'),
(34, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''13''', 1, '::1', 'Laptop-HP', '', '2016-11-16 20:47:07'),
(35, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''2'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:40:44'),
(36, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''3'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:40:45'),
(37, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''4'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:40:46'),
(38, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''6'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:40:47'),
(39, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''7'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:40:47'),
(40, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''8'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:40:48'),
(41, 'admin', 'CALL sp_registrar_productor(''admin'', ''diana'', ''saavedra sarango'', ''09388499'', ''F'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:57:08'),
(42, 'admin', 'CALL sp_registrar_terreno(''admin'', ''6'', ''3'', ''2'', ''2'', ''0.0'', ''sullana'', '''', ''ARRENDATARIO'', ''carta poder'', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:58:19'),
(43, 'admin', 'CALL sp_registrar_terreno(''admin'', ''6'', ''10'', ''1.5'', ''1.5'', '''', ''sullana'', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 18:58:55'),
(44, 'admin', 'CALL sp_registrar_terreno(''admin'', ''1'', ''1'', ''1'', ''1'', ''0.0'', ''sullana'', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 19:46:45'),
(45, 'admin', 'CALL sp_registrar_terreno(''admin'', ''1'', ''2'', ''2'', ''2'', '''', ''sullana'', '''', ''ARRENDATARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 19:47:11'),
(46, 'admin', 'CALL sp_registrar_terreno(''admin'', ''6'', ''6'', ''1.3'', ''1.3'', '''', ''sullana'', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 19:47:43'),
(47, 'admin', 'CALL sp_registrar_terreno(''admin'', ''6'', ''8'', ''2'', ''2'', '''', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 19:48:19'),
(48, 'admin', 'CALL sp_registrar_terreno(''admin'', ''1'', ''1'', ''1.5'', ''1.5'', ''0.0'', '''', '''', ''ARRENDATARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-21 19:49:41'),
(49, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''15'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-21 20:34:48'),
(50, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''c0001'', ''ejemplo'', '''', ''40'', ''UND'', '''', ''1'', ''MATERIAL'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-22 00:03:17'),
(51, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''c0002'', ''ejemplo 2'', '''', ''0'', ''LTS'', '''', ''0'', ''INSUMO'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-22 00:04:32'),
(52, 'admin', 'UPDATE materiales SET stock = stock + ''0'' WHERE id = ''''', 1, '::1', 'Laptop-HP', '', '2016-11-22 02:55:53'),
(53, 'admin', 'UPDATE materiales SET stock = stock + 0 WHERE id = ''''', 1, '::1', 'Laptop-HP', '', '2016-11-22 02:58:32'),
(54, 'admin', 'UPDATE materiales SET stock = stock + 80 WHERE id = ''1''', 1, '::1', 'Laptop-HP', '', '2016-11-22 03:01:19'),
(55, 'admin', 'UPDATE materiales SET stock = stock + 10 WHERE id = ''1''', 1, '::1', 'Laptop-HP', '', '2016-11-22 03:02:21'),
(56, 'admin', 'INSERT INTO ingreso_material (`id_material`, `cantidad`, `origen`, `proveedor`, `observacion`) VALUES (''2'', ''40'', '''', ''proveedor X'', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:45:46'),
(57, 'admin', 'UPDATE materiales SET stock = stock + 40 WHERE id = ''2''', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:45:46'),
(58, 'admin', 'INSERT INTO ingreso_material (`id_material`, `cantidad`, `origen`, `proveedor`, `observacion`) VALUES (''2'', ''10'', '''', ''proveedor y'', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:47:32'),
(59, 'admin', 'UPDATE materiales SET stock = stock + 10 WHERE id = ''2''', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:47:33'),
(60, 'admin', 'INSERT INTO ingreso_material (`id_material`, `cantidad`, `origen`, `proveedor`, `observacion`) VALUES (''2'', ''10'', '''', ''proveedor x'', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:50:42'),
(61, 'admin', 'UPDATE materiales SET stock = stock + 10 WHERE id = ''2''', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:50:42'),
(62, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''17'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 04:55:17'),
(63, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''c'', ''ejemplo 3'', '''', ''0'', ''LTS'', '''', ''0'', ''INSUMO'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-22 06:04:30'),
(64, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''c'', ''ejemplo 4'', '''', ''40'', ''MLL'', '''', ''1'', ''INSUMO'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-22 06:05:59'),
(65, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''c'', ''ejemplo 5'', '''', ''120'', ''PAQUETE'', '''', ''1'', ''MATERIAL'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-22 06:06:49'),
(66, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''c'', ''ejemplo 6'', '''', ''200'', ''UND'', '''', ''1'', ''MATERIAL'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-22 06:07:18'),
(67, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''1'', ''1'', ''120'', ''2016-11-22 02:48:14'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:14'),
(68, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''1'', ''1'', ''180'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:15'),
(69, 'admin', 'UPDATE materiales SET stock = stock - 180 WHERE id = ''1''', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:15'),
(70, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''1'', ''5'', ''120'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:15'),
(71, 'admin', 'UPDATE materiales SET stock = stock - 120 WHERE id = ''5''', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:15'),
(72, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''1'', ''2'', ''40.00'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:16'),
(73, 'admin', 'UPDATE materiales SET stock = stock - 40 WHERE id = ''2''', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:16'),
(74, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''1'', ''3'', ''14.399999999999999'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:16'),
(75, 'admin', 'UPDATE materiales SET stock = stock - 14.4 WHERE id = ''3''', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:16'),
(76, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''1'', ''4'', ''120'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:16'),
(77, 'admin', 'UPDATE materiales SET stock = stock - 120 WHERE id = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-22 07:48:16'),
(78, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''1'', ''2'', ''10'', ''2016-11-22 11:39:37'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:39:38'),
(79, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''1'', ''2'', ''10'', ''2016-11-22 11:39:53'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:39:53'),
(80, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''2'', ''1'', ''125'', ''2016-11-22 11:47:19'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(81, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''4'', ''1'', ''187.5'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(82, 'admin', 'UPDATE materiales SET stock = stock - 187.5 WHERE id = ''1''', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(83, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''4'', ''5'', ''125'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(84, 'admin', 'UPDATE materiales SET stock = stock - 125 WHERE id = ''5''', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(85, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''4'', ''2'', ''40.00'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(86, 'admin', 'UPDATE materiales SET stock = stock - 40 WHERE id = ''2''', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(87, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''4'', ''3'', ''15'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(88, 'admin', 'UPDATE materiales SET stock = stock - 15 WHERE id = ''3''', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(89, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''4'', ''4'', ''125'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:19'),
(90, 'admin', 'UPDATE materiales SET stock = stock - 125 WHERE id = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-22 16:47:20'),
(91, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''18'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 20:47:54'),
(92, 'admin', 'UPDATE material_tipo_caja SET activo = ''0'' WHERE id = ''''', 1, '::1', 'Laptop-HP', '', '2016-11-22 21:58:57'),
(93, 'admin', 'UPDATE material_tipo_caja SET activo = ''0'' WHERE id = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-22 22:01:26'),
(94, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''4'', ''1'', ''1.4'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 23:53:23'),
(95, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''4'', ''5'', ''0.01'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 23:53:28'),
(96, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''1'', ''4'', ''2'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 23:55:56'),
(97, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''1'', ''6'', ''40'', ''0'')', 1, '::1', 'Laptop-HP', '', '2016-11-22 23:56:24'),
(98, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''4'', ''6'', ''1'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:37:57'),
(99, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''15'', ''4'', ''100'', ''2016-11-22 22:38:52'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:52'),
(100, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''5'', ''1'', ''140'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:52'),
(101, 'admin', 'UPDATE materiales SET stock = stock - 140 WHERE id = ''1''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:53'),
(102, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''5'', ''5'', ''140'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:53'),
(103, 'admin', 'UPDATE materiales SET stock = stock - 140 WHERE id = ''5''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:53'),
(104, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''5'', ''6'', ''100'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:53'),
(105, 'admin', 'UPDATE materiales SET stock = stock - 100 WHERE id = ''6''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:38:54'),
(106, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''21''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:40:46'),
(107, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''22''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:41:05'),
(108, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''24''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:41:20'),
(109, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''25''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:41:21'),
(110, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''26''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:41:32'),
(111, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''23''', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:41:54'),
(112, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''3'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:41:57'),
(113, 'admin', 'CALL sp_registrar_terreno(''admin'', ''5'', ''10'', ''3'', ''3'', ''0.0'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-11-23 03:44:01'),
(114, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''19'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 15:28:08'),
(115, 'admin', 'INSERT INTO cuadrillas (`id_asociacion`, `nombre`) VALUES (''4'', ''CUADRILLA 1'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 16:02:35'),
(116, 'admin', 'INSERT INTO cuadrillas (`id_asociacion`, `nombre`) VALUES (''4'', ''CUADRILLA 2'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 16:02:46'),
(117, 'admin', 'INSERT INTO usuarios (`usuario_reg`, `id_trabajador`, `username`, `password`, `id_tipousuario`) VALUES (''admin'', ''2'', ''mperez '', ''a188cf500051cfbbf56c099388e8448e'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 18:47:47'),
(118, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''1'', ''2'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 18:56:58'),
(119, 'admin', 'INSERT INTO usuarios (`usuario_reg`, `id_trabajador`, `username`, `password`, `id_tipousuario`) VALUES (''admin'', ''3'', ''jperez '', ''601711ca32a910adf3349f6c112c5cbb'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 18:57:50'),
(120, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''1'', ''3'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 18:59:19'),
(121, 'admin', 'INSERT INTO usuarios (`usuario_reg`, `id_trabajador`, `username`, `password`, `id_tipousuario`) VALUES (''admin'', ''5'', ''lruiz '', ''232bccad711ef3cc75b645ac3160d85c'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 18:59:33'),
(122, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''1'', ''Array'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 18:59:33'),
(123, 'admin', 'INSERT INTO usuarios (`usuario_reg`, `id_trabajador`, `username`, `password`, `id_tipousuario`) VALUES (''admin'', ''5'', ''lruiz '', ''232bccad711ef3cc75b645ac3160d85c'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 19:01:28'),
(124, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''1'', ''5'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-23 19:01:29'),
(125, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''2'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-28 16:49:34'),
(126, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''4'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-28 16:49:35'),
(127, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''37''', 1, '::1', 'Laptop-HP', '', '2016-11-28 16:51:09'),
(128, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''20'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-11-28 16:53:58'),
(129, 'admin', 'INSERT INTO vapor (`nombre`) VALUES (''VAPOR X'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:37:25'),
(130, 'admin', 'INSERT INTO vapor (`nombre`) VALUES (''VALOR Y'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:42:08'),
(131, 'admin', 'INSERT INTO vapor (`nombre`) VALUES (''VALOR Z'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:43:17'),
(132, 'admin', 'INSERT INTO clientes (`nombre`) VALUES (''CLIENTE X'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:46:24'),
(133, 'admin', 'INSERT INTO vapor (`nombre`) VALUES (''VAPOR A'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:46:49'),
(134, 'admin', 'INSERT INTO contenedor (`numero`, `descripcion`, `marca`, `modelo`, `payload`, `largo`, `ancho`, `altura`, `certificacion`) VALUES (''CONTENEDOR A'', '''', '''', '''', ''0'', '''', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:47:27'),
(135, 'admin', 'INSERT INTO tipo_funda (`nombre`) VALUES (''TIPO A'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 03:53:18'),
(136, 'admin', 'INSERT INTO clientes (`nombre`) VALUES (''CLIENTE C'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 04:20:28'),
(137, 'admin', 'INSERT INTO tipo_funda (`nombre`) VALUES (''FUNDA 1'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 04:57:35'),
(138, 'admin', 'INSERT INTO contenedor (`numero`, `descripcion`, `marca`, `modelo`, `payload`, `largo`, `ancho`, `altura`, `certificacion`) VALUES (''734892-A'', '''', '''', '''', ''0'', '''', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 16:35:45'),
(139, 'admin', 'INSERT INTO puertos (`nombre`) VALUES (''PUERTO D'')', 1, '::1', 'Laptop-HP', '', '2016-11-29 17:20:37'),
(140, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`) VALUES (''admin'', ''2'', ''2'', ''2'', ''2'', ''3'', ''4'', ''93849'', ''9875'', ''48'', ''2016-11-29 12:23:00'', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 19:10:11'),
(141, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`) VALUES (''admin'', ''5'', ''3'', ''2'', ''3'', ''3'', ''4'', ''9878'', ''734'', ''47'', ''2016-11-23 14:20:00'', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 19:21:02'),
(142, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`) VALUES (''admin'', ''3'', ''2'', ''1'', ''2'', ''4'', ''3'', ''9879'', ''638'', ''48'', ''2016-11-29 14:22:00'', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 19:23:01'),
(143, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`) VALUES (''admin'', ''5'', ''3'', ''2'', ''3'', ''3'', ''2'', ''90823'', ''30984'', ''46'', ''2016-11-16 14:24:00'', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 19:25:54'),
(144, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`) VALUES (''admin'', ''2'', ''2'', ''1'', ''1'', ''4'', ''1'', ''j98'', ''09390j'', ''48'', ''2016-11-29 14:27:00'', ''2016-11-29 14:27:00'', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-11-29 19:28:28'),
(145, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''4'', ''3'', ''65'', ''0'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 03:29:25'),
(146, 'admin', 'UPDATE material_tipo_caja SET activo = ''0'' WHERE id = ''10''', 1, '::1', 'Laptop-HP', '', '2016-11-30 03:30:12'),
(147, 'admin', 'INSERT INTO clientes (`nombre`) VALUES (''CLIENTE D'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 06:04:13'),
(148, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''3'', ''6'', ''2'', ''1'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 03:05:50'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:05:56'),
(149, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES ('''', ''1'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:06:02'),
(150, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''1'', ''6'', ''4'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 03:15:02'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:15:03'),
(151, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES ('''', ''1'', ''12'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:15:03'),
(152, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES ('''', ''2'', ''10'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:15:03'),
(153, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES ('''', ''3'', ''4'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:15:03'),
(154, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''4'', ''6'', ''4'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 03:24:52'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:52'),
(155, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''3'', ''1'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:52'),
(156, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''3'', ''2'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:53'),
(157, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''3'', ''3'', ''4'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:53'),
(158, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''3'', ''4'', ''19'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:53'),
(159, 'admin', 'UPDATE packing_list SET nro_cajas = ''26'' WHERE id_packing = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:53'),
(160, 'admin', 'UPDATE packing SET estado = ''1'' WHERE id = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-30 08:24:53'),
(161, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''4'', ''6'', ''6'', ''1'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 04:14:11'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:14:12'),
(162, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''4'', ''6'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:14:12'),
(163, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''4'', ''9'', ''19'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:14:12'),
(164, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''4'', ''12'', ''7'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:14:12'),
(165, 'admin', 'UPDATE packing_list SET nro_cajas = ''28'' WHERE id_packing = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:14:12'),
(166, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''4'', ''6'', ''5'', ''1'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 04:27:33'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:27:33'),
(167, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''5'', ''1'', ''10'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:27:33'),
(168, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''5'', ''2'', ''12'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:27:33'),
(169, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''5'', ''7'', ''9'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:27:33'),
(170, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''5'', ''8'', ''9'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:27:34'),
(171, 'admin', 'UPDATE packing_list SET nro_cajas = ''40'' WHERE id_packing = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:27:34'),
(172, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''4'', ''6'', ''5'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 04:28:16'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:28:16'),
(173, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`) VALUES (''6'', ''1'', ''12'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:28:16'),
(174, 'admin', 'UPDATE packing_list SET nro_cajas = ''12'' WHERE id_packing = ''4''', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:28:17'),
(175, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''4'', ''6'', ''5'', ''1'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 04:33:08'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:33:08'),
(176, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''7'', ''2'', ''2'', ''16-11-30 04:33:08'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:33:08'),
(177, 'admin', 'UPDATE packing_list SET nro_cajas = ''2'' WHERE id_packing = ''4''  AND created_at = ''16-11-30 04:33:08''', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:33:08'),
(178, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''4'', ''3'', ''4'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 04:52:44'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:52:44'),
(179, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''8'', ''1'', ''1'', ''16-11-30 04:52:44'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:52:44'),
(180, 'admin', 'UPDATE packing_list SET nro_cajas = ''1'' WHERE id_packing = ''4''  AND created_at = ''16-11-30 04:52:44''', 1, '::1', 'Laptop-HP', '', '2016-11-30 09:52:44'),
(181, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''2'', ''3'', ''6'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 05:11:35'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 10:11:35'),
(182, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''9'', ''1'', ''10'', ''16-11-30 05:11:35'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 10:11:35'),
(183, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''9'', ''2'', ''12'', ''16-11-30 05:11:35'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 10:11:35'),
(184, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''9'', ''3'', ''1'', ''16-11-30 05:11:35'')', 1, '::1', 'Laptop-HP', '', '2016-11-30 10:11:35'),
(185, 'admin', 'UPDATE packing_list SET nro_cajas = ''23'' WHERE id_packing = ''2''  AND created_at = ''16-11-30 05:11:35''', 1, '::1', 'Laptop-HP', '', '2016-11-30 10:11:36'),
(186, 'admin', 'UPDATE packing SET f_llegada_contenedor = ''16/11/2016 02:24 PM'', f_salida_contenedor = ''17/11/2016 06:03 AM'' , f_inicio_llenado = ''16/11/2016 02:25 PM'' , f_fin_llenado = ''17/11/2016 06:00 AM'' , estado = ''1'' , updated_at = now() WHERE id = ''4'' ', 1, '::1', 'Laptop-HP', '', '2016-11-30 23:15:26'),
(187, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''2'', ''3'', ''1'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 19:50:26'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:50:26'),
(188, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''10'', ''1'', ''1'', ''16-11-30 19:50:26'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:50:26'),
(189, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''10'', ''6'', ''7'', ''16-11-30 19:50:26'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:50:26'),
(190, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''10'', ''10'', ''19'', ''16-11-30 19:50:26'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:50:26'),
(191, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''10'', ''14'', ''9'', ''16-11-30 19:50:26'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:50:27'),
(192, 'admin', 'UPDATE packing_list SET nro_cajas = ''36'' WHERE id_packing = ''2''  AND created_at = ''16-11-30 19:50:26''', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:50:27'),
(193, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''2'', ''3'', ''1'', ''1'', ''2016-11-30 00:00:00'', ''0'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(194, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''1'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(195, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''2'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(196, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''3'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(197, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''4'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(198, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''5'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(199, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''6'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:08'),
(200, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''7'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(201, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''8'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(202, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''9'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(203, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''10'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(204, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''11'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(205, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''12'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(206, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''13'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(207, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''14'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(208, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''15'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(209, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''16'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(210, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''17'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:09'),
(211, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''18'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:10'),
(212, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''19'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:10'),
(213, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''11'', ''20'', ''1'', ''16-11-30 19:51:07'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:10'),
(214, 'admin', 'UPDATE packing_list SET nro_cajas = ''20'' WHERE id_packing = ''2''  AND created_at = ''16-11-30 19:51:07''', 1, '::1', 'Laptop-HP', '', '2016-12-01 00:51:10'),
(215, 'admin', 'UPDATE packing SET f_llegada_contenedor = ''2016-11-17 20:52:00'', f_salida_contenedor = ''2016-11-18 20:52:00'' , f_inicio_llenado = ''2016-11-18 8:55:00'' , f_fin_llenado = ''2016-11-18 20:00:00'' , estado = ''1'' , updated_at = now() WHERE id = ''4'' ', 1, '::1', 'Laptop-HP', '', '2016-12-01 01:53:55'),
(216, 'admin', 'INSERT INTO vapor (`nombre`) VALUES (''VAPOR C'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 02:05:47'),
(217, 'admin', 'INSERT INTO contenedor (`numero`, `descripcion`, `marca`, `modelo`, `payload`, `largo`, `ancho`, `altura`, `certificacion`) VALUES (''CN-09326'', '''', '''', '''', ''0'', '''', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-12-01 02:06:02'),
(218, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`, `codigo`) VALUES (''admin'', ''6'', ''1'', ''3'', ''1'', ''2'', ''1'', ''t00928'', ''g-0092'', ''48'', ''2016-11-29 21:05:00'', '''', '''', '''', ''PK0000000006'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 02:06:32'),
(219, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''3'', ''3'', ''3'', ''2'', ''2016-12-01 00:00:00'', ''0'', ''16-12-01 03:06:35'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:06:35'),
(220, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''12'', ''1'', ''010'', ''16-12-01 03:06:35'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:06:36'),
(221, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''12'', ''2'', ''2'', ''16-12-01 03:06:35'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:06:36'),
(222, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''12'', ''3'', ''1'', ''16-12-01 03:06:35'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:06:36'),
(223, 'admin', 'UPDATE packing_list SET nro_cajas = ''13'' WHERE id_packing = ''3''  AND created_at = ''16-12-01 03:06:35''', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:06:37'),
(224, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''3'', ''3'', ''3'', ''2'', ''2016-12-01 00:00:00'', ''0'', ''16-12-01 03:07:02'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:07:02'),
(225, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''13'', ''5'', ''1'', ''16-12-01 03:07:02'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:07:02'),
(226, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''13'', ''6'', ''1'', ''16-12-01 03:07:02'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:07:02'),
(227, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''13'', ''7'', ''3'', ''16-12-01 03:07:02'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:07:02'),
(228, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''13'', ''8'', ''45'', ''16-12-01 03:07:02'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:07:02'),
(229, 'admin', 'UPDATE packing_list SET nro_cajas = ''50'' WHERE id_packing = ''3''  AND created_at = ''16-12-01 03:07:02''', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:07:03'),
(230, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''21'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:21:18'),
(231, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''AMP 1'', ''2016-12-01 03:49:29'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:49:29'),
(232, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP 3'', ''2016-12-01 03:50:48'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:50:48'),
(233, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP 4'', ''2016-12-01 03:53:23'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:53:23'),
(234, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''1'', ''5'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 08:53:23'),
(235, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''17'', ''4'', ''100'', ''2016-12-01 13:11:45'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:45'),
(236, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''6'', ''1'', ''140'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:46'),
(237, 'admin', 'UPDATE materiales SET stock = stock - 140 WHERE id = ''1''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:46'),
(238, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''6'', ''5'', ''2'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:46'),
(239, 'admin', 'UPDATE materiales SET stock = stock - 2 WHERE id = ''5''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:46'),
(240, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''6'', ''3'', ''65.00'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:46'),
(241, 'admin', 'UPDATE materiales SET stock = stock - 65 WHERE id = ''3''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:11:46'),
(242, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''3'', ''2'', ''40'', ''0'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:13:17'),
(243, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''3'', ''6'', ''1'', ''1'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:13:35'),
(244, 'admin', 'UPDATE usuarios SET activo = ''0'', updated_at = now() WHERE id = ''3''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:15:54'),
(245, 'admin', 'UPDATE usuarios SET activo = ''1'', updated_at = now() WHERE id = ''3''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:15:58'),
(246, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''5'', ''5'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:16:50'),
(247, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''9'', ''5'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:16:55'),
(248, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''19'', ''5'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:16:57'),
(249, 'admin', 'UPDATE permisos_menu SET activo = ''0'', updated_at = now() WHERE id = ''39''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:20:39'),
(250, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''21'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:21:00'),
(251, 'admin', 'CALL sp_registrar_terreno(''admin'', ''4'', ''13'', ''2'', ''2'', ''0.0'', ''sullana'', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:22:21'),
(252, 'admin', 'INSERT INTO vapor (`nombre`) VALUES (''VAPOR NUEVO'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:23:23'),
(253, 'admin', 'INSERT INTO clientes (`nombre`) VALUES (''CLIENTE NUEVO'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:23:39'),
(254, 'admin', 'INSERT INTO contenedor (`numero`, `descripcion`, `marca`, `modelo`, `payload`, `largo`, `ancho`, `altura`, `certificacion`) VALUES (''CONT-0001'', '''', '''', '''', ''0'', '''', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:24:31'),
(255, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`, `codigo`) VALUES (''admin'', ''7'', ''5'', ''4'', ''1'', ''3'', ''4'', ''90349'', ''8394'', ''48'', ''2016-12-01 8:23:00'', '''', '''', '''', ''PK0000000007'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:26:05'),
(256, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''7'', ''1'', ''1'', ''5'', ''2016-11-30 00:00:00'', ''0'', ''16-12-01 13:31:00'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:31:00'),
(257, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''14'', ''1'', ''10'', ''16-12-01 13:31:00'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:31:00'),
(258, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''14'', ''5'', ''5'', ''16-12-01 13:31:00'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:31:00'),
(259, 'admin', 'UPDATE packing_list SET nro_cajas = ''15'' WHERE id_packing = ''7''  AND created_at = ''16-12-01 13:31:00''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:31:00');
INSERT INTO `sys_logs` (`id`, `usuario`, `sql_`, `sql_success`, `ip`, `host`, `browser`, `f_registro`) VALUES
(260, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''7'', ''3'', ''1'', ''2'', ''2016-11-30 00:00:00'', ''0'', ''16-12-01 13:32:01'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:32:01'),
(261, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''15'', ''9'', ''20'', ''16-12-01 13:32:01'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:32:01'),
(262, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''15'', ''10'', ''10'', ''16-12-01 13:32:01'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:32:01'),
(263, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''15'', ''11'', ''1'', ''16-12-01 13:32:01'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:32:01'),
(264, 'admin', 'UPDATE packing_list SET nro_cajas = ''31'' WHERE id_packing = ''7''  AND created_at = ''16-12-01 13:32:01''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:32:01'),
(265, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''6'', ''1'', ''1'', ''5'', ''2016-12-01 00:00:00'', ''0'', ''16-12-01 13:40:33'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:40:33'),
(266, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''16'', ''2'', ''10'', ''16-12-01 13:40:33'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:40:33'),
(267, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''16'', ''3'', ''10'', ''16-12-01 13:40:33'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:40:33'),
(268, 'admin', 'UPDATE packing_list SET nro_cajas = ''20'' WHERE id_packing = ''6''  AND created_at = ''16-12-01 13:40:33''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:40:33'),
(269, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`, `codigo`) VALUES (''admin'', ''4'', ''5'', ''2'', ''2'', ''3'', ''2'', ''9023874'', ''83945'', ''48'', ''2016-12-01 13:44:00'', '''', '''', '''', ''PK0000000008'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:46:35'),
(270, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''8'', ''3'', ''3'', ''1'', ''2016-12-01 00:00:00'', ''0'', ''2016-12-01 13:48:18'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:48:19'),
(271, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''17'', ''2'', ''10'', ''2016-12-01 13:48:18'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:48:19'),
(272, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''17'', ''3'', ''1'', ''2016-12-01 13:48:18'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:48:19'),
(273, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''17'', ''4'', ''1'', ''2016-12-01 13:48:18'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:48:19'),
(274, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''17'', ''5'', ''3'', ''2016-12-01 13:48:18'')', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:48:19'),
(275, 'admin', 'UPDATE packing_list SET nro_cajas = ''15'' WHERE id_packing = ''8''  AND created_at = ''2016-12-01 13:48:18''', 1, '::1', 'Laptop-HP', '', '2016-12-01 18:48:19'),
(276, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''22'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 20:26:11'),
(277, 'admin', 'INSERT INTO permisos_menu (`id_menu`, `id_usuario`, `usuario`) VALUES (''23'', ''1'', ''admin'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 20:26:11'),
(278, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''5'', ''1'', ''3'', ''5'', ''2016-12-06 00:00:00'', ''0'', ''2016-12-06 16:29:27'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 21:29:28'),
(279, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''18'', ''1'', ''1'', ''2016-12-06 16:29:27'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 21:29:28'),
(280, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''18'', ''2'', ''3'', ''2016-12-06 16:29:27'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 21:29:28'),
(281, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''18'', ''3'', ''3'', ''2016-12-06 16:29:27'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 21:29:28'),
(282, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''18'', ''6'', ''2'', ''2016-12-06 16:29:27'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 21:29:28'),
(283, 'admin', 'UPDATE packing_list SET nro_cajas = ''9'' WHERE id_packing = ''5''  AND created_at = ''2016-12-06 16:29:27''', 1, '::1', 'Laptop-HP', '', '2016-12-06 21:29:28'),
(284, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''5'', ''1'', ''3'', ''3'', ''2016-12-06 00:00:00'', ''0'', ''2016-12-06 17:05:30'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 22:05:30'),
(285, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''19'', ''1'', ''10'', ''2016-12-06 17:05:30'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 22:05:30'),
(286, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''19'', ''2'', ''1'', ''2016-12-06 17:05:30'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 22:05:30'),
(287, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''19'', ''3'', ''2'', ''2016-12-06 17:05:30'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 22:05:30'),
(288, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''19'', ''6'', ''3'', ''2016-12-06 17:05:30'')', 1, '::1', 'Laptop-HP', '', '2016-12-06 22:05:30'),
(289, 'admin', 'UPDATE packing_list SET nro_cajas = ''16'' WHERE id_packing = ''5''  AND created_at = ''2016-12-06 17:05:30''', 1, '::1', 'Laptop-HP', '', '2016-12-06 22:05:31'),
(290, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''5'', ''3'', ''4'', ''1'', ''2016-12-06 00:00:00'', ''0'', ''2016-12-06 19:30:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:30:59'),
(291, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''20'', ''1'', ''24'', ''2016-12-06 19:30:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:31:00'),
(292, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''20'', ''2'', ''10'', ''2016-12-06 19:30:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:31:00'),
(293, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''20'', ''3'', ''10'', ''2016-12-06 19:30:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:31:01'),
(294, 'admin', 'UPDATE packing_list SET nro_cajas = ''44'' WHERE id_packing = ''5''  AND created_at = ''2016-12-06 19:30:58''', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:31:01'),
(295, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMPACADORA EJM1'', ''2016-12-06 19:32:14'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:14'),
(296, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''10'', ''6'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:14'),
(297, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''5'', ''6'', ''3'', ''4'', ''2016-12-06 00:00:00'', ''0'', ''2016-12-06 19:32:43'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:43'),
(298, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''21'', ''7'', ''10'', ''2016-12-06 19:32:43'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:43'),
(299, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''21'', ''8'', ''10'', ''2016-12-06 19:32:43'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:44'),
(300, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''21'', ''9'', ''11'', ''2016-12-06 19:32:43'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:44'),
(301, 'admin', 'UPDATE packing_list SET nro_cajas = ''31'' WHERE id_packing = ''5''  AND created_at = ''2016-12-06 19:32:43''', 1, '::1', 'Laptop-HP', '', '2016-12-07 00:32:44'),
(302, 'admin', 'INSERT INTO contenedor (`numero`, `descripcion`, `marca`, `modelo`, `payload`, `largo`, `ancho`, `altura`, `certificacion`) VALUES (''982374'', '''', '''', '''', ''0'', '''', '''', '''', '''')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:27:04'),
(303, 'admin', 'INSERT INTO clientes (`nombre`) VALUES (''CLIENTE NUEVO X'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:27:23'),
(304, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`, `codigo`) VALUES (''admin'', ''1'', ''6'', ''5'', ''3'', ''1'', ''2'', ''92732'', ''38902'', ''49'', ''2016-12-05 21:26:00'', '''', '''', '''', ''PK0000000009'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:27:47'),
(305, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''9'', ''1'', ''1'', ''3'', ''2016-12-05 00:00:00'', ''0'', ''2016-12-06 21:28:28'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:28'),
(306, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''22'', ''1'', ''10'', ''2016-12-06 21:28:28'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:28'),
(307, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''22'', ''2'', ''11'', ''2016-12-06 21:28:28'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:29'),
(308, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''22'', ''3'', ''12'', ''2016-12-06 21:28:28'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:29'),
(309, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''22'', ''9'', ''21'', ''2016-12-06 21:28:28'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:29'),
(310, 'admin', 'UPDATE packing_list SET nro_cajas = ''54'' WHERE id_packing = ''9''  AND created_at = ''2016-12-06 21:28:28''', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:29'),
(311, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP X'', ''2016-12-06 21:28:59'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:28:59'),
(312, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''2'', ''7'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:00'),
(313, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP X'', ''2016-12-06 21:29:09'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:09'),
(314, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''3'', ''8'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:09'),
(315, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP X'', ''2016-12-06 21:29:15'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:15'),
(316, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''3'', ''9'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:15'),
(317, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''9'', ''2'', ''1'', ''5'', ''2016-12-05 00:00:00'', ''0'', ''2016-12-06 21:29:32'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:32'),
(318, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''23'', ''7'', ''10'', ''2016-12-06 21:29:32'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:32'),
(319, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''23'', ''8'', ''10'', ''2016-12-06 21:29:32'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:32'),
(320, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''23'', ''9'', ''10'', ''2016-12-06 21:29:32'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:32'),
(321, 'admin', 'UPDATE packing_list SET nro_cajas = ''30'' WHERE id_packing = ''9''  AND created_at = ''2016-12-06 21:29:32''', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:33'),
(322, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP X'', ''2016-12-06 21:29:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:58'),
(323, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''3'', ''10'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:29:58'),
(324, 'admin', 'INSERT INTO empacadoras (`nombre`, `created_at`) VALUES (''EMP X'', ''2016-12-06 21:32:36'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:32:36'),
(325, 'admin', 'INSERT INTO asociacion_empacadora (`id_asociacion`, `id_empacadora`) VALUES (''4'', ''11'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:32:36'),
(326, 'admin', 'CALL sp_registrar_productor(''admin'', ''pedro'', ''zapata ruiz'', ''90398477'', ''M'', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:14'),
(327, 'admin', 'CALL sp_registrar_terreno(''admin'', ''7'', ''4'', ''1.3'', ''1.3'', ''0.0'', ''sullana'', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:36'),
(328, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''9'', ''8'', ''1'', ''9'', ''2016-12-05 00:00:00'', ''0'', ''2016-12-06 21:33:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:58'),
(329, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''24'', ''6'', ''10'', ''2016-12-06 21:33:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:59'),
(330, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''24'', ''7'', ''10'', ''2016-12-06 21:33:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:59'),
(331, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''24'', ''8'', ''10'', ''2016-12-06 21:33:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:59'),
(332, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''24'', ''9'', ''5'', ''2016-12-06 21:33:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:59'),
(333, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''24'', ''10'', ''5'', ''2016-12-06 21:33:58'')', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:33:59'),
(334, 'admin', 'UPDATE packing_list SET nro_cajas = ''40'' WHERE id_packing = ''9''  AND created_at = ''2016-12-06 21:33:58''', 1, '::1', 'Laptop-HP', '', '2016-12-07 02:34:00'),
(335, 'admin', 'CALL sp_registrar_material(''admin'', ''1'', ''C0007'', ''Agua'', '''', ''0'', ''LTS'', ''0'', ''0'', ''INSUMO'', @salida); SELECT @salida;', 1, '::1', 'DESKTOP-VA6651N', '', '2016-12-14 08:18:45'),
(336, 'admin', 'INSERT INTO material_tipo_caja (`id_tipo_caja`, `id_material`, `multiplo`, `calcular`) VALUES (''2'', ''7'', ''40'', ''0'')', 1, '::1', 'DESKTOP-VA6651N', '', '2016-12-14 08:19:05'),
(337, 'admin', 'INSERT INTO salida_material (`id_cuadrilla`, `id_tipo_caja`, `cantidad`, `created_at`) VALUES (''6'', ''2'', ''100'', ''2016-12-14 03:19:39'')', 1, '::1', 'DESKTOP-VA6651N', '', '2016-12-14 08:19:39'),
(338, 'admin', 'INSERT INTO salida_material_detalle (`id_salida_material`, `id_material`, `cantidad`) VALUES (''7'', ''7'', ''40.00'')', 1, '::1', 'DESKTOP-VA6651N', '', '2016-12-14 08:19:40'),
(339, 'admin', 'UPDATE materiales SET stock = stock - 40 WHERE id = ''7''', 1, '::1', 'DESKTOP-VA6651N', '', '2016-12-14 08:19:41'),
(340, 'admin', 'INSERT INTO packing (`usuario_reg`, `id_vapor`, `id_cliente`, `id_contenedor`, `id_tipo_funda`, `id_puerto_origen`, `id_puerto_destino`, `nro_termoregistro`, `nro_guia`, `nro_semana`, `f_llegada_contenedor`, `f_salida_contenedor`, `f_inicio_llenado`, `f_fin_llenado`, `codigo`) VALUES (''admin'', ''2'', ''4'', ''2'', ''1'', ''4'', ''3'', ''30'', ''300'', ''50'', ''2016-12-13 15:06:00'', ''2016-12-15 15:06:00'', ''2016-12-14 15:06:00'', ''2016-12-15 15:05:00'', ''PK0000000010'')', 1, '190.236.25.255', '190.236.25.255', '', '2016-12-18 20:08:30'),
(341, 'admin', 'INSERT INTO packing_list (`id_packing`, `id_productor_terreno`, `id_tipo_caja`, `id_asociacion_empacadora`, `f_corte`, `nro_cajas`, `created_at`) VALUES (''3'', ''5'', ''4'', ''3'', ''2016-12-18 00:00:00'', ''0'', ''2016-12-18 15:10:04'')', 1, '190.236.25.255', '190.236.25.255', '', '2016-12-18 20:10:04'),
(342, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''25'', ''3'', ''05'', ''2016-12-18 15:10:04'')', 1, '190.236.25.255', '190.236.25.255', '', '2016-12-18 20:10:04'),
(343, 'admin', 'INSERT INTO packing_list_detalle (`id_packing_list`, `nro_pallet`, `cantidad`, `created_at`) VALUES (''25'', ''5'', ''09'', ''2016-12-18 15:10:04'')', 1, '190.236.25.255', '190.236.25.255', '', '2016-12-18 20:10:04'),
(344, 'admin', 'UPDATE packing_list SET nro_cajas = ''14'' WHERE id_packing = ''3''  AND created_at = ''2016-12-18 15:10:04''', 1, '190.236.25.255', '190.236.25.255', '', '2016-12-18 20:10:04'),
(345, 'admin', 'CALL sp_registrar_terreno(''admin'', ''7'', ''5'', ''2'', ''2'', ''2'', '''', '''', ''PROPIETARIO'', '''', '''', @salida); SELECT @salida;', 1, '190.236.25.255', '190.236.25.255', '', '2016-12-18 20:11:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terrenos`
--

CREATE TABLE IF NOT EXISTS `terrenos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area_total` decimal(10,2) NOT NULL,
  `area_cultivo` decimal(10,2) NOT NULL,
  `area_desarrollo` decimal(10,2) NOT NULL,
  `unidad_medida` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Ha',
  `referencia` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `certificacion` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `terrenos`
--

INSERT INTO `terrenos` (`id`, `codigo`, `descripcion`, `area_total`, `area_cultivo`, `area_desarrollo`, `unidad_medida`, `referencia`, `certificacion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'T0000000001', NULL, '1.00', '1.00', '0.00', 'Ha', 'SULLANA', '', 1, '2016-11-16 18:12:11', NULL),
(2, 'T0000000002', NULL, '2.00', '2.00', '0.00', 'Ha', 'SULLANA', '', 1, '2016-11-21 18:58:19', NULL),
(3, 'T0000000003', NULL, '1.50', '1.50', '0.00', 'Ha', 'SULLANA', '', 1, '2016-11-21 18:58:55', NULL),
(4, 'T0000000004', NULL, '1.00', '1.00', '0.00', 'Ha', 'SULLANA', '', 1, '2016-11-21 19:46:45', NULL),
(5, 'T0000000005', NULL, '2.00', '2.00', '0.00', 'Ha', 'SULLANA', '', 1, '2016-11-21 19:47:11', NULL),
(6, 'T0000000006', NULL, '1.30', '1.30', '0.00', 'Ha', 'SULLANA', '', 1, '2016-11-21 19:47:42', NULL),
(7, 'T0000000007', NULL, '2.00', '2.00', '0.00', 'Ha', '', '', 1, '2016-11-21 19:48:19', NULL),
(8, 'T0000000008', NULL, '1.50', '1.50', '0.00', 'Ha', '', '', 1, '2016-11-21 19:49:41', NULL),
(9, 'T0000000009', NULL, '3.00', '3.00', '0.00', 'Ha', '', '', 1, '2016-11-23 03:44:01', NULL),
(10, 'T0000000010', NULL, '2.00', '2.00', '0.00', 'Ha', 'SULLANA', '', 1, '2016-12-01 18:22:21', NULL),
(11, 'T0000000011', NULL, '1.30', '1.30', '0.00', 'Ha', 'SULLANA', '', 1, '2016-12-07 02:33:35', NULL),
(12, 'T0000000012', NULL, '2.00', '2.00', '2.00', 'Ha', '', '', 1, '2016-12-18 20:11:32', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE IF NOT EXISTS `tipousuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `tipousuario`
--

INSERT INTO `tipousuario` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'LEVEL 1', 1, '2016-11-14 21:29:18', NULL),
(2, 'LEVEL 2', 1, '2016-11-14 21:29:18', NULL),
(3, 'LEVEL 3', 1, '2016-11-15 02:50:05', NULL),
(4, 'LEVEL 4', 1, '2016-11-15 02:50:05', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_caja`
--

CREATE TABLE IF NOT EXISTS `tipo_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `tipo_caja`
--

INSERT INTO `tipo_caja` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'CQB', 1, '2016-11-22 05:13:21', NULL),
(2, 'EQUAL', 1, '2016-11-22 05:13:21', NULL),
(3, 'FAIRNANDO', 1, '2016-11-22 05:13:21', NULL),
(4, 'BELGA', 1, '2016-11-22 05:13:21', NULL),
(5, 'BIO TROPIC', 1, '2016-11-22 05:13:21', NULL),
(6, 'SUIBANA', 1, '2016-11-22 05:13:21', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_funda`
--

CREATE TABLE IF NOT EXISTS `tipo_funda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `tipo_funda`
--

INSERT INTO `tipo_funda` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'POLITUBO', 1, '2016-11-28 18:48:57', NULL),
(2, 'TIPO A', 1, '2016-11-29 03:53:17', NULL),
(3, 'FUNDA 1', 1, '2016-11-29 04:57:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

CREATE TABLE IF NOT EXISTS `trabajador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dni` char(8) COLLATE utf8_unicode_ci NOT NULL,
  `nombres` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `apellidos` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8_unicode_ci,
  `genero` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `f_nacimiento` date DEFAULT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `celular` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `img_url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usuario_reg` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni_UNIQUE` (`dni`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`id`, `dni`, `nombres`, `apellidos`, `direccion`, `genero`, `f_nacimiento`, `email`, `celular`, `telefono`, `img_url`, `usuario_reg`, `estado`, `created_at`, `updated_at`) VALUES
(1, '00000000', 'Admin', 'Admin', NULL, 'M', NULL, 'hcumbicusr@gmail.com', '956727976', NULL, NULL, 'admin', 1, '2016-11-14 22:02:36', NULL),
(2, '11111111', 'MARÍA LUISA', 'PEREZ PATIÑO', 'PIURA', 'F', NULL, 'mperez@mail.com', '908789878', '', NULL, 'admin', 1, '2016-11-16 04:00:26', NULL),
(3, '22222222', 'JUANA LUISA', 'PEREZ PATIÑO', 'PIURA', 'F', NULL, 'jperez@mail.com', '908789878', '', NULL, 'admin', 1, '2016-11-16 04:05:14', NULL),
(5, '33333333', 'LUIS ALBERTO', 'RUIZ SAAVEDRA', '', 'M', NULL, 'lruiz@mail.com', '938849992', '', NULL, 'admin', 1, '2016-11-16 04:50:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador_asociacion`
--

CREATE TABLE IF NOT EXISTS `trabajador_asociacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(11) NOT NULL,
  `id_asociacion` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `trabajador_asociacion`
--

INSERT INTO `trabajador_asociacion` (`id`, `id_trabajador`, `id_asociacion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, '2016-11-16 04:50:10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador_cargo`
--

CREATE TABLE IF NOT EXISTS `trabajador_cargo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `trabajador_cargo`
--

INSERT INTO `trabajador_cargo` (`id`, `id_trabajador`, `id_cargo`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, '2016-11-15 03:07:18', NULL),
(2, 5, 1, 1, '2016-11-16 04:50:10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE IF NOT EXISTS `unidad_medida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `unidad` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unidad` (`unidad`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id`, `nombre`, `unidad`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'UNIDAD', 'UND', 1, '2016-11-21 23:23:08', NULL),
(2, 'KILOGRAMO', 'KG', 1, '2016-11-21 23:23:08', NULL),
(3, 'LITRO', 'LTS', 1, '2016-11-21 23:23:08', NULL),
(4, 'MILLAR', 'MLL', 1, '2016-11-21 23:23:08', NULL),
(5, 'ROLLO', 'ROLLO', 1, '2016-11-21 23:23:08', NULL),
(6, 'PAQUETE', 'PAQUETE', 1, '2016-11-21 23:23:08', NULL),
(7, 'GRAMO', 'Gr', 1, '2016-11-21 23:23:08', NULL),
(8, 'METROS', 'Mtr', 1, '2016-11-21 23:23:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `id_trabajador` int(11) NOT NULL,
  `id_tipousuario` int(11) NOT NULL,
  `usuario_reg` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `activo`, `id_trabajador`, `id_tipousuario`, `usuario_reg`, `created_at`, `updated_at`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1, 1, 'admin', '2016-11-14 21:29:58', '2016-11-14 23:25:04'),
(2, 'mperez ', 'a188cf500051cfbbf56c099388e8448e', 1, 2, 2, 'admin', '2016-11-23 18:47:47', NULL),
(3, 'jperez ', '601711ca32a910adf3349f6c112c5cbb', 1, 3, 2, 'admin', '2016-11-23 18:57:49', '2016-12-01 13:15:58'),
(5, 'lruiz ', '232bccad711ef3cc75b645ac3160d85c', 1, 5, 2, 'admin', '2016-11-23 19:01:28', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vapor`
--

CREATE TABLE IF NOT EXISTS `vapor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `vapor`
--

INSERT INTO `vapor` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'SEALAND BALBOA V.1618', 1, '2016-11-28 18:50:28', NULL),
(2, 'VAPOR X', 1, '2016-11-29 03:37:25', NULL),
(3, 'VALOR Y', 1, '2016-11-29 03:42:08', NULL),
(4, 'VALOR Z', 1, '2016-11-29 03:43:17', NULL),
(5, 'VAPOR A', 1, '2016-11-29 03:46:49', NULL),
(6, 'VAPOR C', 1, '2016-12-01 02:05:47', NULL),
(7, 'VAPOR NUEVO', 1, '2016-12-01 18:23:23', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
