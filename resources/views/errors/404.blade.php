@extends('cms::errors.minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found'))
@section('link', route('cms.dashboard', [], false))
