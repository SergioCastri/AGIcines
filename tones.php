#!/usr/bin/php -q
<?php
header('Content-type: text/plain; charset=utf-8');//nuevo
set_time_limit(30);
$param_error_log = '/tmp/notas.log';
$param_debug_on = 1;
require('phpagi.php');
require("definiciones.inc");
error_reporting(E_ALL);//nuevo
$agi = new AGI();
$agi->answer();
$agi->exec("AGI","googletts.agi,\"Bienvenido al servicio telefonico de la plataforma Cine U \",es");
$agi->exec("AGI","googletts.agi,\"mediante esta plataforma podra consultar la cartelera y reservar boletas \",es");

$usuario = verificarUsuario($agi);
menuPrincipal($agi);

function menuPrincipal($agi){
  do {
    $agi->exec("AGI","googletts.agi,\"Menu Principal \",es");
    $agi->exec("AGI","googletts.agi,\"Marque uno para realizar una reserva. \",es");
    $agi->exec("AGI","googletts.agi,\"dos para cancelar una reserva. \",es");
	$agi->exec("AGI","googletts.agi,\"tres para calificar el servicio. \",es");
	$agi->exec("AGI","googletts.agi,\"cuatro para escuchar sus reservas. \",es");
	$agi->exec("AGI","googletts.agi,\"cero para terminar la llamada. \",es");
     $opcion = $agi->get_data('beep', 3000, 1);
     $seleccion = $opcion['result'];
     switch( $seleccion ){
         case 0:
		$agi->exec("AGI","googletts.agi,\"Gracias por utilizar el sitema de audiorespuesta, hasta pronto \",es");
             $agi->hangup();
             break;
         case 1:
             consultarBoletas($agi);
             break;
         case 2:
             cancelarReserva($agi);
             break;
         case 3:
             calificarReserva($agi);
             break;
         case 4:
             escucharReservas($agi);
             break;
         default:
		$agi->exec("AGI","googletts.agi,\"Opcion incorrecta \",es");
     }
 } while ($seleccion != 0);
}


function verificarUsuario($agi){
    $agi->exec("AGI","googletts.agi,\"Ingrese su codigo de usuario despues del bip \",es");
    $opcion = $agi->get_data('beep', 10000, 1);
    $idUsuario = $opcion['result'];
    $conexion = get_db_connection();
    $result = mysql_query("SELECT nombre FROM Usuario where codigo ='$idUsuario'", $conexion);

    if ( mysql_num_rows($result) > 0 ){
        while( $row = mysql_fetch_array($result) ){
	    $agi->exec("AGI","googletts.agi,\"Bienvenido.$row[0] \",es");
            $GLOBALS["usuario"] = $idUsuario;
            return $idUsuario;
        }
        mysql_close($conexion);
    } else {
	$agi->exec("AGI","googletts.agi,\"El codigo que ha ingresado es incorrecto \",es");
        $agi->hangup();
    }
}

function get_db_connection() {
    if ( !($link = mysql_connect(MAQUINA, USUARIO, CLAVE)) ){
        echo "Error conectando a la base de datos.";
        exit();
    }

    if ( !mysql_select_db(DB, $link) ){
        echo "Error seleccionando la base de datos.";
        exit();
    }

    return $link;
}

function consultarBoletas($agi){
	$agi->exec("AGI","googletts.agi,\"Las peliculas disponibles son las siguientes \",es");
    $conexion = get_db_connection();
    $result = mysql_query("SELECT id, nombre FROM Pelicula", $conexion);
    if ( mysql_num_rows($result) > 0 ){
        while( $row = mysql_fetch_array($result) ){
		$agi->exec("AGI","googletts.agi,\"$row[0] \",es");
		$agi->exec("AGI","googletts.agi,\"$row[1] \",es");
        }
        mysql_close($conexion);
    }
    return pedidoBoleta($agi);
}

function pedidoBoleta($agi){
	$agi->exec("AGI","googletts.agi,\"Marque el codigo de la pelicula luego del bip \",es");
    $opcion = $agi->get_data('beep', 10000, 1);
    $pelicula = $opcion['result'];
    $GLOBALS["pelicula"] = $pelicula;

	$agi->exec("AGI","googletts.agi,\"Los tipos de funcion son las siguientes \",es");
    $conexion = get_db_connection();
    $result = mysql_query("SELECT id, tipo_funcion, precio FROM Tipo_Boleta", $conexion);
    if ( mysql_num_rows($result) > 0 ){
        while( $row = mysql_fetch_array($result) ){
		$agi->exec("AGI","googletts.agi,\"$row[0] \",es");
		$agi->exec("AGI","googletts.agi,\"$row[1] \",es");
		$agi->exec("AGI","googletts.agi,\"$row[2] \",es");
        }
    }
    $agi->exec("AGI","googletts.agi,\"Marque el codigo del tipo de Boleta luego del bip \",es");
    $opcion = $agi->get_data('beep', 10000, 1);
    $tipo_funcion = $opcion['result'];

    $conexion = get_db_connection();
    $result = mysql_query("SELECT precio FROM Tipo_Boleta WHERE id='$tipo_funcion'", $conexion);
    $precio = 0;
    if ( mysql_num_rows($result) > 0 ){
        while( $row = mysql_fetch_array($result) ){
            $precio = $row[0];
        }
    }

    $idUsuario = $GLOBALS["usuario"];
    $idPelicula = $GLOBALS["pelicula"];
    date_default_timezone_set("America/Bogota");
    $fecha = date("Y-m-d H:i:s");
    $conexion = get_db_connection();
    $insert = "INSERT INTO Reserva (total, fecha_reserva, usuario, pelicula) VALUES ('$precio','$fecha', $idUsuario, $idPelicula);";
    if (!mysql_query($insert, $conexion)){
      die('Error: ' . mysql_error());
    }else{
      mysql_close($conexion);
    }

	$agi->exec("AGI","googletts.agi,\"El precio total de su reserva es \",es");
	$agi->exec("AGI","googletts.agi,\"$precio \",es");
}

