<?php namespace App\KiCad;

use File;
use Exception;
use SplFileInfo;
use Imagine\Gd\Imagine;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Box;
use Imagine\Image\Point;

use SVGCreator\Element;
use SVGCreator\Elements\Svg;
use SVGCreator\Elements\Rect;


class EeschemaComponent {

	public $name = '';
	public $prefix = '';
	public $keywords = '';
	public $description = '';
	public $docFilename = '';
	public $pinNameOffset = 0;
	public $drawNum = false;
	public $drawName = false;
	public $unitCount = 1;
	public $raw = "";
	const TXTMARGE  = 10;
	public $drawItems = array();

	public $alias = array();

	public $minWidth = 0;
	public $minHeight = 0;

	public function transX($x)
	{
		return $x + $this->minWidth/2;
	}

	public function transY($y)
	{
		return $this->minHeight/2 - $y;
	}

	public function determineTransBB()
	{
		$minX = 0;
		$minY = 0;
		foreach($this->drawItems as $draw)
		{
			$minX = max($minX, abs($draw->PositionX));
			$minY = max($minY, abs($draw->PositionY));
		}

		$this->minWidth = $minX *2;
		$this->minHeight = $minY *2;
	}

	public function draw()
	{
		$this->determineTransBB();

		$attributesSvg = array(
							'width' => '100%',
							'height' => '100%',
							'xmlns' => 'http://www.w3.org/2000/svg',
							'viewBox' => '0 0 ' . $this->minWidth . ' ' . $this->minHeight
						  );
		$svg = new \SVGCreator\Elements\Svg($attributesSvg);

		foreach($this->drawItems as $draw)
		{
			if( $draw->ShapeType == ShapeType::SQUARE )
			{
				if( $draw->Width < 1 )
					$draw->Width = 1;

				$width = abs($draw->EndX) + abs($draw->PositionX);
				$height = abs($draw->EndY) + abs($draw->PositionY);
				$svg->append(new \SVGCreator\Elements\Rect())
					->attr('width', $width)
					->attr('height', $height)
					->attr('fill', '#ffffff')
					->attr('stroke-width', $draw->Width)
					->attr('stroke', '#000000')
					->attr('x', $this->transX($draw->PositionX))
					->attr('y', $this->transY($draw->PositionY));
			}
			else if( $draw->ShapeType == ShapeType::PIN )
			{
				if( $draw->Unit == 0 || $draw->Unit == 1 )
				$draw->draw($svg, $this);
			}
		}
		return $svg->getString();
		/*
		$palette = new RGB();

		$imagine = new Imagine();
		$box = new Box(800, 600);
		$color = $palette->color('#000');
		$image = $imagine->create($box, $color);

		$x = 200;
		foreach($this->drawItems as $draw)
		{
			$coords = array( new Point($x + $draw->PositionX, $x + $draw->EndY),
							new Point($x + $draw->PositionX, $x + $draw->PositionY),
							new Point($x + $draw->EndX, $x + $draw->PositionY),
							new Point($x + $draw->EndX, $x + $draw->EndY)
							);

			$image->draw()->polygon( $coords, $image->palette()->color('fff') );
		}*/

	//	return $image;
	}

	public function parseRaw( array $raw )
	{
		if( strpos($raw[0], "DEF") !== 0 )
		{
			throw new Exception("Error parsing component definition");
		}
		$header = str_replace( "\n", " ",  $raw[0] );
		$header = str_replace( "\t", " ",  $header );
		$header = explode( " ", $header );

		list( $def, $this->name, $this->prefix, $unused, $this->pinNameOffset, $drawNum, $drawName, $this->unitCount ) = $header;

		$this->name = filter_var(trim($this->name), FILTER_SANITIZE_STRING);
		$this->prefix = filter_var($this->prefix, FILTER_SANITIZE_STRING);
		$this->pinNameOffset = (int)$this->pinNameOffset;
		$this->unitCount = (int)$this->unitCount;

		if( $drawNum == 'Y' )
		{
			$this->drawNum = true;
		}
		else
		{
			$this->drawNum = false;
		}

		if( $drawName == 'Y' )
		{
			$this->drawName = true;
		}
		else
		{
			$this->drawName = false;
		}

		if( $this->unitCount < 1 )
		{
			$this->unitCount = 1;
		}

		for( $i = 1; $i < count($raw); $i++ )
		{
			$str = $raw[ $i ];

			if( strpos($str,'#') === 0 )
				continue;

			if( strpos($str,'Ti') === 0 )
			{
				//date time?
			}
			elseif( strpos($str,'F') === 0 )
			{
				//date time?
			}
			elseif( strpos($str, 'DRAW') === 0 )
			{
				$this->readDraw( $raw, $i );
			}
			elseif( strpos($str, 'ALIAS') === 0 )
			{
				$this->readAlias( substr($str,6) );
			}
		}

		$this->raw = implode("\n",$raw);
	}

	public function parseDoc( $raw )
	{
		for( $i = 1; $i < count($raw); $i++ )
		{
			$str = $raw[ $i ];

			if( strpos($str,'D') === 0 )
			{
				$this->description = substr($str,2);
				$this->description = filter_var($this->description, FILTER_SANITIZE_STRING);
			}
			elseif( strpos($str,'K') === 0 )
			{
				$this->keywords = substr($str,2);
				$this->keywords = filter_var($this->keywords, FILTER_SANITIZE_STRING);
			}
			elseif( strpos($str,'F') === 0 )
			{
				$this->docFilename = substr($str,2);
				$this->docFilename = filter_var($this->docFilename, FILTER_SANITIZE_URL);
			}

		}
	}

	private function readAlias( $str )
	{
		$this->alias = array();

		$this->alias = explode(' ', $str);
		$this->alias = filter_var_array($this->alias, FILTER_SANITIZE_STRING);
	}

	private function readDraw( $raw, &$i )
	{
		for( ; $i < count($raw); $i++ )
		{
			if( strpos($raw[ $i ], 'ENDDRAW') === 0 )
			{
				break;
			}

			switch( $raw[$i][0] )
			{
				case 'A':
					//arc
					break;
				case 'C':
					//circle
					break;
				case 'T':
					//text
					break;
				case 'S':
					//square
					$obj = new EeschemaComponentSquare();
					$obj->parse($raw[$i]);
					$this->drawItems[] = $obj;
					break;
				case 'X':
					//pin desc
					$obj = new EeschemaComponentPin();
					$obj->parse($raw[$i]);
					$this->drawItems[] = $obj;
					break;
				case 'P':
					//polyline
					break;
				case 'B':
					//beizer Curves
					break;
				case '#':
					continue;
				default:
					break;
			}
		}
	}
}

class ShapeType
{
  const NONE = 0;
  const SQUARE = 1;
  const PIN = 2;
}

class EeschemaComponentObject
{
	public $ShapeType = ShapeType::NONE;
}

class EeschemaComponentSquare extends EeschemaComponentObject
{
	public $ShapeType = ShapeType::SQUARE;

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
}

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
	public $ShapeType = ShapeType::PIN;

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
		list($this->Name,
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
			$this->attrs) = sscanf($line, "%s %s %d %d %d %s %d %d %d %d %s %s");

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
