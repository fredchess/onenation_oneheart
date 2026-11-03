@extends('front.layouts.app')
@section('title', 'Contact')
@section('meta_description', 'Contactez One Nation One Heart.')

@section('content')
    @include('front.partials.page-hero', ['title' => 'Contact', 'crumbs' => ['Contact' => null], 'site' => 'hero_contact'])

    <section class="wrap grid gap-12 py-12 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-16 lg:py-16">
        <div class="min-w-0">
            @include('front.partials.section-title', [
                'title' => 'Contactez-nous',
                'text' => 'Nous sommes ouverts ! Laissez-nous un message et nous reviendrons vers vous très rapidement.',
            ])

            {{-- Ouvre la messagerie de l'utilisateur avec le message pré-rempli --}}
            <form class="grid gap-5" data-reveal x-data="{ name: '', email: '', subject: '', message: '' }"
                  @submit.prevent="window.location.href = 'mailto:contact@onoh.org?subject=' + encodeURIComponent(subject || 'Message depuis onoh.org') + '&body=' + encodeURIComponent(message + '\n\n' + name + ' (' + email + ')')">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="contact-name" class="label">Nom complet</label>
                        <input id="contact-name" type="text" class="field" x-model="name" autocomplete="name" required>
                    </div>
                    <div>
                        <label for="contact-email" class="label">Email</label>
                        <input id="contact-email" type="email" class="field" x-model="email" autocomplete="email" required>
                    </div>
                </div>
                <div>
                    <label for="contact-subject" class="label">Objet</label>
                    <input id="contact-subject" type="text" class="field" x-model="subject">
                </div>
                <div>
                    <label for="contact-message" class="label">Message</label>
                    <textarea id="contact-message" rows="6" class="field" x-model="message" required></textarea>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <button type="submit" class="btn-coral">Envoyer le message <x-onoh.icon name="arrow-right" class="arrow" /></button>
                    <p class="text-sm text-slate">Votre messagerie s'ouvrira avec le message prêt à envoyer.</p>
                </div>
            </form>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start" data-reveal style="--delay: 100ms">
            <dl class="ndop divide-y divide-white/12 rounded-box px-6 text-white">
                <div class="py-5">
                    <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Adresse</dt>
                    <dd class="mt-1 font-semibold">Omnisports, Yaoundé, Cameroun</dd>
                </div>
                <div class="py-5">
                    <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Email</dt>
                    <dd class="mt-1 font-semibold"><a href="mailto:contact@onoh.org" class="hover:underline">contact@onoh.org</a></dd>
                </div>
                <div class="py-5">
                    <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Téléphone / WhatsApp</dt>
                    <dd class="mt-1 font-semibold">
                        <a href="https://wa.me/237655029867" target="_blank" rel="noopener" class="hover:underline">+237 6 55 02 98 67</a><br>
                        <a href="tel:+33614597593" class="hover:underline">+33 6 14 59 75 93</a>
                    </dd>
                </div>
            </dl>
        </aside>
    </section>
@endsection
