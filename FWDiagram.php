<?php
namespace fw;

require_once("Diagram.php");
require_once("FeynWeb.php");

/*
 * The primary user class.
 * Parses FeynWeb XML input from either a file or a string (supplied at
 * construction or throgh load()) then outputs the SVG diagram either to
 * a file, as a string or echoed directly
 */

class FWDiagram
{
	//__________________________________________________________________
	// Properties
	
	// List of known particle tags
	private $allowed_particle_names = array('fermion', 'photon', 'boson', 'gluon');
	// Current active style
	private $current_style;
	// A string of the xml input
	private $data;
	// The fw\Diagram
	private $diagram;
	// Xml parser
	private $parser;
	// Array of all elements in current tree
	private $element_list;
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($input_ = NULL)
	{
		$this->element_list = array();
		if($input_ != NULL) $this->load($input_);
	}
	
	
	//__________________________________________________________________
	// Methods
	
	// Calls the diagram's draw method which directly echos the SVG
	public function draw()
	{
		$this->diagram->draw();
	}
	
	// General function for loading the FeynWeb XML
	// If input_ is an existing filename, then loadFile will be called
	// If not, loadString is called with input_ as the string
	public function load($input_)
	{
		if( file_exists($input_) )
		{
			$this->loadFile($input_);
		}
		else
		{
			$this->loadString($input_);
		}
		//$this->diagram->draw();
	}
	
	// Loads FeynWeb XML input from file fname_
	public function loadFile($fname_)
	{
		if(file_exists($fname_))
		{
			$this->data = file_get_contents($fname_);
			$this->parse();
		}
		else
		{
			trigger_error("File $fname_ cannot be found. No input loaded", E_USER_WARNING);
		}
	}
	
	// Loads FeynWeb XML input from string
	public function loadString($str_)
	{
		$this->data = $str_;
		$this->parse();
	}
	
	// Calls diagram's saveAs method which will save the SVG to a file
	// fname_. User can also optionally specify the file mode according
	// to the usual php fopen() modes. Defult mode is 'x'
	public function saveAs($fname_, $mode = "x")
	{
		$this->diagram->saveAs($fname_, $mode_);
	}
	
	// Calls diagram's str method, which returns the SVG as a string
	public function str()
	{
		return $this->diagram->str();
	}
	
	
	
	
	/*******************************************************************
	 * Utilities during parsing
	 ******************************************************************/
	
	
	
	private function addStyleAttributes($element_, $attr_)
	{
		foreach(FeynWeb::styleProperties() as $p)
		{
			if( isset($attr_[$p]) )
			{
				$element_->setStyle($p, $attr_[$p]);
			}
		}
	}
	
