@extends('layouts.public')

@section('title', $post->judul)
@section('meta_description', $post->ringkasan ?: str(strip_tags($post->sanitizedIsi()))->limit(160))

@section('content')
    @include('public.posts.partials.detail', ['post' => $post, 'type' => 'artikel'])
@endsection
