<?php
namespace fw;

require_once("Annotation.php");

/*
 * Arrow drawn either on or parallel to a particle line
 */

class Arrow extends Annotation
{
	//__________________________________________________________________
	// Properties
	
	// True if arrow is along line, false if it is counter
	private $sense;
	private $vector;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($id_, Style $mother_, Vector $vector_, Point $point_, $position_ = "w", $radius_ = 20.0, $offset_x_ = 0.0, $offset_y_ = 0.0, $sense_ = true)
	{
		$this->id = $id_;
		$this->offset_x = $offset_x_;
		$this->offset_y = $offset_y_;
		$this->point = $point_;
		$this->setPosition($position_);
		$this->radius = $radius_;
		$this->sense = (bool)$sense_;
		$this->style = new Style($mother_);
		$this->vector = $vector_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	//
	public function sense()
	{
		return $this->sense;
	}
	
	// Set the sense of the arrow
	public function setSense($sense_)
	{
		$this->sense = (bool)$sense_;
	}
	
	public function setVector(Vector $vector_)
	{
		$this->vector = $vector_;
	}
	
	// 
	public function svg()
	{
		if( $this->position == 'c')
		{
			return $this->svg_centre();
		}
		else
		{
			return $this->svg_parallel();
		}
	}
	
	//
	private function svg_centre()
	{
		// Variable to control the scale of the arrow
		$size = $this->style->get("line-width");
		
		// Centre point of the arrow
		$x = $this->point->x();
		$y = $this->point->y();
		
		// Directions
		$v = $this->vector;
		if(!$this->sense) $this->vector->scale(-1.0);
		$n = $v->norm();
		
		// Pinacle of the arrow
		$x0 = $x + ($v->x() * 2.5 * $size);
		$y0 = $y + ($v->y() * 2.5 * $size);
		
		// Centre point between the two tail points
		$x -= ($v->x() * 2.5 * $size);
		$y -= ($v->y() * 2.5 * $size);
		
		// Tail point 1
		$x1 = $x + ($n->x() * 3 * $size);
		$y1 = $y + ($n->y() * 3 * $size);
		
		// Tail point 2
		$x2 = $x - ($n->x() * 3 * $size);
		$y2 = $y - ($n->y() * 3 * $size);
		
		
		$svg = '<path id="' . $this->id . '" d="';
		$svg .= 'M ' . $x0 . ' ' . $y0 . ' ';
		$svg .= 'L ' . $x1 . ' ' . $y1 . ' ';
		$svg .= 'L ' . $x2 . ' ' . $y2 . ' ';
		$svg .= 'Z" stroke="none" fill="' . $this->style->get("line-color") . '" />';
		return $svg;
	}
	
	
	private function svg_parallel()
	{
		// Variable to control the scale of the arrow
		$size = $this->style->get("line-width");
		
		// Directions
		$v = $this->vector;
		if(!$this->sense) $this->vector->scale(-1.0);
		$n = $v->norm();
		
		// Centre point of the arrow
		$x = $this->point->x();
		$y = $this->point->y();
		$p = $this->position;
		if( ($p == 'n') || ($p == 'nw') || ($p == 'ne') )
		{
			$y -= $this->radius;
		}
		else if( ($p == 's') || ($p == 'sw') || ($p == 'se') )
		{
			$y += $this->radius;
		}
		if( ($p == 'e') || ($p == 'ne') || ($p == 'se') )
		{
			$x += $this->radius;
		}
		else if( ($p == 'w') || ($p == 'nw') || ($p == 'sw') )
		{
			$x -= $this->radius;
		}
		
		// Tail of the line
		$x0 = $x - ($size * 6 * $v->x());
		$y0 = $y - ($size * 6 * $v->y());
		
		// Point of the arrow
		$x1 = $x + ($size * 4 * $v->x());
		$y1 = $y + ($size * 4 * $v->y());
		
		// Tail point 1 of the arrow
		$x2 = $x + ($n->x() * 3 * $size);
		$y2 = $y + ($n->y() * 3 * $size);
		
		// Tail point 2 of the arrow
		$x3 = $x - ($n->x() * 3 * $size);
		$y3 = $y - ($n->y() * 3 * $size);
		
		$svg = '<g id="' . $this->id . '">';
		
		$svg .= '<path id="' . $this->id . '-1" d="';
		$svg .= 'M ' . $x1 . ' ' . $y1 . ' ';
		$svg .= 'L ' . $x2 . ' ' . $y2 . ' ';
		$svg .= 'L ' . $x3 . ' ' . $y3 . ' ';
		$svg .= 'Z" stroke="none" fill="' . $this->style->get("line-color") . '" />';
		
		$svg .= '<path id="' . $this->id . '-1" d="';
		$svg .= 'M ' . $x0 . ' ' . $y0 . ' ';
		$svg .= 'L ' . $x . ' ' . $y . '"';
		$svg .= ' stroke="' . $this->style->get("line-color") . '"';
		$svg .= ' stroke-width="' . $this->style->get("line-width") . '"';
		$svg .= ' fill="none" />';
		
		$svg .= '</g>';
		
		return $svg;
	}
	
	//
	public function vector()
	{
		return $this->vector;
	}
}
?>
