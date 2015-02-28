<?php
namespace fw;

/*
 * A general point in 2D
 */
 
class Point
{
	//__________________________________________________________________
	// Properties
	
	// x coordinates
	private $x;
	// y coordinate
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
	
	
	// Get distance from another point
	public function dist(Point $n_)
	{
		$dx = $this->x() - $n_->x();
		$dy = $this->y() - $n_->y();
		$d = sqrt( $dx*$dx + $dy*$dy );
		return $d;
	}
	
	// Set x coordinate
	public function setX($x_)
	{
		$this->x = $x_;
		return $this->x();
	}
	
	// Set y coordinate
	public function setY($y_)
	{
		$this->y = $y_;
		return $this->y();
	}
	
	// Translate the node acording to the vector supplied, multiplied by
	// an optional scaling factor
	public function translate(Vector $v_, $factor_ = 1.0)
	{
		$this->setX( $this->x() + ($v_->x()*$factor_) );
		$this->setY( $this->y() + ($v_->y()*$factor_) );
	}
	
	// Get x coordinate
	public function x()
	{
		return $this->x;
	}
	
	// Get y coordinate
	public function y()
	{
		return $this->y;
	}
}

?>
