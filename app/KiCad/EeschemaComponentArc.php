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
	private $fill;

	public function parse( $line )
	{
		$tmp = 0;
		$line = substr($line, 2);
		$i = sscanf($line, "%d %d %d %d %d %d %d %d %255s %d %d %d %d",
					$this->PositionX,
					$this->PositionY,
					$this->Radius,
					$this->t1,
					$this->t2,
					$this->Unit,
					$this->Convert,
					$this->Width,
					$tmp,
					$this->StartX,
					$this->StartY,
					$this->EndX,
					$this->EndY
				);

		if( $tmp[0] == 'F' )
			$this->fill = self::SHAPE_FILLED;

		if( $tmp[0] = 'f' )
			$this->fill = self::SHAPE_FILLED_WITH_BG_BODYCOLOR;

		$this->t1 = $this->normalize_angle($this->t1)/10;
		$this->t2 = $this->normalize_angle($this->t2)/10;
	}

	private function normalize_angle($angle)
	{
		while( $angle < 0 )
		{
			$angle += 3600;
		}

		while( $angle >= 3600 )
		{
			$angle -= 3600;
		}

		return $angle;
	}

	public function getBoundingBox()
	{
		$result = array(
			'minX' => min($this->StartX,$this->EndX),
			'minY' => min($this->StartY,$this->EndY),
			'maxX' => max($this->StartX,$this->EndX),
			'maxY' => max($this->StartY,$this->EndY)
		);

		return $result;
	}

	public function draw( &$svg, &$component )
	{
		$d = 'M '.$component->transX($this->StartX).','.$component->transY($this->StartY);
		$d .= ' ';
		$d .= 'A '.$this->t1.','.$this->Radius;
		$d .= ' ';
		$d .= '0 0 1';
		$d .= ' ';
		$d .= $component->transX($this->EndX).','.$component->transY($this->EndY);
		$svg->append(\SVGCreator\Element::PATH)
			->attr('d', $d)
			->attr('stroke', '#000000')
			->attr('fill', 'none');
	}
}
