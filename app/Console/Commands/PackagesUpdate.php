<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PHPGit\Git;
use File;
use Exception;
use Storage;
use App\Package;
use App\Kicad\EeschemaLibraryReader;
use App\Component;
use App\Library;
use App\ComponentAlias;

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
			$this->parseLibraries($package, $path);
			
			//foreach ($git->tree('master') as $object) {
			//	echo $git->show($object['file']);
				//print_r($object);
			//}
			
			unset($git);
		}
	}
	
	private function parseLibraries(Package $package, $path)
	{
		$files = File::allFiles($path);
		foreach ($files as $file)
		{
			if( $file->getExtension() == "lib" )
			{
				$lib = new EeschemaLibraryReader();
				//try
				//{
					$lib->read( $file );
				//}
				//catch(Exception $e)
				//{
				//	echo "File extension matched lib but not eeschema lib " . (string)$file."\n";
				//	continue;
				//}
				
				echo "Parsed " . (string)$file . "\n";
				$library = $package->libraries()->where('name', $lib->name)->first();
				
				if( $library == null )
				{
					$library = new Library;
					$library->package_id = $package->id;
					$library->name = $lib->name;
					$library->save();
				}
				
				if( count($lib->components) > 0 )
				{
					foreach($lib->components as $comp)
					{
						$component = $library->components->where('name', $comp->name)->first();
						if( $component == null )
						{
							$component = new Component;
							$component->name = $comp->name;
							$component->prefix = $comp->prefix;
							$component->library_id = $library->id;
							$component->unit_count = $comp->unitCount;
							$component->draw_numbers = $comp->drawNum;
							$component->draw_names = $comp->drawName;
							$component->pin_name_offset = $comp->pinNameOffset;
							$component->raw = $comp->raw;
							$component->save();
							
							foreach($comp->alias as $a)
							{
								$alias = new ComponentAlias;
								$alias->component_id = $component->id;
								$alias->alias = $a;
								$alias->save();
							}
							
							try
							{
								$svg = $comp->draw();
								$path = 'libraries/'.$library->id.'/'.$component->id.'.svg';
								Storage::disk('images')->put($path, $svg);
							}
							catch(\SVGCreator\SVGException $e)
							{
								echo "Error generating image for " . $component->name . "\n";
							}
						}
					}
				}
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
