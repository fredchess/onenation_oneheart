@extends('admin.layouts.app')
{{-- Titre de la page --}}
@section('title')
    Dons
@endsection

@section('subtitle', 'La liste des dons enregistrés')

{{-- Code CSS supplémentaire (soit des <link></link> soit <style></style>) --}}
@section('css')
    <link href="{{ asset('admin_assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="card-title">Liste des dons</h4>
                        <div class="text-right" style="text-align: right;">
                            {{-- <a href="{{ route('donations.create') }}" class="btn btn-success">Ajouter un don</a> --}}
                        </div>
                    </div>
                    <div class="table-responsive">

                        <form action="{{ route('donations.update_status') }}" method="post" id="update_status">
                            @csrf
                            <input type="hidden" name="status" id="status" value="">
                            <input type="hidden" name="donation_id" id="donation_id" value="">
                        </form>

                        <form action="{{ route('donations.multipleDestroy') }}" method="post" name="deleteForm">
                            @csrf
                            <table class="table no-wrap v-middle mb-0" id="dataTable">
                                <thead>
                                    <tr class="border-0">
                                        <th class="font-14 font-weight-medium text-muted">
                                            <label class="custom-control custom-checkbox be-select-all">
                                                <input class="custom-control-input chk_all" type="checkbox"
                                                    name="chk_all"><span class="custom-control-label"></span>
                                            </label>
                                        </th>
                                        <th class="font-14 font-weight-medium text-muted">Nom</th>
                                        <th class="font-14 font-weight-medium text-muted">Email</th>
                                        <th class="font-14 font-weight-medium text-muted">Tel</th>
                                        <th class="font-14 font-weight-medium text-muted">Montant</th>
                                        <th class="font-14 font-weight-medium text-muted">Mode de paiement</th>
                                        <th class="font-14 font-weight-medium text-muted">Statut</th>
                                        <th class="font-14 font-weight-medium text-muted">Date</th>
                                        <th class="font-14 font-weight-medium text-muted">Orphelinat associé</th>
                                        <th class="font-14 font-weight-medium text-muted">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($donations->reverse() as $donation)

                                        <tr>
                                            <td>
                                                <label class="custom-control custom-checkbox">
                                                    <input class="custom-control-input checkboxes" type="checkbox"
                                                        value="{{ $donation->id }}" name="ids[]"
                                                        id="check{{ $donation->id }}"><span
                                                        class="custom-control-label"></span>
                                                </label>
                                            </td>
                                            <td>{{ $donation->datas['name'] ?? '' }}</td>
                                            
                                            <td>{{ $donation->datas['email'] ?? '' }}</td>
                                            <td>{{ $donation->datas['tel'] ?? '' }}</td>
                                            <td>{{ $donation->amount }} FCFA</td>
                                            <td>{{ $donation->datas['payment_mode'] ?? '' }}</td>
                                            <td>

                                                @if ($donation->status == 1) <div onclick="document.getElementById('donation_id').value='{{$donation->id}}'; document.getElementById('status').value='0'; document.getElementById('update_status').submit()"  class="badge btn bg-light-success">Reçu</div> @else <div onclick="document.getElementById('donation_id').value='{{$donation->id}}'; document.getElementById('status').value='1'; document.getElementById('update_status').submit()"   class="badge btn bg-light-danger">Non reçu</div> @endif

                                            </td>
                                            <td>
                                                {{ $donation->created_at }}
                                            </td>
                                            
                                            <td>
                                                @if($donation->orphanage)
                                                <a href="{{route("public.orphanages.details", ["orphanage_slug"=>$donation->orphanage->slug])}}">
                                                    {{ $donation->orphanage->name  }}
                                                </a>
                                                @else
                                                N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{-- <a type="button" class="btn btn-primary"
                                                    href={{ route('donations.edit', $donation) }}><i
                                                        class="bi-pencil c-orange-700"></i></a>
                                                </a> --}}
                                                <button type="button"
                                                    onclick="document.querySelector('#check{{ $donation->id }}').setAttribute('checked', 'checked');(confirm('Êtes-vous sûr de vouloir supprimer cet(s) orphelinat(s) ?')? document.deleteForm.submit():console.log('annulé'));"
                                                    class="btn btn-danger">
                                                    <span class=""><i class="bi-trash c-red-600"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Code Scripts avec des balises <script></script> à ajouter --}}
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="{{ asset('admin_assets/extra-libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin_assets/dist/js/pages/datatable/datatable-basic.init.js') }}"></script>
    <script>
        $(function() {
            $('#dataTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true,
                fixedHeader: true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
                }
            })
            $(".checkboxes").click(function() {

                if ($(".checkboxes").length == $(".subscheked:checked").length) {
                    $(".chk_all").attr("checked", "checked");
                } else {
                    $(".chk_all").removeAttr("checked");
                }

            });

            $(".chk_all").click(function() {
                var checkAll = $(".chk_all").prop('checked');
                if (checkAll) {
                    $(".checkboxes").prop("checked", true);
                } else {
                    $(".checkboxes").prop("checked", false);
                }

            });
        })
    </script>
@endsection
