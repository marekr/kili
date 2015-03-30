@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $library->name }}
				<small>
					<a href="{{ action('PackageController@overview', array($library->package->id)) }}">
						{{ $library->package->name }}
					</a>
				</small>
			</h1>
		</div>
	</div>
	<div class="row">
		<div role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#components" aria-controls="components" role="tab" data-toggle="tab">Components</a></li>
			<li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab">History</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="components">
				<table class="table table-striped">
					<thead>
						<th>Name</th>
					</thead>
					<tbody>
					@foreach( $library->components as $component )
						<tr>
							<td><a href="{{ action('ComponentController@index', array($component->id)) }}">{{ $component->name }}</a></td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="history">
					<table class="table table-striped">
						<thead>
							<tr>
								<th></th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach( $library->events as $event )
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
		</div>
	</div>

	</div>
</div>
@endsection
