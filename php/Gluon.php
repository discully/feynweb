<?php
namespace fw;

require_once("Arc.php");
require_once("Line.php");
require_once("Particle.php");
require_once("Point.php");

/*
 * Particle represented by a curly line
 */

class Gluon extends Particle
{
	//__________________________________________________________________
	// Properties
	
	private $spin_cw;
	
	
	
	//__________________________________________________________________
	// Constructor
	
	protected function init()
	{
		$this->spin_cw = true;
	}
	
	
	
	//__________________________________________________________________
	// Methods
	
	
	public function setAmplitude($a_)
	{
		$this->style->set("gluon-amplitude", $a_);
	}
	
	public function setDelta($d_)
	{
		$this->style->set("gluon-delta", $d_);
	}
	
	public function setSpinCW($s_)
	{
		$this->spin_cw = $s_;
	}
	
	public function setTheta($t_)
	{
		$this->style->set("gluon-theta", $t_);
	}
	
	public function setWavelength($wl_)
	{
		$this->style->set("gluon-wavelength", $wl_);
	}
	
	
	public function svg()
	{
		$svg = '<path id="' . $this->id . '" ';
		
		$svg .= $this->svg_path();
		
		$svg .= ' stroke="' . $this->style->get("line-color") . '"';
		$svg .= ' stroke-width="' . $this->style->get("line-width") . '"';
		if($this->style->get("line-dash-array") != 0)
		{
			$svg .= ' stroke-dasharray="' . $this->style->get("line-dash-array") . '"';
		}
		$svg .= ' fill="none" />';
		
		if($this->arrow != "unset") $svg .= "\n" . $this->arrow->svg();
		if($this->label != "unset") $svg .= "\n" . $this->label->svg();
		
		return $svg;
	}
	
	
	public function svg_path()
	{
		// Get style values
		$amplitude = $this->style->get("gluon-amplitude");
		$wl = $this->style->get("gluon-wavelength");
		$delta = $this->style->get("gluon-delta");
		$theta = $this->style->get("gluon-theta");
		
		$s = $this->path->start();
		$sn = $this->path->normalAt(0.0);
		
		// Make sure the line leaves the node at the correct angle to 
		// match the spin
		if($this->path->isArc())
		{
			if( ($this->path->isClockwise() && $this->spin_cw) || (!$this->path->isClockwise() && !$this->spin_cw) )
				{$sn->scale($amplitude);}
			else
				{$sn->scale(-$amplitude);}
		}
		else
		{
			if($this->spin_cw)
				{$sn->scale(-$amplitude);}
			else
				{$sn->scale($amplitude);}
		}
			
		// get number of draw points the line is split into
		$l = $this->path->length();
		$n = round( $l / $wl );
		
		$svg = 'd=" M ' . round($s->x(),2) . ' ' . round($s->y(),2) . "\n";
		
		for($i = 1; $i <= $n; ++$i)
		{
			$pos = $l * ($i / $n);
			
			if($i != $n)
			{
				$pos1 = $pos + $delta;
				$pos2 = $pos - $delta;
			}
			else
			{
				$pos1 = $pos;
				$pos2 = $pos;
			}
			
			$norm1 = $this->path->normalAt($pos1);
			$norm2 = $this->path->normalAt($pos2);
			
			
			// Make sure loops have correct spin
			if( ($this->path->isClockwise() && !$this->spin_cw) || (!$this->path->isClockwise() && $this->spin_cw) )
			{
				$norm1->scale(-1.0);
				$norm2->scale(-1.0);
			}
			else if($this->path->isLine() && $this->spin_cw)
			{
				$norm1->scale(-1.0);
				$norm2->scale(-1.0);
			}
			
			$norm1->setMag($amplitude);
			$norm2->setMag($amplitude);
			
			$node1 = $this->path->positionAt($pos1);
			$node2 = $this->path->positionAt($pos2);
			
			// Rotate normal to create smooth loops
			// Direction determined by clockwise/anti-clockwise and spin
			if($this->path->isArc())
			{
				if( ($this->path->isClockwise() && $this->spin_cw) || (!$this->path->isClockwise() && $this->spin_cw) )
				{
					$norm1->rotate(deg2rad(-$theta));
					$norm2->rotate(deg2rad($theta));
				}
				else
				{
					$norm1->rotate(deg2rad($theta));
					$norm2->rotate(deg2rad(-$theta));
				}
			}
			else
			{
				
				if(!$this->spin_cw)
				{
					$norm1->rotate(deg2rad($theta));
					$norm2->rotate(deg2rad(-$theta));
				}
				else
				{
					$norm1->rotate(deg2rad(-$theta));
					$norm2->rotate(deg2rad($theta));
				}
			}
			
			if($i == 1)
			{
				$svg .= 'C ' . round($s->x()+$sn->x(),2) . ',' . round($s->y()+$sn->y(),2) . ' ' . round($node1->x()+$norm1->x(),2) . ',' . round($node1->y()+$norm1->y(),2) . ' ' . round($node1->x(),2) . ',' . round($node1->y(),2) . "\n";
			}
			else
			{
				$svg .= 'S ' . round($node1->x()+$norm1->x(),2) . ',' . round($node1->y()+$norm1->y(),2) . ' ' . round($node1->x(),2) . ',' . round($node1->y(),2) . "\n";
			}
			
			if($i != $n) $svg .= 'S ' . round($node2->x()-$norm2->x(),2) . ',' . round($node2->y()-$norm2->y(),2) . ' ' . round($node2->x(),2) . ',' . round($node2->y(),2) . "\n";
		}
		
		$svg .= '"';
		
		return $svg;
	}
	
}

?>
