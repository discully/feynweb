<?php
namespace fw;

require_once("Particle.php");
require_once("Arc.php");
require_once("Line.php");
require_once("Point.php");

/*
 * Particle represented by a wavy line conventionally used to denote
 * photons
 */

class Photon extends Particle
{
	//__________________________________________________________________
	// Properties
	
	
	
	//__________________________________________________________________
	// Constructor
	
	// photon specific construction tasks (none at present)
	protected function init()
	{
		return;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	// Convenience method to set the photon-wavelength style
	public function setWavelength($wl_)
	{
		$this->style->set("photon-wavelength", $wl_);
	}
	
	// Convenience method to set the photon-amplitude style
	public function setAmplitude($a_)
	{
		$this->style->set("photon-amplitude", $a_);
	}
	
	// returns the svg representation as a string
	public function svg()
	{
		$wavelength = $this->style->get("photon-wavelength");
		$amplitude = $this->style->get("photon-amplitude");
		
		// get number of draw points the line is split into
		$l = $this->path->length();
		$n = round( $l / $wavelength );
		$n *= 4;
		
		$d = $l / $n;
		$p = 0.0;
		
		$sx = $this->path->start()->x();
		$sy = $this->path->start()->y();
		$a4 = $this->path->start();
		
		$svg = '<path id="' . $this->id . '" d="M ' . $sx . ' ' . $sy;
		
		for($i = 0; $i != $n; $i+=4)
		{
			$p += $d;
			$a1 = $this->path->positionAt($p);
			$b1 = $this->path->normalAt($p);
			$b1 = $b1->unit();
			$b1->scale($amplitude);
			$a1->translate($b1);
			
			$p += $d;
			$a2 = $this->path->positionAt($p);
			
			$p += $d;
			$a3 = $this->path->positionAt($p);
			$b3 = $this->path->normalAt($p);
			$b3 = $b3->unit();
			$b3->scale(-$amplitude);
			$a3->translate($b3);
			
			$p += $d;
			$a4 = $this->path->positionAt($p);
			
			
			$svg .= " Q " . $a1->x() . "," . $a1->y() . " " . $a2->x() . "," . $a2->y();
			$svg .= " Q " . $a3->x() . "," . $a3->y() . " " . $a4->x() . "," . $a4->y();
			
		}
		
		$svg .= '" stroke="' . $this->style->get("line-color") . '" stroke-width="' . $this->style->get("line-width") . '"';
		if($this->style->get("line-dash-array") != 0)
		{
			$svg .= ' stroke-dasharray="' . $this->style->get("line-dash-array") . '"';
		}
		$svg .= ' fill="none" />';
		
		if($this->arrow != "unset") $svg .= $this->arrow->svg();
		if($this->label != "unset") $svg .= $this->label->svg();
		
		return $svg;
	}
}

?>
