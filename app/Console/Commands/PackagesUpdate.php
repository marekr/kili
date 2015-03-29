<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PHPGit\Git;
use File;
use Exception;
use Storage;
use App\Package;
use App\PackageEvent;
use App\Kicad\EeschemaLibraryReader;
use App\Component;
use App\Library;
use App\LibraryEvent;
use App\ComponentAlias;
use App\ComponentEvent;
use Carbon\Carbon;

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
		$packID = (int)$this->option('package');

		if( $packID != 0 )
		{
			$package = Package::find($packID);
			if( $package == null )
			{
				$this->error('Invalid package id!');
				return;
			}
			$this->updatePackage($package);
		}
		else
		{
			$packages = Package::all();

			foreach( $packages as $package )
			{
				$this->updatePackage($package);
			}
		}
	}

	private function updatePackage(Package $package)
	{
		$base = 'C:\xampp\htdocs\kili\tmp\git';

		$path = $base.'\\'.$package->id;
		$git = new Git();
		if( file_exists($path) === false )
		{
			echo "Cloning";
			$git->clone($package->repository_url, $path);
		}
		$git->setRepository($path);
		//$git->pull();

		$git->checkout('origin');
		$log = $git->log('', null, array('limit'=> 9000));
		$size = count($log);
		for($i = $size-1; $i >= 0; $i--)
		{
			$entry = $log[$i];
			$this->info('Checkout out hash:' . $entry['hash']);
			$git->checkout($entry['hash']);
			$this->parseLibraries($package, $path, new Carbon($entry['date']), $entry['hash']);
			if( $i == $size-5 )
			{
		//		die();
			}
			$this->info('Parsing at hash:' . $entry['hash'] .' complete');
		}

		//foreach ($git->tree('master') as $object) {
		//	echo $git->show($object['file']);
			//print_r($object);
		//}

		unset($git);
	}

	private function parseLibraries(Package $package, $path, Carbon $libDate, $version)
	{
		$files = File::allFiles($path);
		foreach ($files as $file)
		{
			if( $file->getExtension() == "lib" )
			{
				$lib = new EeschemaLibraryReader( $file );
				try
				{
					$lib->read();
				}
				catch(Exception $e)
				{
					echo "File extension matched lib but not eeschema lib " . (string)$file."\n";
					continue;
				}

				echo "Parsed " . (string)$file . "\n";
				$library = $package->libraries()->where('name', $lib->name)->first();

				$new = false;
				if( $library == null )
				{
					$library = new Library;
					$new = true;
				}

				if( $library->hash != $lib->getHash() )
				{
					$library->package_id = $package->id;
					$library->name = $lib->name;
					$library->hash = $lib->getHash();
					$library->save();

					if( $new )
					{
						LibraryEvent::addCreated($library->id, $libDate);
					}
					else
					{
						LibraryEvent::addEdited($library->id, $libDate);
					}
				}

				$this->handleLibraryComponents($lib, $library, $libDate, $version);
			}
		}
	}

	private function handleLibraryComponents(EeschemaLibraryReader &$lib, Library &$library, Carbon $libDate, $version)
	{
		if( count($lib->components) > 0 )
		{
			foreach($lib->components as $comp)
			{
				$new = false;
				$component = $library->components->where('name', $comp->name)->first();
				if( $component == null )
				{
					$component = new Component;
					$new = true;
				}

				if( $component->hash != $comp->getHash() )
				{
					$component->name = $comp->name;
					$component->prefix = $comp->prefix;
					$component->library_id = $library->id;
					$component->unit_count = $comp->unitCount;
					$component->draw_numbers = $comp->drawNum;
					$component->draw_names = $comp->drawName;
					$component->pin_name_offset = $comp->pinNameOffset;
					$component->raw = $comp->raw;
					$component->description = $comp->description;
					$component->keywords = $comp->keywords;
					$component->doc_filename = $comp->docFilename;
					$component->hash = $comp->getHash();
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

					if( $new )
					{
						ComponentEvent::addCreated($component->id, $libDate);
					}
					else
					{
						ComponentEvent::addEdited($component->id, $libDate);
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
			['package', null, InputOption::VALUE_REQUIRED, 'Package ID', 0],
		];
	}

}
