<div class="">
    <div class="fund-raised d-flex align-items-center">
        <div class="icon">
            <span class="flaticon-heart"></span>
        </div>
        <div class="text section-counter-2">
            <h4 class="countup-">
                {{ isset($orphelinat) ? number_format($orphelinat->dons->where('status', 1)->sum('amount')) : number_format($total_donations) }}
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
                    <div class="form-group d-flex" style="flex-wrap: wrap;">
                        <div class="form-check d-flex" id="mode3_payment">
                            <input class="form-check-input" type="radio" name="payment_mode" value="paypal"
                                id="payment_mode1">
                            <label class="form-check-label" for="payment_mode1">Paypal / Carte
                                bancaire</label>
                        </div>
                        <div class="form-check d-flex ms-3">
                            <input class="form-check-input" type="radio" name="payment_mode" value="momo"
                                id="payment_mode3">
                            <label class="form-check-label" for="payment_mode3">OM / MTN
                                MoMo</label>
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
                        <label for="amount">Montant (en EUR)</label>
                        <input type="number" name="amount_eur" class="form-control"
                            placeholder="Montant à donner (en EUR)" id="amount">
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
            <div class="col-md-12 mt-5">
                <input type="submit" value="Faire mon don" class="btn btn-light py-3 px-4 rounded">
            </div>
        </div>
    </form>
</div>

@section('script')
    <script>
        $(document).ready(function() {
            $('#payment_mode3, #payment_mode1').on('change', function() {
                if ($('#payment_mode3').prop('checked') === true) {
                    $('#payment_mode3-block').show()
                    $('#payment_mode1-block').hide()
                } else {
                    $('#payment_mode3-block').hide()
                    $('#payment_mode1-block').show()
                }

            })
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
