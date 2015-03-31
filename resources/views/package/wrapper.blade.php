@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $package->name }}</h1>
		</div>
		<p class="lead">{{ $package->description }}</p>
		<div class="btn-group btn-group-justified" role="group" aria-label="...">
			<a href="{{ action('PackageController@overview', array($package->id)) }}" role="button" class="btn btn-default{{ $page == 'overview' ? ' active' : '' }}">
				Overview
			</a>
			<a href="{{ action('PackageController@history', array($package->id)) }}" role="button" class="btn btn-default{{ $page == 'history' ? ' active' : '' }}">
				History
			</a>
		</div>
	</div>
	@yield('package_content')
</div>
@endsection