	// Returns boolean as to whether supplied tag is a known particle
	// type
	private function isParticle($tag_)
	{
		if( array_search($tag_, $this->allowed_particle_names) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	// Opens a new element as a child under the current one
	private function openElement($id_)
	{
		array_push($this->element_list, $id_);
	}
	
	
	// Closes the current element and returns to its parent
	private function closeElement()
	{
		array_pop($this->element_list);
	}
	
	
	// Returns the currently open element
	private function currentElement()
	{
		if( sizeof($this->element_list) == 0 )
		{
			return NULL;
		}
		else
		{
			$id = end($this->element_list);
			return $this->diagram->getElement($id);
		}
	}
	
	
	
	
	
	/*******************************************************************
	 * Parser
	 ******************************************************************/
	
	
	
	// Parser Control --------------------------------------------------
	
	
	// Main Parsing Function
	// - Creates the parser and sets it up
	// - then calls it on the feynweb info
	private function parse()
	{
		// intialise the parser
		$this->parser = xml_parser_create();
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		//xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_set_object($this->parser, $this);
		xml_set_character_data_handler($this->parser,"char");
		xml_set_element_handler($this->parser, "parse_start_tag", "parse_end_tag");
		xml_set_character_data_handler($this->parser,"parse_char");
		xml_parse($this->parser, $this->data, true);
		xml_parser_free($this->parser);
	}
	
	
	// Start Tag Parser
	// - Picks which START handler to use
	private function parse_start_tag($parser_, $tag_, $attr_)
	{
		if( get_class($this->currentElement()) == "fw\Label" )
		{
			$this->parse_label_addstart($tag_, $attr_);
		}
		else if($tag_ == "feynweb") $this->parse_feynweb_start($attr_);
		else if( $tag_ == "nodegroup") $this->parse_nodegroup_start($attr_);
		else if( ($tag_ == "vertex") || ($tag_ == "input") || ($tag_ == "output") )
		{
			if( get_class($this->currentElement()) == "fw\NodeGroup" )
			{
				$this->parse_nodegroup_node($attr_);
			}
			else
			{
				$this->parse_node_start($attr_);
			}
		}
		else if( ($tag_ == "fermion") || ($tag_ == "boson") || ($tag_ == "gluon") || ($tag_ == "photon") )
		{
			$this->parse_particle_start($tag_, $attr_);
		}
		else if($tag_ == "style") $this->parse_style_start($attr_);
		else if($tag_ == "label")
		{
			$this->parse_label_start($attr_);
		}
		else if($tag_ == "arrow")
		{
			$this->parse_arrow_start($attr_);
		}
		else
		{
			$this->parse_element_start($tag_, $attr_);
		}
	}
	
	
	// End Tag Parser
	// - Picks which END handler to use
	private function parse_end_tag($parser_, $tag_)
	{
		if($tag_ == "label")
		{
			$this->parse_label_end();
		}
		else if( get_class($this->currentElement()) == "fw\Label" )
		{
			$this->parse_label_addend($tag_);
		}
		else if($tag_ == "style")
		{
			$this->parse_style_end();
		}
		else if( $this->isParticle($tag_) )
		{
			$this->parse_particle_end();
		}
		else
		{
			$this->parse_element_close();
		}
	}
	
	
	
	// Character Data Parser
	// - Only does anything if inside a label
	private function parse_char($parser_, $data_)
	{
		if( get_class($this->currentElement()) == 'fw\Label') $this->parse_label_char($data_);
	}
	
	
	
	// Generic Handlers ------------------------------------------------
	
	
	// Start element
	private function parse_element_start($tag_, $attr_)
	{
		trigger_error("Unknown tag ($tag_) found. Tag ignored.", E_USER_NOTICE);
	}
	
	// End element
	private function parse_element_close()
	{
		$this->closeElement();
	}
	
	
	
	// Diagram ---------------------------------------------------------
	
	
	private function parse_feynweb_start($attr_)
	{
		// Check for required attributes
		
		if( !isset($attr_["height"]) )
		{
			trigger_error("Diagram does not have required 'height' attribute.", E_USER_ERROR);
		}
		
		if( !isset($attr_["width"]) )
		{
			trigger_error("Diagram does not have required 'width' attribute.", E_USER_ERROR);
		}
		
		if( !isset($attr_["stylesheet"]))
		{
			trigger_error("Diagram does not have required 'stylesheet' attribute.", E_USER_ERROR);
		}
		
		// Create the diagram
		$this->diagram = new Diagram($attr_["height"], $attr_["width"], $attr_["stylesheet"]);
		$this->current_style = $this->diagram->getStyleTop();
	}
	
	
	
	// Node ------------------------------------------------------------
	
	
	// Start Node
	private function parse_node_start($attr_)
	{
		// Attempt to get 'id' attribute or, if it's not set, generate one
		if( !isset($attr_["id"]) ) $attr_["id"] = "node" . $this->diagram->getElementN();
		
		// Check for required attributes: 'x' and 'y'
		if( !isset($attr_["x"]) )
		{
			trigger_error("Node " . $attr_["id"] . " is without required 'x' attribute.", E_USER_ERROR);
		}
		if( !isset($attr_["y"]) )
		{
			trigger_error("Node is without required 'y' attribute.", E_USER_ERROR);
		}
		
		// Create node
		$node = new Node($attr_["id"], $this->current_style, $attr_["x"], $attr_["y"]);
		
		// Check for optional attributes
		if( isset( $attr_["type"]) )				$node->setType($attr_["type"]);
		
		// Retrieve style attributes
		$this->addStyleAttributes($node, $attr_);
		
		// Include element in diagram
		$this->diagram->addElement($node);
		$this->openElement($node->id());
	}
	
	// End Node
	private function parse_node_end()
	{
		$this->closeElement();
	}
	
	
	
	// Node Group ------------------------------------------------------
	
	
	// Start NodeGroup
	private function parse_nodegroup_start($attr_)
	{
		// Attempt to get 'id' attribute or, if it's not set, generate one
		if( !isset($attr_["id"]) )
		{
			$attr_["id"] = "node" . $this->diagram->getElementN();
		}
		
		// Check for required attributes: 'x' and 'y'
		if( !isset($attr_["x"]) )
		{
			trigger_error("Node Group is without required 'x' attribute.", E_USER_ERROR);
		}
		if( !isset($attr_["y"]) )
		{
			trigger_error("Node Group is without required 'y' attribute.", E_USER_ERROR);
		}
		
		// Create NodeGroup
		$ng = new NodeGroup($attr_["id"], $this->current_style, $attr_["x"], $attr_["y"]);
		
		// Check for optional arguments
		if( isset($attr_["rotation"]) )
		{
			$ng->setRotation($attr_["rotation"]);
		}
		if( isset($attr_["type"]) )
		{
			$ng->setType($attr_["type"]);
		}
		
		// Retrieve style attributes
		$this->addStyleAttributes($ng, $attr_);
		
		// Include NodeGroup in diagram
		$this->diagram->addElement($ng);
		$this->openElement($ng->id());
	}
	
	
	// Add Node to NodeGroup
	private function parse_nodegroup_node($attr_)
	{
		// Attempt to get 'id' attribute or, if it's not set, generate one
		if( !isset($attr_["id"]) )
		{
			$attr_["id"] = "node" . $this->diagram->getElementN();
		}
		
		// Create node
		$node = new Node($attr_["id"], $this->current_style, 0.0, 0.0);
		
		// Check for optional arguments
		if( isset($attr_["type"]) )		$node->setType($attr_["type"]);
		
		// Retrieve style attributes
		$this->addStyleAttributes($node, $attr_);
		
		// Include Node in diagram
		$this->currentElement()->addNode($node);
		$this->openElement($node->id());
	}
	
	
	
	// Particles -------------------------------------------------------
	
	
	// Start Particle
	private function parse_particle_start($tag_, $attr_)
	{
		// Attempt to get 'id' attribute or, if it's not set, generate one
		if( !isset($attr_["id"]) )
		{
			$attr_["id"] = "particle" . $this->diagram->getElementN();
		}
		
		// Attempt to get 'r' attribute
		if( !isset($attr_["r"]) )
		{
			$attr_["r"] = 0.0;
		}
		
		// Check for required attributes: 'start' and 'end'
		if( !isset($attr_["start"]) )
		{
			trigger_error("Particle is without required 'start' attribute.", E_USER_ERROR);
		}
		if( !isset($attr_["end"]) )
		{
			trigger_error("Particle is without required 'end' attribute.", E_USER_ERROR);
		}
		
		// Get start and end points
		$start = $this->diagram->getElement($attr_["start"])->point();
		$end = $this->diagram->getElement($attr_["end"])->point();
		
		// Call particle specific handling function
		switch($tag_)
		{
			case "fermion":
				$particle = $this->parse_particle_fermion($attr_, $start, $end);
				break;
			case "gluon":
				$particle = $this->parse_particle_gluon($attr_, $start, $end);
				break;
			case "photon":
				$particle = $this->parse_particle_photon($attr_, $start, $end);
				break;
			case "boson":
				$particle = $this->parse_particle_boson($attr_, $start, $end);
				break;
			default:
				trigger_error("Unknown particle type ($tag_)", E_USER_ERROR);
				break;
		}
		
		// Check for optional attributes
		if( isset($attr_["clockwise"]) && ($attr_["r"] != 0.0) ) $particle->path()->setClockwise($attr_["clockwise"]);
		
		// Retrieve style attributes
		$this->addStyleAttributes($particle, $attr_);
		
		// Include particle in diagram
		$this->diagram->addElement($particle);
		$this->openElement($particle->id());
	}
	
	
	// Start Particle - Fermion
	private function parse_particle_fermion($attr_, Point $start_, Point $end_)
	{
		$fermion = new Fermion($attr_["id"], $this->current_style, $start_, $end_, $attr_["r"]);
		
		return $fermion;
	}
	
	
	// Start Particle - Photon
	private function parse_particle_photon($attr_, Point $start_, Point $end_)
	{
		// Create Photon
		$photon = new Photon($attr_["id"], $this->current_style, $start_, $end_, $attr_["r"]);
		
		return $photon;
	}
	
	
	// Start Particle - Gluon
	private function parse_particle_gluon($attr_, Point $start_, Point $end_)
	{
		$gluon = new Gluon($attr_["id"], $this->current_style, $start_, $end_, $attr_["r"]);
		
		// Check for optional attributes
		if( isset($attr_["spincw"]) )			$gluon->setSpinCW($attr_["spincw"]);
		
		return $gluon;
	}
	
	
	// Start Particle - Boson
	private function parse_particle_boson($attr_, Point $start_, Point $end_)
	{
		// Create boson
		$boson = new Boson($attr_["id"], $this->current_style, $start_, $end_, $attr_["r"]);
		
		return $boson;
	}
	
	
	// End Particle
	private function parse_particle_end()
	{
		$this->closeElement();
	}
	
	
	
	// Style -----------------------------------------------------------
	
	
	// Start Style
	private function parse_style_start($attr_)
	{
		// Create new style
		$style = new Style($this->current_style);
		
		// Include new style in diagram
		$this->diagram->addStyle($style);
		$this->current_style = $style;
		
		// Retrieve style attributes
		$this->addStyleAttributes($style, $attr_);
	}
	
	
	// End Style
	private function parse_style_end()
	{
		$this->current_style = $this->current_style->mother();
	}
	
	
	
	// Label -----------------------------------------------------------
	
	
	// Start Label
	private function parse_label_start($attr_)
	{
		// Get element to which label must be applied
		$c = $this->currentElement();
		
		// Create label
		$c->addLabel("");
		
		// Check for optional attributes
		if( isset($attr_["position"]) )
		{
			$c->label()->setPosition($attr_["position"]);
		}
		if( isset($attr_["r"]) )
		{
			$c->label()->setRadius($attr_["r"]);
		}
		if( isset($attr_["offset-x"]) )
		{
			$c->label()->setOffsetX($attr_["offset-x"]);
		}
		if( isset($attr_["offset-y"]) )
		{
			$c->label()->setOffsetY($attr_["offset-y"]);
		}
		
		// Retrieve style attributes
		$this->addStyleAttributes($c->label(), $attr_);
		
		// Include label in diagram
		$this->openElement( $c->label()->id() );
	}
	
	
	// End Label
	private function parse_label_end()
	{
		$this->closeElement();
	}
	
	
	// Label Data - Character
	private function parse_label_char($data_)
	{
		$this->currentElement()->addText($data_);
	}
	
	
	// Label Data - Entity
	private function parse_label_entity($data_)
	{
		$this->currentElement()->addText($data_);
	}
	
	
	// Label Data - Tag Start
	private function parse_label_addstart($tag_, $attr_)
	{
		$text = '<' . $tag_;
		foreach($attr_ as $k => $v)
		{
			$text .= ' ' . $k . '="' . $v . '"';
		}
		$text .= '>';
		$this->currentElement()->addText($text);
	}
	
	
	// Label Data - Tag End
	private function parse_label_addend($tag)
	{
		$text = '</' . $tag . '>';
		$this->currentElement()->addText($text);
	}
	
	
	
	// Arrow -----------------------------------------------------------
	
	
	// Start Arrow
	private function parse_arrow_start($attr_)
	{
		// Get element to which arrow must be applied
		$c = $this->currentElement();
		
		// Create arrow
		$c->addArrow();
		
		// Check for optional attributes
		if( isset($attr_["position"]) )
		{
			$c->arrow()->setPosition($attr_["position"]);
		}
		if( isset($attr_["r"]) )
		{
			$c->arrow()->setRadius($attr_["r"]);
		}
		if( isset($attr_["offset-x"]) )
		{
			$c->arrow()->setOffsetX($attr_["offset-x"]);
		}
		if( isset($attr_["offset-y"]) )
		{
			$c->arrow()->setOffsetY($attr_["offset-y"]);
		}
		if( isset($attr_["sense"]) )
		{
			$c->arrow()->setSense($attr_["sense"]);
		}
		
		// Retrieve style attributes
		$this->addStyleAttributes($c->arrow(), $attr_);
		
		// Include arrow in diagram
		$this->openElement( $c->arrow()->id() );
	}
}
?>
