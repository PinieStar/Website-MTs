@php
    $site = config('site');
    $number = $site['contact']['whatsapp'] ?? null;
    $hasWhatsApp = filled($number);
    $message = rawurlencode('Assalamualaikum, saya ingin bertanya tentang MTs NU Al Syairiyah.');
    $href = $hasWhatsApp
        ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', (string) $number).'?text='.$message
        : route('public.kontak').'#kontak';
@endphp

<a href="{{ $href }}"
   @if ($hasWhatsApp) target="_blank" rel="noopener noreferrer" @endif
   class="wa-fab group fixed bottom-4 right-4 z-80 inline-flex min-h-12 items-center gap-2 rounded-pill bg-teal-bright px-3.5 text-sm font-semibold text-white shadow-float transition-[background-color,transform] duration-300 hover:-translate-y-0.5 hover:bg-teal-deep sm:bottom-6 sm:right-6 sm:px-4"
   aria-label="{{ $hasWhatsApp ? 'Hubungi madrasah melalui WhatsApp' : 'Buka halaman kontak' }}">
    <x-public.icon :name="$hasWhatsApp ? 'whatsapp' : 'message-circle'" :size="20" class="shrink-0" />
    <span class="hidden sm:inline">Hubungi</span>
</a>