function cancelarReserva($agi){
  do {
	$agi->exec("AGI","googletts.agi,\" menu de cancelacion de reservas \",es");
	$agi->exec("AGI","googletts.agi,\" marque uno si conoce el codigo de la reserva \",es");
	$agi->exec("AGI","googletts.agi,\" dos para escuchar sus reservas \",es");
	$agi->exec("AGI","googletts.agi,\" cero para regresar al menu principal \",es");
    $opcion = $agi->get_data('beep', 3000, 1);
    $seleccion = $opcion['result'];
    switch( $seleccion ){
        case 0:
            menuPrincipal($agi);
        case 1:
            eliminarReserva($agi);
            break;
        case 2:
            escucharReservas($agi);
            eliminarReserva($agi);
            break;
        default:
		$agi->exec("AGI","googletts.agi,\" opcion incorrecta \",es");
    }
  } while ($seleccion != 0);
}

function eliminarReserva($agi){
	$agi->exec("AGI","googletts.agi,\" Marque el codigo de la reserva a eliminar \",es");
  $opcion = $agi->get_data('beep', 10000, 1);
  $idReserva = $opcion['result'];
  $conexion = get_db_connection();
  $result= mysql_query("DELETE FROM Reserva where nro_reserva='$idReserva'", $conexion);
  if($result == 1){
      mysql_close($conexion);
	$agi->exec("AGI","googletts.agi,\" La reserva con codigo '$idReserva' fue eliminada satisfactoriamente \",es");
  }else{
	$agi->exec("AGI","googletts.agi,\" La reserva no existe \",es");
  }
}

function escucharReservas($agi){
	$agi->exec("AGI","googletts.agi,\"Sus reservas son las siguientes \",es");
  $usuario = $GLOBALS["usuario"];
  $conexion = get_db_connection();
  $result = mysql_query("SELECT nro_reserva, total, pelicula FROM Reserva WHERE usuario='$usuario'", $conexion);
  if ( mysql_num_rows($result) > 0 ){
      while( $row = mysql_fetch_array($result) ){
		$agi->exec("AGI","googletts.agi,\" el codigo de la reserva es \",es");
		$agi->exec("AGI","googletts.agi,\"$row[0]\",es");
		$agi->exec("AGI","googletts.agi,\" el valor de la reserva es \",es");
		$agi->exec("AGI","googletts.agi,\" $row[1]\",es");
          $idPelicula = $row[2];
          $query = mysql_query("SELECT nombre FROM Pelicula where id='$idPelicula'", $conexion);
          if ( mysql_num_rows($query) > 0 ){
              while( $filas = mysql_fetch_array($query) ){
			$agi->exec("AGI","googletts.agi,\" El nombre de la pelicula es \",es");
			$agi->exec("AGI","googletts.agi,\"$filas[0] \",es");
              }
          }
      }
      mysql_close($conexion);
  }else{
	$agi->exec("AGI","googletts.agi,\" Usted no tiene reservas \",es");
  }
}

function calificar($agi){
	$agi->exec("AGI","googletts.agi,\" Por favor ingrese el numero de la reserva despues del bip \",es");
  $op = $agi->get_data('beep', 10000, 1);
  $reserva = $op['result'];

  $usuario = $GLOBALS["usuario"];
  $conexion = get_db_connection();
  $result = mysql_query("SELECT * FROM Reserva WHERE nro_reserva ='$reserva' AND usuario='$usuario'", $conexion);
  if(mysql_num_rows($result) > 0){
      do{
		$agi->exec("AGI","googletts.agi,\" Por favor, ingrese la calificacion de 1 a 5 que quiere darle al servicio \",es");
          $opcion = $agi->get_data('beep', 10000, 1);
          $calificacion = $opcion['result'];
          if ($calificacion == 0 | $calificacion > 5)
		$agi->exec("AGI","googletts.agi,\" Por favor ingrese un valor correcto \",es");
      }while($calificacion > 5);

      $result = mysql_query("UPDATE Reserva SET puntuacion_servicio = '$calificacion' WHERE nro_reserva = '$reserva'", $conexion);
      if($result == 1){
          mysql_close($conexion);
		$agi->exec("AGI","googletts.agi,\" Gracias por calificar nuestros servicios \",es");
      }
  } else {
	$agi->exec("AGI","googletts.agi,\" La reserva no existe o no fue usted quien la hizo \",es");
  }
}

function calificarReserva($agi){
  do {
	$agi->exec("AGI","googletts.agi,\" menu de calificacion de reservas  \",es");
	$agi->exec("AGI","googletts.agi,\" marque uno si conoce el codigo de la reserva \",es");
	$agi->exec("AGI","googletts.agi,\" dos para escuchar sus reservas \",es");
	$agi->exec("AGI","googletts.agi,\" cero para regresar al menu principal \",es");
    $opcion = $agi->get_data('beep', 3000, 1);
    $seleccion = $opcion['result'];
    switch( $seleccion ){
        case 0:
            menuPrincipal($agi);
        case 1:
            calificar($agi);
            break;
        case 2:
            escucharReservas($agi);
            calificar($agi);
            break;
        default:
		$agi->exec("AGI","googletts.agi,\" opcion incorrecta \",es");
    }
  } while ($seleccion != 0);

}

$agi->exec("AGI","googletts.agi,\" Gracias por utilizar el sitema de audiorespuesta, hasta pronto \", es");
$agi->hangup();
