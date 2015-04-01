<?php namespace App\KiCad;

use SVGCreator\Element;
use SVGCreator\Elements\Svg;
use SVGCreator\Elements\Rect;

class EeschemaComponentPinType
{
	const INPUT = 'I';
	const OUTPUT = 'O';
	const BIDIRECTIONAL = 'B';
	const TRISTATE = 'T';
	const PASSIVE = 'P';
	const UNSPECIFIED = 'U';
	const POWERIN = 'W';
	const POWEROUT = 'w';
	const OPENCOLLECTOR = 'C';
	const OPENEMITTER = 'E';
	const NC = 'N';
}

class EeschemaComponentPinAttributes
{
	const Invisible = 'N';
	const Invert = 'I';
	const Clock = 'C';
	const LowLevelIn = 'L';
	const LowLevelOut = 'V';
	const ClockFall = 'F';
	const NonLogic = 'X';
}

class EeschemaComponentPin extends EeschemaComponentObject
{
	public $ShapeType = EeschemaComponentObject::SHAPE_PIN;

	public $Name = "";
	public $Number = "";
	public $PositionX = 0;
	public $PositionY = 0;
	public $Length = 0;
	public $Orientation = 0;
	public $NumberTextSize = 0;
	public $NameTextSize = 0;
	public $Unit = 0;
	public $Convert = 0;
	public $Type = '';
	private $attrs = '';

	private $font = 'Monaco, monospace';

	public $Attributes = array( 'invisible' => false,
								'shape' => array()
								);

	public function parse( $line )
	{
		$line = substr($line, 2);
		$i = sscanf($line, "%s %s %d %d %d %s %d %d %d %d %s %s",
			$this->Name,
			$this->Number,
			$this->PositionX,
			$this->PositionY,
			$this->Length,
			$this->Orientation,
			$this->NumberTextSize,
			$this->NameTextSize,
			$this->Unit,
			$this->Convert,
			$this->Type,
			$this->attrs);

		$this->Name = filter_var($this->Name, FILTER_SANITIZE_STRING);
		$this->Number = filter_var($this->Number, FILTER_SANITIZE_STRING);

		for($i = 0; $i < strlen($this->attrs); $i++ )
		{
			switch( $this->attrs[$i] )
			{
				case EeschemaComponentPinAttributes::Invisible:
					$this->Attributes['invisible'] = true;
					break;
				case EeschemaComponentPinAttributes::Clock:
					$this->Attributes['shape'][] = 'clock';
					break;
				case EeschemaComponentPinAttributes::Invert:
					$this->Attributes['shape'][] = 'invert';
					break;
			}
		}
	}

	public function draw( &$svg, &$component )
	{
		if( $this->Attributes['invisible'] )
		{
		}

		$this->drawPinSymbol($svg, $component);
		$this->drawPinText($svg, $component);
	}

