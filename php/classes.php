<?php

class barrio
{
    // Declaración de la propiedad
    private $id;
	private $nombre;
	private $zona;
	private $shape="";
	private $wav1;
	private $wav2;
	private $wav3;
	//private $html;
	private $estado;
	
    // Declaración de métodos
    function __construct($i,$n,$z,$sh,$w1,$w2,$w3) {
		
		$this->id = $i;
        $this->nombre = $n;
		$this->zona = $z;
		$this->shape = $sh;
		$this->wav1 = $w1;
		$this->wav2 = $w2;
		$this->wav3 = $w3;
		$this->estado = false;
		//$this->html = '<label><input type="checkbox" onchange="dale(this)" id="' .$i .'" />' .$n .'</label><br />';
	}
	
	public function getnombre() {
		return $this->nombre;
	}

	public function getWAV() {
		$nm=strtolower(html_entity_decode($this->nombre));
		$nw="";
		for ($i = 0; $i <= strlen($nm); $i++) {
			$c = substr($nm,$i,1);
			if (strpos("; abcdefghijklmnopqrstuvwxyz0123456789.-",$c) ) $nw .= $c;
		}
		return $nw;
	}

	public function getshape() {
		return $this->shape;
	}

	public function getestado() {
		return $this->estado;
	}

	public function setestado($est) {
		$this->estado=$est;
	}
	
	public function togleestado() {
		$this->estado = !$this->estado;
	}

	public function innerHTML() {
		 $h = '<label><input type="checkbox" onchange="dale(this)" id="' .$this->id .'" ';
		 if ($this->getestado()) $h .= 'checked="checked"';
		 $h .= '/>' .$this->nombre .'</label><br />';
		 //$this->html=$h;
		 
		return $h;
	}
}


?>
