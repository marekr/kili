<?php namespace App\KiCad;

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