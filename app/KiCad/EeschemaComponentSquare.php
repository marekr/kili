<?php namespace App\KiCad;

use SVGCreator\Element;
use SVGCreator\Elements\Svg;
use SVGCreator\Elements\Rect;

class EeschemaComponentSquare extends EeschemaComponentObject
{
	public $ShapeType = EeschemaComponentObject::SHAPE_SQUARE;

	public $PositionX = 0;
	public $PositionY = 0;
	public $EndX = 0;
	public $EndY = 0;
	public $Unit = 0;
	public $Convert = 0;
	public $Width = 0;
	public $Type = '';

	public function parse( $line )
	{
		$line = substr($line, 2);
		list($this->PositionX, $this->PositionY, $this->EndX, $this->EndY, $this->Unit, $this->Convert, $this->Width, $this->Type) = sscanf($line, "%d %d %d %d %d %d %d %255s");
	}

	public function width()
	{
		return abs($this->PositionX) + abs($this->EndX);
	}

	public function height()
	{
		return abs($this->PositionY) + abs($this->EndY);
	}
}
