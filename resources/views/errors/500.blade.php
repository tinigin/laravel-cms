@extends('cms::errors.minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))
@section('link', route('cms.dashboard', [], false))
