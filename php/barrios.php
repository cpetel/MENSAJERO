<?php

require_once("classes.php");

global $b;

// Función de comparación
function cmp($a, $b) {
    if ($a->getnombre() == $b->getnombre()) {
        return 0;
    }
    return ($a->getnombre() < $b->getnombre()) ? -1 : 1;
}

if (count($b)<5) {
//if (true) {

//					echo "BARRIOS";
					$ini_array = file("barrios.ini");
					unset($GLOBALS["BARRIOS"]);
					unset($b);
					$b=array();
					$ns=0;
					$n="";
					$i=0;
					foreach ($ini_array as $line_num => $line) {
						$l = trim($line);

						if ((strlen($l)==0) && (strlen($n)>0)) {
							// graba anterior
								// crea barrio
								++$ns;
								$sh="<g><title>" .$n ."</title>";
								foreach ($s as $line_numa => $linea) {
									$sh .= '<' .$linea .' id="svg_' .$ns .'" onclick="daleSVG(evt,this)"/>';
								}
								$sh .= "</g>";
								$b[$ns]= new barrio($ns, $n,"zz",$sh ,"" ,"" ,"");

								// resetea variables
								
								$n="";
								$s="";
							}
							// 
							if (strlen($n)==0) $i=0;

							++$i;
							switch ($i) {
								case 1:
									// nombre
									$n = $l;
								break;
								case 2:
									// shape
									$s[0] = $l;
								break;
								default:
									// sigue shape
									$s[count($s)] = $l;
								break;
							}
					
					uasort($b, 'cmp');
					}


}

?>
