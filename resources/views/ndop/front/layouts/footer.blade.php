<div class="band"></div>
<footer class="ndop pb-20 text-white lg:pb-0">
    <div class="wrap grid gap-10 py-12 md:grid-cols-[1.3fr_1fr_1fr] md:gap-10">
        <div>
            <span class="inline-block rounded-ui bg-white px-2 py-1.5">
                <img src="{{ asset('redesign/img/logo.webp') }}" alt="One Nation One Heart" width="360" height="129" class="h-8 w-auto" loading="lazy">
            </span>
            <p class="mt-4 max-w-[36ch] text-sm text-[#dfe3ff]">Chacun a la capacité et la responsabilité de changer le monde pour le mieux.</p>
            <div class="mt-5 flex gap-2">
                <a href="https://www.facebook.com/ONOH237" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-ui shadow-[inset_0_0_0_1px_rgba(255,255,255,.35)] transition-colors hover:bg-white hover:text-indigo" aria-label="Facebook de ONOH">
                    <x-onoh.icon name="facebook" class="size-[18px]" />
                </a>
                <a href="https://wa.me/237655029867" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-ui shadow-[inset_0_0_0_1px_rgba(255,255,255,.35)] transition-colors hover:bg-white hover:text-indigo" aria-label="WhatsApp de ONOH">
                    <x-onoh.icon name="whatsapp" class="size-[18px]" />
                </a>
            </div>
        </div>
        <nav aria-label="Pied de page">
            <h2 class="mb-3 text-xs font-bold uppercase tracking-[.14em] text-sky">Navigation</h2>
            <ul class="space-y-1.5 text-sm text-[#dfe3ff]">
                <li><a class="hover:text-white hover:underline" href="{{ route('public.orphanages') }}">Orphelinats</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('public.about') }}">À propos de ONOH</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('public.joinus') }}">Nous rejoindre</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('public.blog') }}">Blogs</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('public.contact') }}">Contact</a></li>
            </ul>
        </nav>
        <div>
            <h2 class="mb-3 text-xs font-bold uppercase tracking-[.14em] text-sky">Des questions ?</h2>
            <ul class="space-y-1.5 text-sm text-[#dfe3ff]">
                <li>Omnisport, Yaoundé, Cameroun</li>
                <li><a class="hover:text-white hover:underline" href="tel:+237655029867">+237 6 55 02 98 67</a> / <a class="hover:text-white hover:underline" href="tel:+33614597593">+33 6 14 59 75 93</a></li>
                <li><a class="hover:text-white hover:underline" href="mailto:contact@onoh.org">contact@onoh.org</a></li>
            </ul>
        </div>
    </div>
    <div class="wrap">
        <div class="flex flex-col gap-1 border-t border-white/15 py-5 text-[13px] text-[#aeb6e6] sm:flex-row sm:justify-between">
            <p>© {{ date('Y') }} One Nation One Heart. Tous droits réservés.</p>
            <p>Réalisé par <a href="https://fredchess.com" target="_blank" rel="noopener" class="text-white underline underline-offset-4">Frederick Essono</a></p>
        </div>
    </div>
</footer>
