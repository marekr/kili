@extends('app')

@section('content')


<style type='text/css'>
.jumbotron {
  text-align: center;
  background-color: transparent;
}
</style>
<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $package->name }}</h1>
		</div>
		<p class="lead">{{ $package->description }}</p>
		<p>
			<strong>Repository URL:</strong> {{ $package->repository_url }}
		</p>
	</div>
	<div class="row">
		<h2>Libraries</h2>
		<table class="table table-striped">
			<thead>
				<th>Name</th>
				<th>Type</th>
			</thead>
		</table>
	</div>
</div>
@endsection
