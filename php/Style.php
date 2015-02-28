<?php
namespace fw;

/*
 * A selection of style properties and their values. References a parent
 * to enable styles to cascade.
 */

class Style
{
	//__________________________________________________________________
	// Properties
	
	// Array with properties as keys and values as elements
	private $list;
	
	// Variable pointing to Style's mother style
	private $mother;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($mother_ = NULL)
	{
		$this->list = array();
		$this->mother = $mother_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	// Returns the value of any style property_
	public function get($property_)
	{
		if( isset($this->list[$property_]) )
		{
			return $this->list[$property_];
		}
		else if( $this->mother == NULL )
		{
			trigger_error("Reached top-level style object without finding property $property_.", E_USER_WARNING);
			return NULL;
		}
		else
		{
			return $this->mother->get($property_);
		}
	}
	
	// Boolean indicating if the style has a parent
	public function hasMother()
	{
		if($this->mother == NULL)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// Load the FeynWeb Style (fws) file fname_
	public function load($fname_)
	{
		if( !file_exists($fname_) )
		{
			trigger_error("Style file $fname_ not found. Nothing loaded.", E_USER_ERROR);
			return false;
		}
		
		$file = fopen($fname_, "r");
		
		while(!feof($file))
		{
			$s = fgets($file);
			$property = strtok($s, ':');
			$value = rtrim( strtok(':') );
			if( ($property != "") && ($value != "") )
			{
				$this->list[$property] = $value;
			}
		}
		
		return true;
	}
	
	// Returns the parent style if one exists, otherwise returns null
	public function mother()
	{
		return $this->mother;
	}
	
	// Set any style property_ to value_
	public function set($property_, $value_)
	{
		$this->list[$property_] = $value_;
	}
	
	// Assign the parent style
	public function setMother($mother_)
	{
		$this->mother = $mother_;
	}
	
	// Synonym of set()
	public function setStyle($property_, $value_)
	{
		$this->set($property_, $value_);
	}
}
