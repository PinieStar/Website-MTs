@extends('layouts.public')

@section('title', 'Berita & Pengumuman')
@section('meta_description', 'Kabar terbaru, pengumuman, dan kegiatan dari MTs NU Al Syairiyah.')

@section('content')
    <x-public.page-hero eyebrow="Informasi"
                         title="Berita & Pengumuman"
                         lead="Ikuti kabar terbaru seputar kegiatan dan informasi penting dari madrasah." />

    <x-public.section class="pt-4 lg:pt-6" labelledby="berita-title">
        <div class="flex flex-col gap-8">
            <div>
                <p class="eyebrow">
                    <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                    Kanal Informasi
                </p>
                <h2 id="berita-title" class="mt-4 font-display text-display font-bold text-teal-deep" data-reveal="lines">
                    Kabar dari madrasah
                </h2>
                <p class="measure mt-4 text-lead text-ink-soft" data-reveal="fade">
                    Dokumentasi kegiatan dan pengumuman terbaru dari madrasah.
                </p>
            </div>

            @if ($posts->count())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        @include('public.posts.partials.card', ['post' => $post, 'type' => 'berita'])
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <div class="mt-2">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <div class="bento flex flex-col items-center gap-3 border-dashed p-8 text-center sm:p-10">
                    <span class="h-px w-12 bg-gold" aria-hidden="true"></span>
                    <p class="font-display text-xl font-bold text-teal-deep">Belum ada berita</p>
                    <p class="max-w-md text-sm leading-relaxed text-ink-soft">
                        Berita dan pengumuman akan tampil di sini setelah dipublikasikan.
                    </p>
                </div>
            @endif
        </div>
    </x-public.section>
@endsection
