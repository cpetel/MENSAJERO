<?php


$respuesta = "";

require_once("classes.php");

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

require_once("barrios.php");

function codifica($x)
{
	return bin2hex($x);
}

function agregaRespuesta($tipo, $a)
{
	global $respuesta;
	$respuesta .= "<" . $tipo . ">" . $a . "</" . $tipo . ">";
}
function estado()
{
	if (strpos(shell_exec(stripslashes("asterisk -nrx \"database get DAYNIGHT C1\"")), "DAY") === false) {
		return "EMERGENCIA";
	}
	return "NORMAL";
}

function agregaAccion($a)
{
	//	agregaRespuesta("accion",htmlspecialchars($a, ENT_QUOTES,"ISO-8859-15",false));
	agregaRespuesta("accion", codifica($a));
}

function agregaObjeto($tipo, $a)
{
	$r = "<id>" . $tipo . "</id>";
	//	$r .= "<html>" .htmlspecialchars($a, ENT_QUOTES,"ISO-8859-15",false);
	$r .= "<html>" . codifica($a);
	$r .= "</html>";
	agregaRespuesta("objeto", $r);
}

function tocoBarrio($cual)
{
	global $b;

	if (array_key_exists($cual, $b)) {
		// existe $cual
		$b[$cual]->togleestado();
		mandaBarrio($cual);
	}
}


function tocohora($cual)
{
	global $hora;
	global $hayhorario;

	$hora = $cual;
	$hayhorario = true;
	agregaAccion("document.getElementById('hayhorario').checked='checked';");
}

function tocoMinutos($cual)
{
	global $minutos;
	global $hayhorario;

	$minutos = $cual;
	$hayhorario = true;
	agregaAccion("document.getElementById('hayhorario').checked='checked';");
}

function tocoOpcion($cual)
{
	global $hayhorario;
	global $haymotivo;
	//	global $saludoinicial;
	//	global $saludofinal;

	switch ($cual) {
		case 'haymotivo':
			$haymotivo = !$haymotivo;
			break;

			/*		case 'saludoinicial':
			$saludoinicial = !$saludoinicial;
		break;
		
		case 'saludofinal':
			$saludofinal = !$saludofinal;
		break;
*/
		case 'hayhorario':
			$hayhorario = !$hayhorario;
			break;

		default:
			break;
	}
}

function eliminaArchivos()
{
	$sale = `/bin/sh ./sacacorte.sh`;
	logeo($sale);
}
function copiaArchivos()
{
	$sale = `/bin/sh ./msjcorte.sh`;
	logeo($sale);
}


function eliminaArchivosPrueba()
{
	$sale = `/bin/sh ./sacacorteprueba.sh`;
	logeo($sale);
}
function copiaArchivosPrueba()
{
	$sale = `/bin/sh ./msjcorteprueba.sh`;
	//$sale = `whoami>salida.txt`;
	logeo($sale);
}

