<?php namespace App\KiCad;

use File;
use Exception;
use SplFileInfo;

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
		$maxX = 0;
		$maxY = 0;
		foreach($this->drawItems as $draw)
		{
			$bb = $draw->getBoundingBox();

			$minX = min($minX,$bb['minX']);
			$minY = min($minY,$bb['minY']);
			$maxX = max($maxX,$bb['maxX']);
			$maxY = max($maxY,$bb['maxY']);
		}

		$this->minWidth = abs($minX) + abs($maxX);
		$this->minHeight = abs($minY) + abs($maxY);
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
			if( $draw->Unit == 0 || $draw->Unit == 1 )
			$draw->draw($svg, $this);
		}
		return $svg->getString();
	}

	public function getHash()
	{
		return hash('sha1', $this->raw);
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

			$obj = null;
			switch( $raw[$i][0] )
			{
				case 'A':
					//arc
					$obj = new EeschemaComponentArc();
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
					break;
				case 'X':
					//pin desc
					$obj = new EeschemaComponentPin();
					break;
				case 'P':
					//polyline
					$obj = new EeschemaComponentPolyline();
					break;
				case 'B':
					//beizer Curves
					break;
				case '#':
					continue;
				default:
					break;
			}

			if( $obj != null )
			{
				$obj->parse($raw[$i]);
				$this->drawItems[] = $obj;
			}
		}
	}
}
