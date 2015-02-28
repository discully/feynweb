<?php
namespace fw;

require_once("Path.php");
require_once("Point.php");
require_once("Vector.php");

/*
 * A general circular curved path between two points.
 */
 
class Arc extends Path
{
	//__________________________________________________________________
	// Properties
	
	// Point at arc's centre
	private $centre;
	
	// Vector from centre to start
	private $c2s;
	
	// Vector from centre to end
	private $c2e;
	
	// Boolean indicating whether the arc is clockwise (true) or anti-clockwise (false)
	private $cw;
	
	// Boolean indicating whether the arc is large (true) or small (flase)
	private $large;
	
	// Radius of the arc
	private $r;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct(Point $start_, Point $end_, $radius_, $clockwise_ = true, $large_ = false)
	{
		$this->setArc($start_, $end_, $radius_, $clockwise_, $large_);
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	// Return Point at centre
	public function centre()
	{
		return $this->centre;
	}
	
	
	// Returns Point at end
	public function end()
	{
		$end = new Point( ($this->centre->x() + $this->c2e->x()), ($this->centre->y() + $this->c2e->y()) );
		return $end;
	}
	
	
	// Returns node at mid-point between start and end
	public function getMidPoint(Point $start_, Point $end_)
	{
		$x = ($start_->x() + $end_->x()) / 2.0;
		$y = ($start_->y() + $end_->y()) / 2.0;
		$n = new Point($x, $y);
		return $n;
	}
	
	
	// Returns true, indicating that this is an arc
	public function isArc()
	{
		return true;
	}
	
	
	// Returns true if clockwise, false if anti-clockwise
	public function isClockwise()
	{
		return $this->cw;
	}
	
	
	// Returns true if large, false if small
	public function isLarge()
	{
		return $this->large;
	}
	
	
	// Returns false, because this is an arc
	public function isLine()
	{
		return false;
	}
	
	
	// Returns the length along the arc
	public function length()
	{
		$theta = $this->c2s->theta($this->c2e);
		
		if(!$this->cw) $theta = 2.0*pi() - $theta;
		$l = $this->r * $theta;
		
		return $l;
	}
	
	
	// Return s a vector normal to the arc at a distance $length_ along
	// the arc
	public function normalAt($length_)
	{
		$n = $this->c2s;
		$n = $n->unit();
		
		$theta = $length_ / $this->r;
		
		if(!$this->cw) $theta *= -1.0;
		
		$n->rotate($theta);
		
		return $n;
	}
	
	
	// Returns a Point corresponding to a position at distance $length_
	// along the arc
	public function positionAt($length_)
	{
		$n = $this->normalAt($length_);
		$n->scale($this->r);
		
		$p = new Point( ($this->centre->x() + $n->x()), ($this->centre->y() + $n->y()) );
		return $p;
	}
	
	
	// Returns the arc's radius
	public function radius()
	{
		return $this->r;
	}
	
	
	// Used to calculate the arc's properties
	private function setArc(Point $start_, Point $end_, $radius_, $clockwise_ = true, $large_ = false)
	{
		if( $start_ == $end_)
		{
			trigger_error("Start and end points are the same. Not currently supported", E_USER_ERROR);
		}
		
		if( $radius_ < ($start_->dist($end_)/2.0) )
		{
			trigger_error("Radius is too small.", E_USER_ERROR);
		}
		
		$this->cw = $clockwise_;
		$this->large = $large_;
		$this->r = $radius_;
		
		$this->centre = $this->setCentre($start_, $end_);
		
		$this->c2s = new Vector( ($start_->x() - $this->centre->x()), ($start_->y() - $this->centre->y()) );
		$this->c2e = new Vector( ($end_->x() - $this->centre->x()), ($end_->y() - $this->centre->y()) );
	}
	
	
	
	// Determines the centre Point
	private function setCentre(Point $start_, Point $end_)
	{
		// Get mid-point
		$mid = $this->getMidPoint($start_, $end_);
		
		// Determine distance from mid-point to centre
		$s2m = $start_->dist($mid);
		$m2c = sqrt( $this->r*$this->r - $s2m*$s2m );
		
		// Get vector from midpoint to one of the centres
		$start_to_end = new Vector( ($end_->x()-$start_->x()), ($end_->y()-$start_->y()) );
		$start_to_end = $start_to_end->unit();
		
		$mid_to_centre = $start_to_end->norm();
		$mid_to_centre->scale($m2c);
		
		// Get two candidate centres
		$c1 = new Point( ($mid->x() + $mid_to_centre->x()), ($mid->y() + $mid_to_centre->y()) );
		$c2 = new Point( ($mid->x() - $mid_to_centre->x()), ($mid->y() - $mid_to_centre->y()) );
		
		// get vectors from c1 to start/end points
		$c1s = new Vector( ($start_->x() - $c1->x()), ($start_->y() - $c1->y()));
		$c1e = new Vector( ($end_->x() - $c1->x()), ($end_->y() - $c1->y()));
		
		// determine if small arc is clockwise or not
		$c1cw = ( $c1s->cross($c1e) > 0.0  );
		// determine if arc is small or large
		$c1l = ( $c1s->theta($c1e) > pi() );
		
		// return appropriate centre point
		if( $c1cw == $this->isClockwise())
		{
			if( $c1l == $this->isLarge() )
			{
				return $c1;
			}
			else
			{
				return $c2;
			}
		}
		else
		{
			if( $c1l == $this->isLarge())
			{
				return $c2;
			}
			else
			{
				return $c1;
			}
		}
	}
	
	
	
	public function setClockwise($clockwise_)
	{
		$this->setArc($this->start(), $this->end(), $this->radius(), $clockwise_, $this->isLarge());
	}
	
	
	// Changes the end point to $end_
	public function setEnd(Point $end_)
	{
		$this->setArc($this->start(), $end_, $this->radius(), $this->isClockwise, $this->isLarge());
	}
	
	
	public function setLarge($large_)
	{
		$this->setArc($this->start(), $this->end(), $this->radius(), $this->isClockwise(), $large_);
	}
	
	
	// Changes the start point to $start_
	public function setStart(Point $start_)
	{
		$this->setArc($start_, $this->end(), $this->radius(), $this->isClockwise, $this->isLarge());
	}
	
	
	// Returns Point at the start
	public function start()
	{
		$start = new Point( ($this->centre->x() + $this->c2s->x()), ($this->centre->y() + $this->c2s->y()) );
		return $start;
	}
	
	
	// Returns a Vector along the arc's direction at distance $length_
	// along it
	public function vectorAt($length_)
	{
		$v = $this->normalAt($length_);
		
		if($this->cw)
		{
			$v->rotate(pi()/2.0);
		}
		else
		{
			$v->rotate(-pi()/2.0);
		}
		
		return $v;
	}
}
