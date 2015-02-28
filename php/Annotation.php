<?php
namespace fw;

require_once("Element.php");
require_once("Point.php");

/*
 * An abstract class for secondary elements drawn on primary ones
 * eg: arrows or labels
 */

abstract class Annotation extends Element
{
	//__________________________________________________________________
	// Properties
	
	// Optional offsets relative to the normal position of the label
	// to allow fine tuning of the label if necessary
	protected $offset_x;
	protected $offset_y;
	
	// The point which the label is positioned wrt
	protected $point;
	
	// Poition can be any of n,ne,e,se,s,sw,w,nw
	// Determines the side of Point on which the label is drawn
	protected $position;
	
	// The distance from Point at which the text is drawn
	protected $radius;
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	public function getElement($id_)
	{
		return NULL;
	}
	
	
	public function getOffsetX()
	{
		return $this->offset_x;
	}
	
	
	public function getOffsetY()
	{
		return $this->offset_y;
	}
	
	
	public function getPoint()
	{
		return $this->point;
	}
	
	
	public function getPosition()
	{
		return $this->position;
	}
	
	
	public function getRadius()
	{
		return $this->radius;
	}
	
	
	public function setOffsetX($offset_x_)
	{
		$this->offset_x = $offset_x_;
	}
	
	
	public function setOffsetY($offset_y_)
	{
		$this->offset_y = $offset_y_;
	}
	
	
	public function setPoint(Point $point_)
	{
		$this->point = $point_;
	}
	
	
	public function setPosition($position_)
	{
		$p = strtolower($position_);
		if(	$p == "n" || $p == "ne" ||
			$p == "e" || $p == "se" ||
			$p == "s" || $p == "sw" ||
			$p == "w" || $p == "nw"	||
			$p == "c" )
		{
			$this->position = $p;
		}
		else
		{
			trigger_error("Invalid position ($position_). Position has not been changed", E_USER_WARNING);
		}
	}
	
	
	public function setRadius($radius_)
	{
		$this->radius = $radius_;
	}
}
?>
