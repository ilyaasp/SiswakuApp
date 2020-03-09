@extends('template')

@section('main')
	<div id="siswa">
		<h2>Edit siswa</h2>

		{!! Form::model($siswa, ['method' => 'PATCH', 'files' => true, 'action' => ['SiswaController@update', $siswa->id]]) !!}
			@include('siswa.form', ['submitButtonText' => 'Update'])
		{!! Form::close() !!}
	</div>
@stop

@section('footer')
	@include('footer')
@stop
