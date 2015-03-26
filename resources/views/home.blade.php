@extends('app')

@section('content')


<style type='text/css'>
.jumbotron {
  text-align: center;
  background-color: transparent;
}
</style>
<div class="container">
	<div class="jumbotron">
		<h1>Find your KiCad libraries, components and footprints!</h1>
	</div>
	<div class="row">
		<form method="get" action="{{ url('/search') }}">
			<div class="input-group">
				<input type="text" class="form-control" name="q" placeholder="Search libraries....">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
				</span>
			</div>
		</form>
	</div>
</div>
@endsection