function tocoBoton($cual)
{

	global $hora, $minutos, $modo_produccion;

	switch ($cual) {
		case 'escuchar':
			$salida = "'php/tmp/salida" . session_id() . ".wav'";
			if ($modo_produccion) {
				logeo("Escuchando audio=" . $salida);
			} else {

				logeo("SIMULANDO-> Escuchando audio=" . $salida);
			}

			agregaAccion('document.getElementById("sonido").innerHTML= "<embed src=' . $salida . "hidden='true' type='audio/wav' autostart='true'></embed>" . '";');
			break;

		case 'enviar':
			// arma archivo de recuerdo de llamada caduca
			$wt = mktime($hora, $minutos, 0) - (3 * 60); // tres minutos antes
			if ($wt < time()) {
				// proximo día
				$wt = $wt + (24 * 60 * 60);
			}

			logeo("copiando archivo recordatorio para las " . $wt . "(" . date('jS F Y h:i:s A (T)', $wt) . ")");
			copy("llama.call", "llamaahora.call");
			touch("llamaahora.call", $wt, $wt);
			rename("llamaahora.call", "/var/spool/asterisk/outgoing/llamaahora.call");

			// debe haber un enlace al archivo MSG-Generico-E.wav en la carpeta sonidos
			// llamado msg-emergencia.wav

			$salida = "tmp/salida" . session_id() . ".wav";
			$logaudio = "log/log"  . date("d-m-Y H:i:s") . "." . session_id() . ".wav";
			// agregado para el servidor de CPE
			// genera archivos
			$file = fopen("tmp/mensaje.txt", "w");

			fwrite($file, mensaje_web() . PHP_EOL);

			fclose($file);

			$file = fopen("tmp/mapa.svg", "w");

			fwrite($file, arma_mapa() . PHP_EOL);

			fclose($file);

			logeo("Archivos generados para página web...");


			// si esta en modo pruebas evita modificar DAYNIGHT
			if ($modo_produccion) {
				copy($salida, "sonidos/msg-emergencia.wav");
				copy($salida, $logaudio);
				// debe haber un enlace al archivo anuncioIVRcorte en la carpeta sonidos
				// llamado anuncio.wav
				copy($salida, "sonidos/anuncio.wav");

				// entra en emergencia
				$p = popen(stripslashes("asterisk -nrx \"database put DAYNIGHT C1 NIGHT\""), "r");
				pclose($p);
				logeo("Modo emergencia. audio=" . $logaudio);

				// copia archivos
				copiaArchivos();

				// hasta acá


			} else {

				logeo("SIMULANDO-> Entrando en modo emergencia. audio=" . $logaudio);
				// copia archivos
				copiaArchivosPrueba();

				// hasta acá
			}

			agregaAccion('alert("no olvide verificar que el mensaje ha sido tomado llamando a la cabecera...");');
			break;

		case 'sacaemergencia':

			//borra el aviso de mensaje caduco
			unlink("/var/spool/asterisk/outgoing/llamaahora.call");

			// debe haber un enlace al archivo MSG-Generico-E.wav en la carpeta sonidos
			// llamado msg-emergencia.wav

			if ($modo_produccion) {
				copy("sonidos/MSG-Generico-E-original.wav", "sonidos/msg-emergencia.wav");

				// sale de emergencia
				$p = popen(stripslashes("asterisk -nrx \"database put DAYNIGHT C1 DAY\""), "r");
				pclose($p);

				// debe haber un enlace al archivo anuncioIVRcorte en la carpeta sonidos
				// llamado anuncio.wav
				copy("sonidos/nada.wav", "sonidos/anuncio.wav");
				// elimina archivos
				eliminaArchivos();

				// hasta acá

				logeo("Saliendo del modo emergencia.");
			} else {
				// elimina archivos
				eliminaArchivosPrueba();

				// hasta acá


				logeo("SIMULANDO-> Saliendo del modo emergencia.");
			}


			agregaAccion('alert("no olvide verificar que el mensaje ha sido removido llamando a la cabecera...");');
			break;

		default:
			break;
	}
	//	agregaAccion('alert("tocaste el boton: ' .$cual .'");');

}

function tocoMotivo($cual, $mi)
{
	global $motivo;
	global $haymotivo;
	global $mindex;

	$motivo = urldecode($cual);
	$mindex = $mi;
	$haymotivo = true;

	agregaAccion("document.getElementById('haymotivo').checked='checked';");
}

function mandaBarrio($cual)
{
	global $b;

	if (array_key_exists($cual, $b)) {

		if ($b[$cual]->getestado()) {
			// barrio=veraddero	
			agregaAccion("document.getElementById('" . $cual . "').checked='cheked';");
			agregaAccion("document.getElementById('svg_" . $cual . "').setAttribute('opacity','0.75');");
			//			agregaAccion("document.getElementById('svg_" .$cual ."').setAttribute('stroke-dasharray','');");
			agregaAccion("document.getElementById('svg_" . $cual . "').setAttribute('stroke-width','2');");
			agregaAccion("document.getElementById('svg_" . $cual . "').setAttribute('stroke','#000000');");
		} else {
			// barrio=falso
			agregaAccion("document.getElementById('" . $cual . "').checked='';");
			agregaAccion("document.getElementById('svg_" . $cual . "').setAttribute('opacity','0.25');");
			//			agregaAccion("document.getElementById('svg_" .$cual ."').setAttribute('stroke-dasharray','');");
			agregaAccion("document.getElementById('svg_" . $cual . "').setAttribute('stroke-width','1');");
			agregaAccion("document.getElementById('svg_" . $cual . "').setAttribute('stroke','#b2b2b2');");
		}
	}
}

