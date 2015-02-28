<?php
namespace fw;

require_once("Annotation.php");
require_once("Point.php");
require_once("Style.php");

/*
 * Text label for drawable elements such as nodes or particles
 */

class Label extends Annotation
{
	//__________________________________________________________________
	// Properties
	
	// The text of the label
	private $text;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($id_, Style $mother_, $text_, Point $point_, $position_ = "w", $radius_ = 20.0, $offset_x_ = 0.0, $offset_y_ = 0.0)
	{
		$this->id = $id_;
		$this->offset_x = $offset_x_;
		$this->offset_y = $offset_y_;
		$this->point = $point_;
		$this->setPosition($position_);
		$this->radius = $radius_;
		$this->style = new Style($mother_);
		$this->text = $text_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	public function addText($text_)
	{
		$this->setText($this->getText() . $text_);
	}
	
	public function getText()
	{
		return $this->text;
	}
	
	public function setText($text_)
	{
		$this->text = $text_;
	}
	
	
	public function svg()
	{
		$p = $this->position;
		$r = $this->radius;
		$c = sqrt(2.0);
		
		// Compute the x,y position
		$x = $this->point->x();
		$y = $this->point->y();
		
		switch($p)
		{
			case 'n':
				$y -= $r;
				break;
			case 'ne':
				$x += ($c*$r);
				$y -= ($c*$r);
				break;
			case 'e':
				$x += $r;
				break;
			case 'se':
				$x += ($c*$r);
				$y += ($c*$r);
				break;
			case 's':
				$y += $r;
				break;
			case 'sw':
				$x -= ($c*$r);
				$y += ($c*$r);
				break;
			case 'w':
				$x -= $r;
				break;
			case 'nw':
				$x -= ($c*$r);
				$y -= ($c*$r);
				break;
		}
		
		$x += $this->offset_x;
		$y += $this->offset_y;
		
		
		// Begin the text element and its various styles
		$s = '<text id="' . $this->id . '"';
		$s .= ' x="' . $x . '" y="' . $y . '"';
		$s .= ' font-family="' . $this->style->get("font-family") . '"';
		$s .= ' font-size="' . $this->style->get("font-size") . '"';
		$s .= ' font-style="' . $this->style->get("font-style") . '"';
		$s .= ' font-weight="' . $this->style->get("font-weight") . '"';
		
		// Set the horizontal text position
		if( ($p == 'n') || ($p== 's') || ($p == 'c') )
		{
			$s .= ' text-anchor="middle"';
		}
		else if( substr_count($p, 'e') )
		{
			$s .= ' text-anchor="start"';
		}
		else if( substr_count($p, 'w') )
		{
			$s .= ' text-anchor="end"';
		}
		
		// Set the vertical text position
		$s .= '><tspan ';
		if( substr_count($p, 's') )
		{
			$s .= 'baseline-shift="-100%"';
		}
		else if( ($p == 'e') || ($p == 'w') || ($p == 'c') )
		{
			$s .= 'baseline-shift="-50%"';
		}
		
		// text-decoration is often used for anti-particles
		$s .= ' text-decoration="' . $this->style->get("text-decoration") . '">';
		
		// Finish
		$s .= $this->text . '</tspan></text>';
		
		return $s;
	}
	
}

?>
