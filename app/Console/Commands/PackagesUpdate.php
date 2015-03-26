<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PHPGit\Git;
use File;
use Exception;
use App\Package;
use App\Kicad\EeschemaLibraryReader;

class PackagesUpdate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'packages:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$packages = Package::all();
		
		$base = 'C:\xampp\htdocs\kili\tmp\git';
		foreach( $packages as $package )
		{
			$path = $base.'\\'.$package->id;
			$git = new Git();
			if( file_exists($path) === false )
			{
				echo "Cloning";
				$git->clone($package->repository_url, $path);
			}
			$git->setRepository($path);
			//$git->pull();
			$this->parseLibraries($path);
			
			//foreach ($git->tree('master') as $object) {
			//	echo $git->show($object['file']);
				//print_r($object);
			//}
			
			unset($git);
		}
	}
	
	private function parseLibraries($path)
	{
		$files = File::allFiles($path);
		foreach ($files as $file)
		{
			if( $file->getExtension() == "lib" )
			{
				echo (string)$file, "\n";
			
				try
				{
					$lib = new EeschemaLibraryReader();
					$lib->read( $file );
				}
				catch(Exception $e)
				{
				}
			}
			else if( $file->getExtension() == "dcm" )
			{
				echo (string)$file, "\n";
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
		];
	}

}
