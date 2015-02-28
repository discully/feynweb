<?php
namespace fw;

/*
 * An abstract class for paths such as Arcs or Lines
 */

abstract class Path
{
	//__________________________________________________________________
	// Properties
	
	
	
	//__________________________________________________________________
	// Methods
	
	// Return a node at the end point of the path
	abstract public function end();
	
	// If path is an arc returns true, else returns false
	abstract public function isArc();
	
	abstract public function isClockwise();
	
	// If path is a line return true, else return false
	abstract public function isLine();
	
	// Returns the length along the path
	abstract public function length();
	
	// Returns a vector normal to the line at the distance specified
	abstract public function normalAt($length_);
	
	// Returns a node at the distance along the line specified
	abstract public function positionAt($length_);
	
	// Set the end position to the supplied node
	abstract public function setEnd(Point $n_);
	
	// Set the start position to the supplied node
	abstract public function setStart(Point $n_);
	
	// Returns a node at the start point of the path
	abstract public function start();
	
	// Returns a vector parallel along the line at the distance specified
	abstract public function vectorAt($length_);
}
?>
