<?php
namespace fw;

require_once("Element.php");
require_once("Node.php");
require_once("Point.php");
require_once("Style.php");
require_once("Vector.php");

/*
 * A container of nodes which spaces them equally along a straigth line
 */

class NodeGroup extends Element
{
	//__________________________________________________________________
	// Properties
	
	private $label;
	private $point;
	private $node_list;
	private $rotation;
	private $type;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	public function __construct($id_, Style $mother_, $x_ = 0.0, $y_ = 0.0, $rotation_ = 0.0, $type_ = "oval")
	{
		$this->id = $id_;
		$this->label = "unset";
		$this->node_list = array();
		$this->point = new Point($x_, $y_);
		$this->rotation = $rotation_;
		$this->style = new Style($mother_);
		$this->type = $type_;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	public function addLabel($string_, $position_ = "w", $radius_ = 20.0, $offset_x_ = 0.0, $offset_y_ = 0.0)
	{
		$id = $this->id . '-label';
		
		$this->label = new Label($id, $this->style, $string_, $this->point, $position_, $radius_, $offset_x_, $offset_y_);
	}
	
	public function addNode(Node $n_)
	{
		array_push($this->node_list, $n_);
		$this->updateNodes();
	}
	
	public function getElement($id_)
	{
		if( $this->label() != NULL )
		{
			if($this->label()->id() == $id_) return $this->label();
		}
		foreach($this->node_list as $n)
		{
			if($n->id() == $id_) return $n;
		}
		foreach($this->node_list as $n)
		{
			if($n->getElement($id_) != NULL) return $n->getElement($id_);
		}
		return NULL;
	}
	
	public function getPoint()
	{
		return $this->point;
	}
	
	public function getRotation()
	{
		return $this->rotation;
	}
	
	public function label()
	{
		if($this->label == "unset")
		{
			return NULL;
		}
		else
		{
			return $this->label;
		}
	}
	
	public function point()
	{
		return $this->getPoint();
	}
	
	public function setPoint(Point $p_)
	{
		$this->point = $p_;
	}
	
	public function setRotation($rotation_)
	{
		$this->rotation = $rotation_;
		$this->updateNodes();
	}
	
	
	// Convenience function for setting node separation
	public function setSeparation($separation_)
	{
		$this->style->set("nodegroup-separation", $separation_);
		$this->updateNodes();
	}
	
	
	public function setType($type_)
	{
		$this->type = $type_;
	}
	
	
	public function setX($x_)
	{
		$this->point()->setX($x_);
		$this->updateNodes();
	}
	
	
	public function setY($y_)
	{
		$this->point()->setY($y_);
		$this->updateNodes();
	}
	
	
	public function svg()
	{
		$svg = '<ellipse' . "\n\t";
		$svg .= ' cx="' . $this->point->x() . '" cy="' . $this->point->y() . '"' . "\n\t";
		
		$ry = sizeof($this->node_list) / 2.0;
		$ry *= $this->style->get("nodegroup-separation");
		$svg .= ' ry="' . $ry . '"' . "\n\t";
		
		$svg .= ' rx="' . ($this->style->get("nodegroup-separation")/2.0) . '"' . "\n\t";
		$svg .= ' stroke="' . $this->style->get("line-color") . '" stroke-width="' . $this->style->get("line-width") . '"' . "\n\t";
		$svg .= ' fill="' . $this->style->get("diagram-background") . '" transform="rotate(' . $this->rotation . ' ' . $this->point->x() . ' ' . $this->point->y() . ')" />' . "\n";
		
		foreach($this->node_list as $n)
		{
			$svg .= $n->svg();
		}
		
		if($this->label != "unset") $svg .= $this->label->svg();
		
		return $svg;
	}
	
	
	private function updateNodes()
	{
		$n = sizeof($this->node_list);
		$d = $this->style->get("nodegroup-separation");
		
		$width = $n*$d;
		
		// Vector between two node's positions
		$diff = new Vector(0.0, 1.0);
		$diff->scale($d);
		$diff->rotate($this->rotation);
		
		// Vector from centre to start
		$c2s = new Vector(0.0, -1.0);
		$c2s->scale( (($n-1)*$d)/2.0 );
		$c2s->rotate($this->rotation);
		
		// Position of the current node
		$p = new Point($this->point->x(), $this->point->y());
		$p->translate($c2s);
		
		foreach($this->node_list as $node)
		{
			$node->setX($p->x());
			$node->setY($p->y());
			
			$p->translate($diff);
		}
	}
}
