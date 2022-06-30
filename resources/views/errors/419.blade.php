@extends('cms::errors.minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('link', route('cms.dashboard', [], false))
