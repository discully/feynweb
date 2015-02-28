<?php
namespace fw;

require_once("Style.php");
require_once("Point.php");

/*
 * Abstract class for drawable elements, eg: nodes, particles
 */
 
abstract class Element
{
	//__________________________________________________________________
	// Properties
	
	// Unique id of the element
	protected $id;
	// Style holding properties of the element
	protected $style;
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	// Abstract --------------------------------------------------------
	
	// Search the element and its attached annotations for the id given
	abstract public function getElement($id_);
	
	// Return a string of the svg representation
	abstract public function svg();
	
	
	// Implemented -----------------------------------------------------
	
	// Get the value of any style property
	public function getStyle($property_)
	{
		return $this->style->get($property_);
	}
	
	// Returns the element's id
	public function id()
	{
		return $this->id;
	}
	
	// Set the value of any style property
	public function setStyle($property_, $value_)
	{
		$this->style->set($property_, $value_);
	}
	
	// Returns the element's style
	public function style()
	{
		return $this->style;
	}
}
