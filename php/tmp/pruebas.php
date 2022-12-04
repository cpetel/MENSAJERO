<?php

$respuesta = "";

require_once("../classes.php");

session_start();

date_default_timezone_set("America/Argentina/Cordoba");



$b = (isset($_SESSION['BARRIOS'])) ? $b = $_SESSION['BARRIOS'] : "";
$hora = (isset($_SESSION['hora'])) ? $_SESSION['hora'] : "";
$minutos = (isset($_SESSION['minutos'])) ? $_SESSION['minutos'] : "";
$motivo = (isset($_SESSION['motivo'])) ? $_SESSION['motivo'] : "";
$mindex = (isset($_SESSION['mindex'])) ? $_SESSION['mindex'] : 0;
$haymotivo = (isset($_SESSION['haymotivo'])) ? $_SESSION['haymotivo'] : false;
//$saludoinicial = (isset($_SESSION['saludoinicial'])) ? $_SESSION['saludoinicial']: false;
//$saludofinal = (isset($_SESSION['saludofinal'])) ? $_SESSION['saludofinal']: false;
$hayhorario = (isset($_SESSION['hayhorario'])) ? $_SESSION['hayhorario'] : false;


$modo_produccion = (strpos($_SERVER["SCRIPT_FILENAME"], "pruebas") === false);

require_once("../barrios.php");



function logeo($que)
{

	$fichero = '../log/log_mensajero.log';
	// La nueva persona a añdir al fichero
	$cuando_y_que = date("d/m/y H:i:s") . " - " . $que . chr(13) . chr(10);
	// Escribir los contenidos en el fichero,
	// usando la bandera FILE_APPEND para añadir el contenido al final del fichero
	// y la bandera LOCK_EX para evitar que cualquiera escriba en el fichero al mismo tiempo
	file_put_contents($fichero, $cuando_y_que, FILE_APPEND | LOCK_EX);
}



function creaWAV($a)
{

	$salida = "../tmp/salida" . session_id() . ".wav";
	$i = 0;
	$d = 0;

	foreach ($a as $w) {
		++$i;

		$f = fopen("sonidos/" . $w . ".wav", "r");
		fseek($f, 17);

		$u = "1234";
		$nh = 17 + 4;
		while (($u <> "data") && ($nh < 100)) {
			$u = substr($u . fgetc($f), -4);
			$nh++;
		}

		$ld = ord(fgetc($f)) + 256 * (ord(fgetc($f)) + 256 * (ord(fgetc($f)) + 256 * (ord(fgetc($f)))));
		fclose($f);
		$d = $d + $ld;


		if ($i == 1) {
			//primero
			if (file_exists($salida)) unlink($salida);
			copy("sonidos/" . $w . ".wav", $salida);
		} else {
			//otros
			$c = file_get_contents("sonidos/" . $w . ".wav", NULL, NULL, $nh);
			file_put_contents($salida, $c, FILE_APPEND);
		}
	}

	$f = fopen($salida, "r+");

	fseek($f, 17);
	$u = "    ";
	$nh = 17 + 4;
	while ($u <> "data") {
		$u = substr($u . fgetc($f), 1, 4);
		$nh++;
	}

	$lt = $d + $nh - 8;

	fseek($f, 4);
	$c = $lt % 256;
	$lt = ($lt - $c) / 256;
	fwrite($f, chr($c));
	$c = $lt % 256;
	$lt = ($lt - $c) / 256;
	fwrite($f, chr($c));
	$c = $lt % 256;
	$lt = ($lt - $c) / 256;
	fwrite($f, chr($c));
	fwrite($f, chr($lt));

	$lt = $d;
	fseek($f, $nh - 4);
	$c = $lt % 256;
	$lt = ($lt - $c) / 256;
	fwrite($f, chr($c));
	$c = $lt % 256;
	$lt = ($lt - $c) / 256;
	fwrite($f, chr($c));
	$c = $lt % 256;
	$lt = ($lt - $c) / 256;
	fwrite($f, chr($c));
	fwrite($f, chr($lt));

	fclose($f);
}
?>