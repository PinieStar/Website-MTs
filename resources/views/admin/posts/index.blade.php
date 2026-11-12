@extends('layouts.admin')

@php($type = $type ?? 'berita')

@section('title', $type === 'artikel' ? 'Artikel' : 'Berita')

@section('content')
    <livewire:admin.post-manager :type="$type" />
@endsection
