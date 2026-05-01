<div class="">
    <div class="fund-raised d-flex align-items-center">
        <div class="icon">
            <span class="flaticon-heart"></span>
        </div>
        <div class="text section-counter-2">
            <h4 class="countup-">
                {{ isset($orphelinat) ? number_format($orphelinat->dons->where('payment_status', \App\Enums\PaymentStatus::SUCCESS)->sum('amount')) : number_format($total_donations) }}
                FCFA</h4>
            <span>Dons récoltés</span>
        </div>
    </div>
    <form method="POST" action="{{ route('public.donation') }}" class="appointment" id="donation_form">
        @csrf
        <span class="subheading">Faire un don</span>
        <h2 class="mb-4 appointment-head">Donner est le plus grand acte de grace</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <input type="text" name="name" class="form-control" placeholder="Nom complet" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="amount">Nature du Don </label>

                    <select class="form-select" name="donate_option" id="select-don">
                        @foreach (\App\Enums\DonationTypeEnum::cases() as $key => $case)
                            <option value="{{ $case->value }}" id="{{ $case->value }}-option" {{ $case->value != \App\Enums\DonationTypeEnum::FINANCIAL->value ? 'disabled': '' }}>{{ $case->label() }}</option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div id="financial-block" class="mt-3" style="display: block">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="mb-2">Moyen de paiement</label>
                        <input type="hidden" name="payment_mode" id="payment_mode_hidden" value="">
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input payment-switch" type="checkbox" role="switch"
                                    id="switch_card" data-mode="card" data-target="payment_mode1-block">
                                <label class="form-check-label" for="switch_card">Carte bancaire (Visa/Mastercard)</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input payment-switch" type="checkbox" role="switch"
                                    id="switch_momo" data-mode="momo" data-target="payment_mode3-block">
                                <label class="form-check-label" for="switch_momo">OM / MTN MoMo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="payment_mode3-block" style="display: none">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tel">N° de tel</label>
                        <input id="phone" type="tel" name="tel" class="form-control"
                            placeholder="Mobile Money">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="amount">Montant (en FCFA)</label>
                        <input type="number" name="amount" class="form-control"
                            placeholder="Montant à donner (en FCFA)" id="amount">
                    </div>
                </div>
            </div>
            <div id="payment_mode1-block" style="display: none">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="amount_card">Montant (en FCFA)</label>
                        <input type="number" name="amount_card" class="form-control"
                            placeholder="Montant à donner (en FCFA)" id="amount_card">
                    </div>
                </div>
            </div>
            <div id="recepteur-block" style="display: none">
                <div class="col-md-12">
                    <div class="form-group">
                        <p> Recepteur des dons en nature</p>
                        <form>
                            <ul>
                                <li>Nom: One nation one heart</li>
                                <li>Numero de telephone: +237 6 55 02 98 67</li>
                            </ul>
                        </form>

                    </div>
                </div>
            </div>
            <div id="Sponsoring-block" style="display: none">
                <div class="col-md-12">
                    <div class="form-group">
                        <p>Veuillez joindre ce contact: +33 6 59 49 68 51</p>
                    </div>
                </div>
            </div>
            <div id="achat-block" style="display: none">
                <div class="col-md-12">
                    <div class="form-group">
                        <a href="https://market.lamater.net/?ref=onenation_oneheart" target="_blank">effectuons
                            l'achat</a>
                    </div>
                </div>
            </div>
            @isset($orphelinat)
                <input type="hidden" name="orphanage_id" value="{{ $orphelinat->id }}" />
            @endisset
            <div class="col-md-12 mt-4">
                @include('components.turnstile-widget', ['theme' => 'light'])
            </div>
            <div class="col-md-12 mt-3">
                <input type="submit" value="Faire mon don" class="btn btn-light py-3 px-4 rounded">
            </div>
        </div>
    </form>
</div>

@section('script')
    <script>
        $(document).ready(function() {
            $('.payment-switch').on('change', function() {
                var $this = $(this);

                if ($this.is(':checked')) {
                    // Désactiver l'autre switch
                    $('.payment-switch').not($this).prop('checked', false);
                    // Cacher tous les blocs de saisie
                    $('#payment_mode1-block, #payment_mode3-block').hide();
                    // Afficher le bloc correspondant
                    $('#' + $this.data('target')).show();
                    // Mettre à jour le champ caché
                    $('#payment_mode_hidden').val($this.data('mode'));
                } else {
                    // Empêcher le décoché si aucun autre n'est sélectionné
                    $this.prop('checked', true);
                }
            });
            $('#select-don').change(function() {
                if ($('#financial-option').is(':selected')) {
                    $('#financial-block').show()
                } else {
                    $('#financial-block').hide()
                    $('#payment_mode3-block').hide()
                    $('#payment_mode1-block').hide()
                }
                if ($('#recepteur-option').is(':selected')) {
                    $('#recepteur-block').show()
                } else {
                    $('#recepteur-block').hide()
                }
                if ($('#Collecteur-option').is(':selected')) {
                    $('#recepteur-block').show()
                }

                if ($('#Sponsoring-option').is(':selected')) {
                    $('#Sponsoring-block').show()
                } else {
                    $('#Sponsoring-block').hide()
                }
                if ($('#achat-option').is(':selected')) {
                    $('#achat-block').show()
                } else {
                    $('#achat-block').hide()
                }
            })

            const id = setTimeout(() => {
                document.querySelector('.info-donation').classList.add('hidden')
            }, 5000);
        })
    </script>
@endsection