	private function drawPinSymbol(&$svg, &$component)
	{
		$x = $x1 = $this->PositionX;
		$y = $y1 = $this->PositionY;

		$mapX1 = 0;
		$mapY1 = 0;
		switch( $this->Orientation )
		{
			case 'U':
				$y1 += $this->Length;
				$mapY1 = 1;
				break;
			case 'D':
				$y1 -= $this->Length;
				$mapY1 = -1;
				break;
			case 'L':
				$x1 -= $this->Length;
				$mapX1 = 1;
				break;
			case 'R':
				$x1 += $this->Length;
				$mapX1 = -1;
				break;
		}


		$svg->append(\SVGCreator\Element::LINE)
			->attr('x1', $component->transX($x1))
			->attr('y1', $component->transY($y1))
			->attr('x2', $component->transX($x))
			->attr('y2', $component->transY($y))
			->attr('stroke', '#000000');


		if( in_array('clock', $this->Attributes['shape'] ) )
		{
			$clock_size = $this->NameTextSize/2;

			if( $mapY1 == 0 )
			{
				$svg->append(\SVGCreator\Element::LINE)
					->attr('x1', $component->transX($x1))
					->attr('y1', $component->transY($y1 + $clock_size))
					->attr('x2', $component->transX($x1 - $mapX1 * $clock_size * 2))
					->attr('y2', $component->transY($y1))
					->attr('stroke', '#000000');

				$svg->append(\SVGCreator\Element::LINE)
					->attr('x1', $component->transX($x1 - $mapX1 * $clock_size * 2))
					->attr('y1', $component->transY($y1))
					->attr('x2', $component->transX($x1))
					->attr('y2', $component->transY($y1 - $clock_size))
					->attr('stroke', '#000000');
			}
			else
			{
				$svg->append(\SVGCreator\Element::LINE)
					->attr('x1', $component->transX($x1 + $clock_size))
					->attr('y1', $component->transY($y1))
					->attr('x2', $component->transX($x1))
					->attr('y2', $component->transY($y1 - ($mapY1*$clock_size*2)))
					->attr('stroke', '#000000');
				$svg->append(\SVGCreator\Element::LINE)
					->attr('x1', $component->transX($x1))
					->attr('y1', $component->transY($y1 - ($mapY1*$clock_size*2)))
					->attr('x2', $component->transX($x1 - $clock_size))
					->attr('y2', $component->transY($y1))
					->attr('stroke', '#000000');
			}

			$svg->append(\SVGCreator\Element::LINE)
				->attr('x1', $component->transX($mapX1 * $clock_size * 2 + $x1))
				->attr('y1', $component->transY($mapY1 * $clock_size * 2 + $y1))
				->attr('x2', $component->transX($x))
				->attr('y2', $component->transY($y))
				->attr('stroke', '#000000');
		}
	}

	private function drawPinText(&$svg, &$component)
	{
		$x1 = $x = $this->PositionX;
		$y1 = $y = $this->PositionY;

		switch( $this->Orientation )
		{
			case 'U':
				$y1 += $this->Length;
				break;
			case 'D':
				$y1 -= $this->Length;
				break;
			case 'L':
				$x1 -= $this->Length;
				break;
			case 'R':
				$x1 += $this->Length;
				break;
		}

		$nX = $x1;
		$nY = $y1;
		if( $component->pinNameOffset )
		{
			if( $component->drawName )
			{
				$anchor = 'start';
				if( $this->Orientation == 'R' )
				{
					$nX = $x1 + $component->pinNameOffset;
				}
				else if( $this->Orientation == 'L' )
				{
					$nX = $x1 - $component->pinNameOffset;
					$anchor = 'end';
				}
				else if( $this->Orientation == 'D' )
				{
					$nY = $y1 - $component->pinNameOffset;
				}
				else if( $this->Orientation == 'U' )
				{
					$nY = $y1 + $component->pinNameOffset;
				}

				$text = $svg->append(\SVGCreator\Element::TEXT)
					->attr('x', $component->transX($nX))
					->attr('y', $component->transY($nY))
					->attr('fill', '#000000')
					->attr('font-size', $this->NameTextSize)
					->attr('font-family', $this->font)
					->attr('text-anchor', $anchor)
					->attr('alignment-baseline', 'middle');

				if( strpos( $this->Name, '~' ) === 0 )
				{
					$text->attr('text-decoration', 'overline');
					$text->text(str_replace('~','',$this->Name));
				}
				else
				{
					$text->text($this->Name);
				}
			}

			$nuX = 0;
			$nuY = 0;
			if( $component->drawNum )
			{
				if( $this->Orientation == 'R' ||
					$this->Orientation == 'L' )
				{
					$nuX = ($x1 + $x)/2;
					$nuY = $y1 + EeschemaComponent::TXTMARGE;
				}
				else if( $this->Orientation == 'D' ||
							$this->Orientation == 'U' )
				{
					$nuX = $x1 - EeschemaComponent::TXTMARGE;
					$nuY = ($y1 + $y)/2;
				}

				$text = $svg->append(\SVGCreator\Element::TEXT)
					->attr('x', $component->transX($nuX))
					->attr('y', $component->transY($nuY))
					->attr('fill', '#000000')
					->attr('font-size', $this->NumberTextSize)
					->attr('font-family', $this->font)
					->attr('text-anchor', 'end')
					->attr('alignment-baseline', 'bottom')
					->text($this->Number);
			}
		}
	}
}
