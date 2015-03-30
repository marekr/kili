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
		<div class="col-md-6">
			@if(!empty($component->doc_filename))
			<p>
				<a href="{{ $component->doc_filename }}">Documentation</a>
			</p>
			@endif
			<p>
				{{ $component->description }}
			</p>
		</div>
		<div class="col-md-6">
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
		</div>
	</div>
	<div class="row">
		<table class="table table-striped">
			<thead>
				<tr>
					<th></th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@foreach( $component->events as $event )
					<tr>
						<td>
							{{ $event }}
						</td>
						<td>
							{{ $event->date_occurred }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading">Symbol Preview</div>
			<div class="panel-body">
				<object style="width:100%" data="{{ asset('images/libraries/'.$component->library->id.'/'.$component->id) }}.svg" type="image/svg+xml"></object>
			</div>
		</div>
	</div>
</div>
@endsection
