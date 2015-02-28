<?php
namespace fw;

require_once("Path.php");
require_once("Point.php");
require_once("Vector.php");

/*
 * A straight path between two points
 */

class Line extends Path
{//__________________________________________________________________
	// Properties
	
	// Point on which line starts
	private $start;
	// Point on which line ends
	private $end;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct(Point $start_, Point $end_)
	{
		$this->start = $start_;
		$this->end = $end_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	// Return the point at the end point of the path
	public function end()
	{
		return $this->end;
	}
	
	
	// Since line is straight, returns false
	public function isArc()
	{
		return false;
	}
	
	// Since line is straight, returns false
	public function isClockwise()
	{
		return false;
	}
	
	
	// Identifies as a line by returning true
	public function isLine()
	{
		return true;
	}
	
	
	// Returns the length along the path
	public function length()
	{
		$l = $this->end->dist($this->start);
		return $l;
	}
	
	
	// Returns a unit vector normal to the path
	// The length has no affect for a straight line but must be supplied
	public function normalAt($length_)
	{
		$v = $this->vectorAt($length_);
		$v = $v->norm();
		
		return $v;
	}
	
	
	// Returns a node at the distance along the line specified
	public function positionAt($length_)
	{
		$n = new Point();
		
		if( $length_ > $this->length() )
		{
			trigger_error("Length is greater than line. Returning end position.", E_USER_WARNING);
			$n = $this->end();
		}
		else if( $length_ < 0.0 )
		{
			trigger_error("Length is less than 0. Returning start position.", E_USER_WARNING);
			$n = $this->start();
		}
		else
		{
			$v = $this->vectorAt($length_);
			$v->scale($length_);
			$n->setX($this->start->x() + $v->x());
			$n->setY($this->start->y() + $v->y());
		}
		
		return $n;
	}
	
	
	// Set the end position to the supplied point
	public function setEnd(Point $n_)
	{
		$this->end = $n_;
	}
	
	
	// Set the start position to the supplied point
	public function setStart(Point $n_)
	{
		$this->start = $n_;
	}
	
	
	// Returns a node at the start point of the path
	public function start()
	{
		return $this->start;
	}
	
	
	// Returns a unit vector along the path
	// The length has no affect for a straight line
	public function vectorAt($length_)
	{
		$dx = $this->end->x() - $this->start->x();
		$dy = $this->end->y() - $this->start->y();
		$v = new Vector($dx,$dy);
		$v = $v->unit();
		
		return $v;
	}
}
?>
