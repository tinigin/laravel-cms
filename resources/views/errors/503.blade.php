@extends('cms::errors.minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable'))
@section('link', route('cms.dashboard', [], false))
