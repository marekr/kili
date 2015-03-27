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

	public $name;
	public $prefix;
	public $pinNameOffset;
	public $drawNum = false;
	public $drawName = false;
	public $unitCount = 1;
	public $raw = "";
	
	public $drawItems = array();
	
	public function transX($x)
	{
		return $x + 300;
	}
	
	public function transY($y)
	{
		return $y + 300;
	}
	
	public function draw()
	{
		$attributesSvg = array(
							'width' => 1000,
							'height' => 1000
						  );
		$svg = new \SVGCreator\Elements\Svg($attributesSvg);
		
				
		foreach($this->drawItems as $draw)
		{
			if( $draw->ShapeType == ShapeType::SQUARE )
			{
				$width = abs($draw->EndX) + abs($draw->PositionX);
				$height = abs($draw->EndY) + abs($draw->PositionY);
				$svg->append(new \SVGCreator\Elements\Rect())
					->attr('width', $width)
					->attr('height', $height)
					->attr('fill', '#ff0000')
					->attr('x', $this->transX($draw->PositionX))
					->attr('y', $this->transY($draw->PositionY));
			}
			else if( $draw->ShapeType == ShapeType::PIN )
			{
				$this->drawPinText($svg, $draw);
			}
		}
		echo $svg->getString();	
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
	
	private function drawPinText($svg, $draw)
	{
		$x = $this->transX($draw->PositionX);
		$y = $this->transY($draw->PositionY);
		switch( $draw->Orientation )
		{
			case 'U':
				$y -= $draw->Length;
				break;
			case 'D':
				$y += $draw->Length;
				break;
			case 'R':
				$x += $draw->Length;
				break;
			case 'L':
				$x -= $draw->Length;
				break;
		}
		
		$svg->append(\SVGCreator\Element::TEXT)
			->attr('x', $x)
			->attr('y', $y)
			->attr('fill', '#000000')
			->text($draw->Name);
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
			if( strpos($raw[ $i ],'#') === 0 )
				continue;
			
			if( strpos($raw[ $i ],'Ti') === 0 )
			{
				//date time?
			}
			elseif( strpos($raw[ $i ],'F') === 0 )
			{
				//date time?
			}
			elseif( strpos($raw[ $i ], 'DRAW') === 0 )
			{
				$this->readDraw( $raw, $i );
			}
		}	
		
		$this->raw = implode("\n",$raw);
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
	public $Attrs = '';
	
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
			$this->Attrs) = sscanf($line, "%s %s %d %d %d %s %d %d %d %d %s %s");
	}
}