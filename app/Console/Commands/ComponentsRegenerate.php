<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Storage;
use App\Component;
use App\KiCad\EeschemaComponent;

class ComponentsRegenerate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'components:regenerate';

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
		$components = Component::with('library')->get();

		foreach($components as $component)
		{
			$comp = new EeschemaComponent();
			$comp->parseRaw( explode("\n",$component->raw) );

			try
			{
				$svg = $comp->draw();
				$path = 'libraries/'.$component->library->id.'/'.$component->id.'.svg';
				Storage::disk('images')->put($path, $svg);
			}
			catch(\SVGCreator\SVGException $e)
			{
				echo "Error generating image for " . $component->name . "\n";
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
