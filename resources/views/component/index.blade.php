@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $component->name }}
				<small> 
					<a href="{{ action('PackageController@overview', array($component->library->package->id)) }}">
						{{ $component->library->package->name }}
					</a> / 
					<a href="{{ action('LibraryController@overview', array($component->library->id)) }}">
						{{ $component->library->name }}
					</a>
				</small>
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading">Aliases</div>
			<div class="panel-body">
			<ul>
			@foreach( $component->aliases as $alias )
				<li>{{ $alias->alias }}</li>
			@endforeach
			</ul>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">Symbol</div>
			<div class="panel-body">
				<object style="width:400px" data="{{ asset('images/libraries/'.$component->library->id.'/'.$component->id) }}.svg" type="image/svg+xml"></object>
			</div>
		</div>
	</div>
</div>
@endsection
