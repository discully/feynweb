<?php
namespace fw;

require_once("Boson.php");
require_once("Fermion.php");
require_once("Gluon.php");
require_once("Label.php");
require_once("Node.php");
require_once("NodeGroup.php");
require_once("Photon.php");
require_once("Style.php");

/*
 * A FeynWeb Diagram. Diagrams are created by adding elements to this
 * class, then manipulating their properties. The resulting svg can be
 * output to a file, a string or directly echoed
 */

class Diagram
{
	//__________________________________________________________________
	// Properties
	
	private $element_list;
	private $height;
	private $stylesheet;
	private $style_list;
	private $width;
	
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($height_, $width_, $stylesheet_ = "feynweb/style_default.fsv", $input_ = "NOTSET")
	{
		$this->element_list = array();
		
		$this->height = $height_;
		$this->stylesheet = $stylesheet_;
		$this->width = $width_;
		
		$this->style_list[0] = new Style();
		$this->style_list[0]->load($stylesheet_);
		$this->current_style = $this->style_list[0];
		
		$this->current_element = NULL;
		
		if($input_ != "NOTSET")
		{
			if(file_exists($input_))
			{
				$this->loadFile($input_);
			}
			else
			{
				$this->loadStr($input_);
			}
		}
	}
	
	
	//__________________________________________________________________
	// Public Methods
	
	public function loadFile($fname_)
	{
		$this->data = file_get_contents($fname_);
		$this->parse();
	}
	
	public function loadStr($str_)
	{
		$this->data = $str_;
		$this->parse();
	}
	
	public function addElement(Element $e_)
	{
		$id = $e_->id();
		
		if( isset($this->element_list[$id]) )
		{
			trigger_error("Element with specified ID already exists ($id). No element created.", E_USER_WARNING);
		}
		else
		{
			$this->element_list[$id] = $e_;
		}
	}
	
	public function addStyle(Style $s_)
	{
		$i = sizeof($this->style_list);
		$this->style_list[$i] = $s_;
	}
	
	public function draw()
	{
		echo $this->svg();
	}
	
	public function getElement($id_)
	{
		if( isset($this->element_list[$id_]) )
		{
			return $this->element_list[$id_];
		}
		else
		{
			foreach($this->element_list as $e)
			{
				if( $e->getElement($id_) != NULL )
				{
					return $e->getElement($id_);
				}
			}
			trigger_error("Requested element not found (" . $id_ . ")", E_USER_WARNING);
			return NULL;
		}
	}
	
	public function getElementN()
	{
		return sizeof($this->element_list);
	}
	
	public function getHeight()
	{
		return $this->height;
	}
	
	public function getStylesheet()
	{
		return $this->stylesheet;
	}
	
	// Return the mother of all styles
	public function getStyleTop()
	{
		return $this->style_list[0];
	}
	
	public function getWidth()
	{
		return $this->width;
	}
	
	public function saveAs($fname_, $mode_ = "x")
	{
		$file = fopen($fname_, $mode_);
		
		if(!$file)
		{
			trigger_error("Error occured attempting to open file ($fname_)", E_USER_ERROR);
		}
		
		$svg = '<?xml version="1.0"?>';
		$svg .= $this->svg();
		
		fwrite($file, $svg);
		
		fclose($file);
	}
	
	public function setHeight($h_)
	{
		$this->height = $h_;
	}
	
	public function setWidth($w_)
	{
		$this->width = $w_;
	}
	
	public function str()
	{
		return $this->svg();
	}
	
	private function svg()
	{
		$svg = "";
		
		$svg .= '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" height="' . $this->height . '" width="' . $this->width . '" >' . "\n";
		
		$svg .= '<defs>' . "\n";
		$svg .= '<pattern id="blobDiag" patternUnits="userSpaceOnUse"';
		$svg .= ' x="0" y="0" width="5" height="5" viewBox="0 0 5 5" fill="transparent" >' . "\n\t";
		$svg .= '<line x1="-5" x2="10" y1="10" y2="-5" stroke="black" stroke-width="2" />' . "\n\t";
		$svg .= '<line x1="-5" x2="10" y1="15" y2="0" stroke="black" stroke-width="2" />' . "\n\t";
		$svg .= '<line x1="-5" x2="10" y1="5" y2="-10" stroke="black" stroke-width="2" />' . "\n";
		$svg .= '</pattern>' . "\n";
		$svg .= '<pattern id="blobCross" patternUnits="userSpaceOnUse"';
		$svg .= ' x="0" y="0" width="5" height="5" viewBox="0 0 5 5" fill="transparent" >' . "\n\t";
		$svg .= '<line x1="-5" x2="10" y1="10" y2="-05" stroke="black" stroke-width="1" />' . "\n\t";
		$svg .= '<line x1="-5" x2="10" y1="15" y2="000" stroke="black" stroke-width="1" />' . "\n\t";
		$svg .= '<line x1="-5" x2="10" y1="05" y2="-10" stroke="black" stroke-width="1" />' . "\n\t";
		$svg .= '<line x1="10" x2="-5" y1="10" y2="-05" stroke="black" stroke-width="1" />' . "\n\t";
		$svg .= '<line x1="10" x2="-5" y1="15" y2="000" stroke="black" stroke-width="1" />' . "\n\t";
		$svg .= '<line x1="10" x2="-5" y1="05" y2="-10" stroke="black" stroke-width="1" />' . "\n";
		$svg .= '</pattern>' . "\n";
		$svg .= '</defs>' . "\n\n";
		
		// frame
		$border_width = $this->style_list[0]->get("diagram-border-width");
		$svg .= '<rect';
		$svg .= ' x="' . ($border_width/2.0) . '" y="' . ($border_width/2.0) . '"';
		$svg .= ' height="' . ($this->height - $border_width) . '" width="' . ($this->width - $border_width) . '"';
		$svg .= ' fill="' . $this->style_list[0]->get("diagram-background") . '"';
		$svg .= ' stroke="' . $this->style_list[0]->get("diagram-border-color") . '"';
		$svg .= ' stroke-width="' . $border_width . '" />' . "\n";
		
		foreach($this->element_list as $e)
		{
			if( (get_class($e) != 'fw\Node') && (get_class($e) != 'fw\NodeGroup') ) $svg .= $e->svg() . "\n\n";
		}
		
		foreach($this->element_list as $e)
		{
			if( (get_class($e) == 'fw\Node') || (get_class($e) == 'fw\NodeGroup') ) $svg .= $e->svg() . "\n\n";
		}
		
		$svg .= "</svg>\n";
		
		return $svg;
	}
}
?>
