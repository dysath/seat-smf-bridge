@extends('web::layouts.grids.12')

@section('title', trans('web::smfbridge.forum'))

@section('full')

	  <iframe src="{{ env('SMF_URL') }}" style="border:none; margin-top: -40px;" height="1000px" width="100%"></iframe>

@stop

