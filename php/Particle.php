<?php
namespace fw;

require_once("Arc.php");
require_once("Arrow.php");
require_once("Element.php");
require_once("Line.php");
require_once("Point.php");
require_once("Style.php");

/*
 * An abstract class for particle lines in the diagram
 */

abstract class Particle extends Element
{
	//__________________________________________________________________
	// Properties
	
	protected $path;
	protected $arrow;
	protected $label;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($id_, Style $mother_, Point $start_, Point $end_, $radius_ = 0.0, $clockwise_ = true, $large_ = false)
	{
		$this->id = $id_;
		
		$this->style = new Style($mother_);
		
		if($radius_ == 0.0)
		{
			$this->path = new Line($start_, $end_);
		}
		else
		{
			$this->path = new Arc($start_, $end_, $radius_, $clockwise_, $large_);
		}
		
		$this->label = "unset";
		$this->arrow = "unset";
		
		// Call any class specific initialisation
		$this->init();
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	public function arrow()
	{
		if($this->arrow == "unset")
		{
			return NULL;
		}
		else
		{
			return $this->arrow;
		}
	}
	
	abstract protected function init();
	
	public function addLabel($string_, $position_ = "w", $radius_ = 20.0, $offset_x_ = 0.0, $offset_y_ = 0.0)
	{
		if($this->label == "unset")
		{
			$point = $this->path->positionAt($this->path->length() / 2.0);
			$id = $this->id . '-label';
			
			$this->label = new Label($id, $this->style, $string_, $point, $position_, $radius_, $offset_x_, $offset_y_);
		}
		else
		{
			trigger_error("Element already has exisiting label and multiple labels are not supported.", E_USER_WARNING);
		}
	}
	
	public function addArrow($position_ = "c", $radius_ = 0.0, $offset_x_ = 0.0, $offset_y_ = 0.0)
	{
		if($this->arrow == "unset")
		{
			$id = ($this->id . "-arrow");
			$point = $this->path->positionAt( $this->path->length() / 2.0 );
			$vector = $this->path->vectorAt( $this->path->length() / 2.0 );
			$this->arrow = new Arrow($id, $this->style, $vector, $point, $position_, $radius_ = 0.0, $offset_x_ = 0.0, $offset_y_ = 0.0);
		}
		else
		{
			trigger_error("Element already has exisiting arrow and multiple arrows are not supported.", E_USER_WARNING);
		}
	}
	
	public function getElement($id_)
	{
		if( $this->label() != NULL )
		{
			if($this->label()->id() == $id_) return $this->label();
		}
		else if( $this->arrow() != NULL )
		{
			if($this->arrow()->id() == $id_) return $this->arrow();
		}
		return NULL;
	}
	
	public function path()
	{
		return $this->path;
	}
	
	public function label()
	{
		if($this->label == "unset")
		{
			return NULL;
		}
		else
		{
			return $this->label;
		}
	}
	
	public function setDashArray($dash_string_)
	{
		$this->style->set("dash-array", $dash_string_);
	}
	
	public function setLineColor($color_)
	{
		$this->style->set("line-color", $color_);
	}
	
	public function setLineWidth($width_)
	{
		$this->style->set("line-width", $width_);
	}
}
?>
