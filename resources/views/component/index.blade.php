@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $component->name }}</h1>
		</div>
		<object style="width:400px" data="{{ asset('images/libraries/'.$component->library->id.'/'.$component->id) }}.svg" type="image/svg+xml"></object>
	</div>
</div>
@endsection
