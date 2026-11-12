@php($site = config('site'))

<div class="page-transition" data-page-transition data-active="false" aria-hidden="true">
    <div class="page-transition__mark flex flex-col items-center gap-4" data-transition-mark>
        <x-public.logo :size="58" loading="eager" />
        <span class="text-[0.68rem] font-bold uppercase tracking-[0.28em] text-paper/60">{{ $site['name'] }}</span>
        <span class="gold-rule !w-10"></span>
    </div>
</div>
