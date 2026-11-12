@extends('layouts.admin')

@section('title', isset($galleryId) ? 'Kelola Album Galeri' : 'Galeri')

@section('content')
    @livewire('admin.gallery-manager', ['galleryId' => $galleryId ?? null])
@endsection
