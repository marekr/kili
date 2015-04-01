<?php namespace App\KiCad;

use SVGCreator\Element;
use SVGCreator\Elements\Svg;
use SVGCreator\Elements\Rect;

class EeschemaComponentArc extends EeschemaComponentObject
{
	public $ShapeType = EeschemaComponentObject::SHAPE_ARC;

	public $PositionX = 0;
	public $PositionY = 0;
	public $Radius = 0;
	public $t1 = 0;
	public $t2 = 0;
	public $Unit = 0;
	public $Convert = 0;
	public $Width = '';
	public $StartX = 0;
	public $StartY = 0;
	public $EndX = 0;
	public $EndY = 0;

	public function parse( $line )
	{
	}
}
