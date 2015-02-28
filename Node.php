<?php
namespace fw;

require_once("Element.php");
require_once("Point.php");

/*
 * An intersection, source or sink for paricles in the diagram
 */

class Node extends Element
{
	//__________________________________________________________________
	// Properties
	
	private $point;
	private $type;
	protected $label;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($id_, Style $mother_, $x_ = 0.0, $y_ = 0.0, $type_="dot")
	{
		$this->id = $id_;
		$this->label = "unset";
		$this->point = new Point($x_, $y_);
		$this->style = new Style($mother_);
		$this->type = $type_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	public function addLabel($string_, $position_ = "w", $radius_ = 20.0, $offset_x_ = 0.0, $offset_y_ = 0.0)
	{
		$id = $this->id . '-label';
		
		$this->label = new Label($id, $this->style, $string_, $this->point, $position_, $radius_, $offset_x_, $offset_y_);
	}
	
	public function getElement($id_)
	{
		if( $this->label() != NULL )
		{
			if($this->label()->id() == $id_) return $this->label();
		}
		return NULL;
	}
	
	public function getPoint()
	{
		return $this->point;
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
	
	// Synonym for getPoint()
	public function point()
	{
		return $this->getPoint();
	}
	
	public function setPoint(Point $p_)
	{
		$this->point = $p_;
	}
	
	public function setType($type_)
	{
		$this->type = $type_;
	}
	
	public function setX($x_)
	{
		$this->point()->setX($x_);
	}
	
	public function setY($y_)
	{
		$this->point()->setY($y_);
	}
	
	public function svg()
	{
		$svg = "";
		if($this->type == "dot")		$svg .= $this->svg_dot();
		else if($this->type == "cross")	$svg .= $this->svg_cross();
		else if($this->type == "blob")	$svg .= $this->svg_blob();
		else if($this->type == "none")	$svg .= "";
		else 							$svg = $this->svg_blob();
		
		if($this->label != "unset") 	$svg .= "\n" . $this->label->svg() . "\n";
		
		return $svg;
	}
	
	private function svg_dot()
	{
		$svg = '<circle id="' . $this->id . '"';
		$svg .= ' cx="' . $this->point->x() . '" cy="' . $this->point->y() . '"';
		$svg .= ' r="' . $this->style->get("dot-radius") . '" fill="' . $this->style->get("dot-fill") . '" />';
		return $svg;
	}
	
	private function svg_cross()
	{
		$svg = '<g id="' . $this->id . '">';
		
		$svg .= '<line';
		$svg .= ' x1="' . ($this->point->x()-5) . '" x2="' . ($this->point->x()+5) . '"';
		$svg .= ' y1="' . ($this->point->y()-5) . '" y2="' . ($this->point->y()+5) . '"';
		$svg .= ' stroke="black" stroke-width="3" />';
		
		$svg .= '<line';
		$svg .= ' x1="' . ($this->point->x()-5) . '" x2="' . ($this->point->x()+5) . '"';
		$svg .= ' y1="' . ($this->point->y()+5) . '" y2="' . ($this->point->y()-5) . '"';
		$svg .= ' stroke="black" stroke-width="3" />';
		
		$svg .= '</g>';
		return $svg;
	}
	
	private function svg_blob()
	{
		$svg = '<circle id="' . $this->id . '-under"';
		$svg .= ' cx="' . $this->point->x() . '" cy="' . $this->point->y() . '"';
		$svg .= ' r="' . $this->style->get("blob-radius") . '"';
		$svg .= ' fill="' . $this->style->get("blob-fill") . '"';
		$svg .= ' stroke-width="2" stroke="black" />';
		
		$pattern = $this->style->get("blob-pattern");
		if( ($pattern == "cross") || ($pattern == "diag") )
		{
			$svg .= '<circle id="' . $this->id . '-over"';
			$svg .= ' cx="' . $this->point->x() . '" cy="' . $this->point->y() . '"';
			$svg .= ' r="' . $this->style->get("blob-radius") . '"';
			if( $pattern == "cross")	$svg .= ' fill="url(#blobCross)"';
			else if($pattern == "diag")	$svg .= ' fill="url(#blobDiag)"';
			else 						$svg .= ' fill="transparent"';
			$svg .= ' stroke-width="2" stroke="black" />';
		}
		
		return $svg;
	}
	
	private function svg_default()
	{
		$svg = '<circle id="' . $this->id . '"';
		$svg .= ' cx="' . $this->point->x() . '" cy="' . $this->point->y() . '"';
		$svg .= ' r="4" fill="red" />';
		return $svg;
	}
}
