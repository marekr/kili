<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Package;
use App\Component;
use App\Library;
use App\LibraryEvent;
use App\ComponentAlias;
use App\ComponentEvent;

class DebugReset extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:reset';

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
		Library::truncate();
		LibraryEvent::truncate();
		Component::truncate();
		ComponentEvent::truncate();
		ComponentAlias::truncate();
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
