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

		$tok = strtok($line, " \n\t");
		$tok = strtok(" \n\t");
		$tok = strtok(" \n\t");
		$tok = strtok(" \n\t");

		$x = 0;
		$y = 0;
		for ($i = 0; $i < $this->count; $i++)
		{
			$x = strtok(" \n\t");
			if( $x === false )
				throw new Exception("Missing X");

		    $y = strtok(" \n\t");
			if( $y === false )
				throw new Exception("Missing X");

			$this->points[] = array($x, $y);
		}

		$last = strtok(" \n\t");
		if( $last == 'F' )
			$this->fill = self::SHAPE_FILLED;

		if( $last = 'f' )
			$this->fill = self::SHAPE_FILLED_WITH_BG_BODYCOLOR;

	}

	public function getBoundingBox()
	{
		$result = array(
			'minX' => 0,
			'minY' => 0,
			'maxX' => 0,
			'maxY' => 0
		);

		foreach($this->points as $set)
		{
			$result['minX'] = min($result['minX'], $set[0]);
			$result['maxX'] = max($result['maxX'], $set[0]);
			$result['minY'] = min($result['minY'], $set[1]);
			$result['maxY'] = max($result['maxY'], $set[1]);
		}

		return $result;
	}

	public function draw( &$svg, &$component )
	{
		$points = array();
		foreach($this->points as $set)
		{
			$points[] = $component->transX($set[0]).','.$component->transY($set[1]);
		}
		$points = implode(' ', $points);

		$svg->append(\SVGCreator\Element::POLYGON)
			->attr('fill', 'none')
			->attr('stroke', '#000000')
			->attr('points', $points);
	}
}
