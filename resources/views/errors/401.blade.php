@extends('cms::errors.minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized'))
@section('link', route('cms.dashboard', [], false))
