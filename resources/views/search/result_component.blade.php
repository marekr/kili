<div class="well">				
	<h4>
		<a href="{{ action('PackageController@overview', array($component->library->package->id)) }}">{{ $component->library->package->name }}</a> /
		<a href="{{ url('/library/') }}">{{ $component->library->name }}</a> /
		<a href="{{ action('ComponentController@index', array($component->id))}}">{{ $component->name }}</a>
	</h4>
	<small>eeschema component</small>
	<a href="{{ action('ComponentController@index', array($component->id))}}" class="btn btn-default pull-right btn-primary">View</a>
</div>