function logeo($que)
{

	$fichero = 'log/log_mensajero.log';
	// La nueva persona a añdir al fichero
	$cuando_y_que = date("d/m/y H:i:s") . " - " . $que . chr(13) . chr(10);
	// Escribir los contenidos en el fichero,
	// usando la bandera FILE_APPEND para añadir el contenido al final del fichero
	// y la bandera LOCK_EX para evitar que cualquiera escriba en el fichero al mismo tiempo
	file_put_contents($fichero, $cuando_y_que, FILE_APPEND | LOCK_EX);
}

function creaWAV($a)
{

	$salida = "tmp/salida" . session_id() . ".wav";
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

if (isset($_GET['action'])) {

	switch ($_GET['action']) {
		case "init":
			$r = "";
			foreach ($b as $ba) {
				$r .= $ba->innerHTML();
			}
			unset($ba);
			agregaObjeto("barrios", $r);


			$r = file_get_contents("encabezadomapa.svg");
			foreach ($b as $ba) {
				$r .= ($ba->getshape());
			}
			unset($ba);

			$r .= file_get_contents("piemapa.svg");
			agregaObjeto("mapa", $r);


			if (strlen($hora) == 0) {
				// no hay hora
				// ponemos la hora actual + 1
				$hora = date("H", time() + (1 * 60 * 60));
				$minutos = intval(date("i", time() + (1 * 60 * 60)) / 15) * 15;

				//$minutos = sprintf("%1$02d",$m);
			}

			if (isset($hora)) agregaAccion("document.getElementById('hora').selectedIndex=" . $hora . ";");
			if (isset($minutos)) agregaAccion("document.getElementById('minutos').selectedIndex=" . ($minutos / 15) . ";");
			if (isset($motivo)) agregaAccion("document.getElementById('motivo').selectedIndex=" . $mindex . ";");
			$chk = ($haymotivo) ? "checked" : "";
			if (isset($haymotivo)) agregaAccion("document.getElementById('haymotivo').checked='" . $chk . "';");

			// Actualiza Barrios
			for ($i = 1; $i <= count($b); $i++) {
				mandaBarrio($i);
			}


			break;
		case "timer":
			agregaObjeto("ahora", estado() . " - " . date("d-m-Y H:i:s"));
			break;

		case "toco":
			if ((isset($_GET['id']) && isset($_GET['tipo']))) {

				// toco un elemento
				$idtocado = $_GET['id'];
				$idtipo = $_GET['tipo'];
				switch ($idtipo) {
					case "svg":
						// procesa zona
						$idtocado = substr($idtocado, 4);
					case "barrio":
						// procesa barrio
						tocoBarrio($idtocado);
						break;

					case "hora":
						// procesa barrio
						tocohora($idtocado);
						break;

					case "minutos":
						// procesa barrio

						tocoMinutos($idtocado);
						break;

					case "boton":
						// procesa barrio
						tocoBoton($idtocado);
						break;

					case "motivo":
						// procesa barrio
						if (isset($_GET['index'])) {
							tocoMotivo($idtocado, $_GET['index']);
						}
						break;

					case "opcion":
						// procesa barrio
						tocoOpcion($idtocado);
						break;

					default:
						//no hace nada
						agregaAccion('alert("error de parametros; avise a dario...");');
						break;
				}
			} else {
				agregaAccion('alert("error de parametros; avise a dario...");');

				break;
			}
			break;

		default:
			break;
	}
}
function arma_mapa()
{
	global $b;

	$r = file_get_contents("encabezadomapa.svg");
	foreach ($b as $ba) {
		$shape = $ba->getshape();
		if ($ba->getestado()) {
			// barrio marcado
			$o = ' opacity="0.75" stroke-width="2" stroke="#000000" ';
		} else {
			$o = ' opacity="0.25" stroke-width="1" stroke="#b2b2b2" ';
		}
		$l = str_replace('onclick="daleSVG(evt,this)"', $o, $shape);
		$l = str_replace(array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&ntilde;"), array("a", "e", "i", "o", "u", "ñ"), $l);
		$r .= $l;
	}
	unset($ba);

	$r .= file_get_contents("piemapa.svg");
	return $r;
}

function mensaje_web()
{
	global $audio, $b;
	global $motivo;
	global $haymotivo;
	global $hayhorario;
	global $mindex;
	global $hora, $minutos;

	// arma mensaje
	$msj = "";
	$audio = array();
	/* eliminado 12-2012
	// no se pone más opción de mensaje inicial
	
	if ($saludoinicial)	{
		$msj .= "Se ha comunicado con la CPE.";
		$audio[] = "saludoinicial";
	}
	*/

	$belejidos = "";
	$abelejidos = array();
	$cbelejidos = 0;
	// Lista Barrios
	for ($i = 1; $i <= count($b); $i++) {
		if ($b[$i]->getestado()) {
			++$cbelejidos;
			if ($cbelejidos > 1) $belejidos .= ",";
			$belejidos .= " " . $b[$i]->getnombre();
			$abelejidos[count($audio) + count($abelejidos) + 2] = $b[$i]->getWAV() . "";
		}
	}

	if ($cbelejidos > 0) {
		// por barrios
		$msj .= " En estos momentos hay un corte de energ&iacute;a";
		if ($cbelejidos > 1) {
			// plural
			$msj .= "  que afecta a los barrios ";
			$audio[] = "cortequeafectaplural";
		} else {
			// singular
			$msj .= "  que afecta al barrio ";
			$audio[] = "cortequeafectasingular";
		}
		$msj .= $belejidos;
		$audio = $audio + $abelejidos;
	} else {
		// corte sin especificar barrios
		$msj .= " Actualmente hay un corte de energ&iacute;a";
		$audio[] = "corte";
	}

	if ($haymotivo) {
		$msj .= " debido a ";
		$audio[] = "sedebea";
		$msj .= $motivo;
		$audio[] = "motivo" . $mindex;
	}

	if ($hayhorario) {
		// hay horario de reposición
		$msj .= " y estimamos reponer el servicio a la hora ";
		$audio[] = "alas";
		$msj .= $hora . ":" . $minutos;
		//		$msj .= $hora ." y " .$minutos;
		$audio[] = "hora" . $hora;
		if ($minutos == 0) {

			// $audio[] = "horas";

		} else {

			//$audio[] = "y";
			$audio[] = "minutos" . $minutos;
		}
	} else {
		// No hay horario de reposición
		//borra el aviso de mensaje caduco
		unlink("/var/spool/asterisk/outgoing/llamaahora.call");
	}


	return $msj;
}

if (true) {

	// 12-2012 se decide dejar siempre este final de mensaje
	//if ($saludofinal)	{
	$m = mensaje_web();
	$m .= ". Si usted no est&aacute; en la zona o quiere informar un accidente o emergencia en la v&iacutea p&uacuteblica, aguarde y ser&aacute atendido...";
	$audio[] = "saludofinal";
	$audio[] = "ComseraGrabada";
	//}
	$m .= ".";

	//	agregaObjeto("mensaje",implode(",", $abelejidos));
	//	agregaObjeto("mensaje",implode(",", $audio));
	agregaObjeto("mensaje", $m);
	creaWAV($audio);


	// prepare xml data
	header('Content-type: text/xml;charset="ISO-8859-15"');
	echo "<ids>";
	echo $respuesta;
	echo "</ids>";

	$respuesta = "";
}

$_SESSION['BARRIOS'] = $b;
$_SESSION['hora'] = $hora;
$_SESSION['minutos'] = $minutos;
$_SESSION['motivo'] = $motivo;
$_SESSION['mindex'] = $mindex;
$_SESSION['haymotivo'] = $haymotivo;
//$_SESSION['saludoinicial']=$saludoinicial;
//$_SESSION['saludofinal']=$saludofinal;
$_SESSION['hayhorario'] = $hayhorario;
