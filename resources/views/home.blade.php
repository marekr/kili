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
		
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Search libraries....">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
			</span>
		</div><!-- /input-group -->
	</div>
</div>
@endsection
