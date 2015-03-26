<?php namespace App\Kicad;

use File;
use Exception;
use SplFileInfo;

class EeschemaLibraryReader {

	public $name = '';
	public $components = array();
	
	public function read( $file )
	{
		$data = File::get( $file );
		
		$spl = new SplFileInfo($file);
		$this->name = $spl->getBasename('.'.$spl->getExtension());
		
		$data = explode( "\n", $data );
		
		if( strpos( $data[0], "EESchema-LIB" ) === FALSE )
		{
			print $data[0];
			throw new Exception("Invalid file format");
		}
		
		$buffer = array();
		$reading = false;
		for( $i = 0; $i < count($data); $i++ )
		{
			if( strpos($data[$i], "DEF") === 0 )
			{
				$reading = true;
				$buffer = array();
			}
			
			if( $reading )
			{
				$buffer[] = $data[$i];
			}
			
			if( strpos($data[$i], "ENDDEF") === 0 )
			{
				$reading = false;
				$part = new EeschemaComponent();
				$part->parseRaw( $buffer );
				$this->components[] = $part;
				continue;
			}
		}
	}
}

class EeschemaComponent {

	public $name;
	public $prefix;
	public $pinNameOffset;
	public $drawNum = false;
	public $drawName = false;
	public $unitCount = 1;
	public $raw = "";
	
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
			
		}	
		
		$this->raw = implode("\n",$raw);
	}
}
