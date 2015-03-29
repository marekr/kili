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
		unset($data);

		// check if theres a doc file
		$docFile = $spl->getRealPath();
		$docFile = preg_replace('/'.$spl->getExtension().'$/', 'dcm', $docFile);

		$this->loadDoc($docFile);

	}

	private function setDocDataToComponent($name, $data)
	{
		foreach( $this->components as $component )
		{
			if( $component->name == $name )
			{
				$component->parseDoc($data);
			}
		}
	}

	private function loadDoc($docFile)
	{
		if( File::exists($docFile) )
		{
			$data = File::get( $docFile );
			$data = explode( "\n", $data );
			$componentName = '';
			$reading = false;
			for( $i = 0; $i < count($data); $i++ )
			{
				$str = $data[ $i ];

				if( strpos($str, '$CMP') === 0 )
				{
					$reading = true;
					$buffer = array();

					$componentName = trim(substr($str, 5));

					// We dont store this string anywhere but we do compare
					// it agaisnt the component name which is also filtered
					$componentName = filter_var($componentName, FILTER_SANITIZE_STRING);
				}

				if( $reading )
				{
					$buffer[] = $str;
				}

				if( strpos($str, '$ENDCMP') === 0 )
				{
					$reading = false;
					$this->setDocDataToComponent($componentName, $buffer);
					$componentName = '';
				}
			}
		}
	}
}
