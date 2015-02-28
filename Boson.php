<?php
namespace fw;

require_once("Arc.php");
require_once("Line.php");
require_once("Particle.php");
require_once("Point.php");

/*
 * Particle represented by a dashed straight line conventionally used
 * for W and Z bosons
 */

class Boson extends Particle
{
	//__________________________________________________________________
	// Properties
	
	
	
	//__________________________________________________________________
	// Constructor
	
	protected function init()
	{
		return;
	}
	
	
	//__________________________________________________________________
	// Methods
	
	public function setBosonDashArray($dash_array_)
	{
		$this->style->set("line-dash-array", $dash_array_);
		$this->style->set("boson-dash-array", $dash_array_);
	}
	
	public function svg()
	{
		$svg = '<path id="' . $this->id . '" ';
		
		if($this->path->isLine()) $svg .= $this->svg_line();
		else $svg .= $this->svg_arc();
		
		if($this->style->get("boson-dash-array") != 0)
		{
			$svg .= ' stroke-dasharray="' . $this->style->get("boson-dash-array") . '"';
		}
		
		$svg .= ' stroke="' . $this->style->get("line-color") . '"';
		$svg .= ' stroke-width="' . $this->style->get("line-width") . '"';
		$svg .= ' fill="none" />';
		
		if($this->arrow != "unset") $svg .= "\n" . $this->arrow->svg();
		if($this->label != "unset") $svg .= "\n" . $this->label->svg();
		
		return $svg;
	}
	
	private function svg_arc()
	{
		$x_rotation = 0;
		
		if($this->path->isLarge()) $large_arc_flag = 1;
		else $large_arc_flag = 0;
		
		if($this->path->isClockwise()) $sweep_flag = 1;
		else $sweep_flag = 0;
		
		$svg = 'd=" M ' . $this->path->start()->x() . ' ' . $this->path->start()->y();
		$svg .= ' A ' . $this->path->radius() . ' ' . $this->path->radius();
		$svg .= ' ' . $x_rotation . ' ' . $large_arc_flag . ' ' . $sweep_flag;
		$svg .= ' ' . $this->path->end()->x() . ' ' . $this->path->end()->y() . '"';
		
		return $svg;
	}
	
	private function svg_line()
	{
		$svg = 'd=" M ' . $this->path->start()->x() . ',' . $this->path->start()->y();
		$svg .= ' L ' . $this->path->end()->x() . ',' . $this->path->end()->y() . '"';
		
		return $svg;
	}
}

?>
