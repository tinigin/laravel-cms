@extends('cms::errors.minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests'))
@section('link', route('cms.dashboard', [], false))
