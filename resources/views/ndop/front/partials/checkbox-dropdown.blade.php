{{--
    Liste déroulante à cases à cocher (villes, âges), identique sur l'accueil et la page Orphelinats.
    $name (ex. "villes[]"), $label, $placeholder, $options [valeur => libellé], $selected [valeurs], $class
    $responsive : sur mobile, affiche les options en puces (panneau « Filtres ») au lieu d'un menu ;
                  s'appuie sur la variable Alpine `desktop` du formulaire parent.
--}}
@php
    $responsive = $responsive ?? false;
    $selected = array_values(array_map('strval', $selected ?? []));
    $summary = collect($selected)->map(fn ($value) => $options[$value] ?? null)->filter()->join(', ');
@endphp
<fieldset class="relative min-w-0 {{ $class ?? '' }}"
          x-data="{ open: false, selected: {{ \Illuminate\Support\Js::from($selected) }}, labels: {{ \Illuminate\Support\Js::from((object) $options) }},
                    get summary() { return this.selected.map((value) => this.labels[value]).filter(Boolean).join(', ') } }"
          @click.outside="open = false" @keydown.escape.stop="open = false">
    <legend class="{{ $responsive ? 'label mb-2 lg:sr-only' : 'sr-only' }}">{{ $label }}</legend>

    <button type="button" class="{{ $responsive ? 'hidden lg:block' : 'block' }} w-full text-left" @click="open = !open" :aria-expanded="open.toString()">
        <span class="label mb-0.5" aria-hidden="true">{{ $label }}</span>
        <span class="flex items-center justify-between gap-2 font-semibold">
            <span class="truncate {{ $summary ? '' : 'font-normal text-slate/60' }}" :class="summary ? 'font-semibold text-ink' : 'font-normal text-slate/60'"
                  x-text="summary || @js($placeholder)">{{ $summary ?: $placeholder }}</span>
            <x-onoh.icon name="chevron-down" class="size-4 shrink-0 text-indigo transition-transform duration-200" ::class="open && 'rotate-180'" />
        </span>
    </button>

    <div x-show="{{ $responsive ? 'open || !desktop' : 'open' }}" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 -translate-y-1"
         class="{{ $responsive
             ? 'flex flex-wrap gap-2 lg:absolute lg:left-0 lg:top-full lg:z-30 lg:mt-px lg:w-full lg:min-w-56 lg:flex-col lg:gap-1 lg:rounded-b-box lg:border lg:border-rule lg:bg-white lg:p-2 lg:shadow-xl'
             : 'absolute inset-x-0 top-full z-30 mt-px flex max-h-72 min-w-56 flex-col gap-1 overflow-y-auto rounded-b-box border border-rule bg-white p-2 shadow-xl' }}">
        @foreach ($options as $value => $optionLabel)
            <label class="chip {{ $responsive ? 'lg:min-h-9 lg:border-0 lg:hover:bg-paper' : 'min-h-9 border-0 hover:bg-paper' }}">
                <input type="checkbox" name="{{ $name }}" value="{{ $value }}" x-model="selected" @checked(in_array((string) $value, $selected, true))>
                {{ $optionLabel }}
            </label>
        @endforeach
    </div>
</fieldset>
