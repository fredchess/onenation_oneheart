{{--
    Titre de section, filet en dessous. $title, $text, $aside (HTML optionnel), $light.
    Par défaut titre à gauche et texte à droite ; $stacked place le texte sous le titre.
--}}
<div class="mb-8 grid items-end gap-4 border-b pb-5 {{ empty($stacked) ? 'md:grid-cols-2 md:gap-10' : 'md:grid-cols-[1fr_auto]' }} {{ !empty($light) ? 'border-white/15' : 'border-rule' }}" data-reveal>
    <div>
        <h2 class="display text-[2.4rem] md:text-[3.25rem] {{ !empty($light) ? 'text-white' : 'text-indigo' }}">{{ $title }}</h2>
        @if (!empty($stacked) && !empty($text))<p class="mt-2 max-w-[60ch] {{ !empty($light) ? 'text-[#dfe3ff]' : 'text-slate' }}">{!! $text !!}</p>@endif
    </div>
    @if ((empty($stacked) && !empty($text)) || !empty($aside))
        {{-- Colonne de droite alignée à droite pour ne pas laisser de vide en bout de ligne --}}
        <div class="flex flex-col gap-3 md:items-end md:text-right">
            @if (empty($stacked) && !empty($text))<p class="max-w-[52ch] {{ !empty($light) ? 'text-[#dfe3ff]' : 'text-slate' }}">{!! $text !!}</p>@endif
            {!! $aside ?? '' !!}
        </div>
    @endif
</div>
