<?php namespace App\KiCad;

use SVGCreator\Element;
use SVGCreator\Elements\Svg;
use SVGCreator\Elements\Rect;

class EeschemaComponentPolyline extends EeschemaComponentObject
{
	public $ShapeType = EeschemaComponentObject::SHAPE_POLYLINE;

	public $count = 0;
	public $Unit = 0;
	public $convert = 0;
	public $width = '';
	public $points = array();
	private $fill;

	public function parse( $line )
	{
		$tmp = 0;
		$line = substr($line, 2);
		$i = sscanf($line, "%d %d %d %d",
			$this->count,
			$this->Unit,
			$this->convert,
			$this->width);

		$fill = self::SHAPE_FILL_NONE;

		if( $i < 4 )
		{
			throw new Exception("Invalid polyline specification");
		}

		if( $this->count <= 0 )
		{
			throw new Exception("Invalid number of polyline points");
		}
/*

		if( $tmp[0] == 'F' )
			$this->fill = self::SHAPE_FILLED;

		if( $tmp[0] = 'f' )
			$this->fill = self::SHAPE_FILLED_WITH_BG_BODYCOLOR;
			*/
	}

	public function draw( &$svg, &$component )
	{
	}
}
