<?php
namespace fw;

class FeynWeb
{
	public static $version = 0.1;
	
	private static $style_properties = array(
		'blob-border-width',
		'blob-border-color',
		'blob-fill',
		'blob-pattern',
		'blob-radius',
		'boson-dash-array',
		'diagram-background',
		'diagram-border-color',
		'diagram-border-width',
		'dot-radius',
		'dot-fill',
		'dot-border-width',
		'dot-border-color',
		'font-family',
		'font-size',
		'font-style',
		'font-weight',
		'gluon-wavelength',
		'gluon-amplitude',
		'gluon-delta',
		'gluon-theta',
		'line-color',
		'line-dash-array',
		'line-width',
		'nodegroup-separation',
		'photon-wavelength',
		'photon-amplitude',
		'text-decoration'
		);
	
	public static function styleProperties()
	{
		return self::$style_properties;
	}
}
?>
