<?php
namespace fw;

/*
 * A general 2D vector
 */

class Vector
{
	//__________________________________________________________________
	// Properties
	
	// x components
	private $x;
	// y component
	private $y;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($x_ = 0.0, $y_ = 0.0)
	{
		$this->x = $x_;
		$this->y = $y_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	// Returns the 2D equivalent of a cross-product with another vector
	public function cross(Vector $v_)
	{
		$c = ( ($this->x()*$v_->y()) - ($this->y()*$v_->x()) );
		return $c;
	}
	
	
	// Returns the dot product with another vector
	public function dot(Vector $v_)
	{
		$d = ( $this->x()*$v_->x() ) + ( $this->y()*$v_->y() );
		return $d;
	}
	
	
	// Returns the magnitude
	public function mag()
	{
		$m = $this->mag2();
		$m = sqrt($m);
		return $m;
	}
	
	
	// Returns the magnitude squared
	public function mag2()
	{
		$m2 = ( $this->x()*$this->x() ) + ( $this->y()*$this->y() );
		return $m2;
	}
	
	
	// Subtract a vector from the current vector
	public function minus(Vector $v_)
	{
		$this->setX( $this->x - $v_->x() );
		$this->setY( $this->y - $v_->y() );
	}
	
	
	// Returns a perpendicular vector with same magnitude
	public function norm()
	{
		$v = new Vector($this->x,$this->y);
		$v->rotate(pi()/2.0);
		return $v;
	}
	
	
	// Sum a vector with the current vector
	public function plus(Vector $v_)
	{
		$this->setX( $this->x + $v_->x() );
		$this->setY( $this->y + $v_->y() );
	}
	
	
	// Rotate vector clockwise by angle theta_ (in radians)
	public function rotate($theta_)
	{
		$x = $this->x*cos($theta_) - $this->y*sin($theta_);
		$y = $this->x*sin($theta_) + $this->y*cos($theta_);
		
		$this->setX($x);
		$this->setY($y);
	}
	
	
	// Multiply the vector by a factor s_
	public function scale($s_)
	{
		$this->setX( $this->x*$s_ );
		$this->setY( $this->y*$s_ );
	}
	
	
	// Scale the vector to have magnitude mag_
	public function setMag($mag_)
	{
		$s = $mag_ / $this->mag();
		$this->scale($s);
	}
	
	
	// Set x component
	public function setX($x_)
	{
		$this->x = $x_;
	}
	
	
	// Set y component
	public function setY($y_)
	{
		$this->y = $y_;
	}
	
	
	// Clockwise angle from current vector to supplied vector
	public function theta($v_)
	{
		$u = $this->unit();
		$v = $v_->unit();
		
		$cross = $u->cross($v);
		$dot = $u->dot($v);
		
		$theta = acos($dot);
		
		if($cross > 0.0)
		{
			$theta = $theta;
		}
		else
		{
			$theta = 2*pi() - $theta;
		}
		
		return $theta;
	}
	
	
	// Get unit vector in same direction
	public function unit()
	{
		$u_x = $this->x() / $this->mag();
		$u_y = $this->y() / $this->mag();
		$u = new Vector($u_x, $u_y);
		return $u;
	}
	
	
	// Get x component
	public function x()
	{
		return $this->x;
	}
	
	
	// Get y component
	public function y()
	{
		return $this->y;
	}
}

?>
