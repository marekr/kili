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
		<div class="btn-group btn-group-justified" role="group" aria-label="...">
			<a href="{{ action('LibraryController@overview', array($library->id)) }}" role="button" class="btn btn-default{{ $page == 'overview' ? ' active' : '' }}">
				Overview
			</a>
			<a href="{{ action('LibraryController@history', array($library->id)) }}" role="button" class="btn btn-default{{ $page == 'history' ? ' active' : '' }}">
				History
			</a>
		</div>
	</div>
	@yield('library_content')
</div>
@endsection
