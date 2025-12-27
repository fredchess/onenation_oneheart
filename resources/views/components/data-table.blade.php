<table class="table no-wrap v-middle mb-0" id="dataTable">
    <thead>
        <tr class="border-0">
            <th class="font-14 font-weight-medium text-muted">
                <label class="custom-control custom-checkbox be-select-all">
                    <input class="custom-control-input chk_all" type="checkbox" name="chk_all"><span
                        class="custom-control-label"></span>
                </label>
            </th>
            <th class="font-14 font-weight-medium text-muted">Client</th>
            <th class="font-14 font-weight-medium text-muted">Message</th>
            <th class="font-14 font-weight-medium text-muted">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($avis->reverse() as $avis)

        <tr>
            <td>
                <label class="custom-control custom-checkbox">
                    <input class="custom-control-input checkboxes" type="checkbox" value="{{$avis->id}}" name="ids[]"
                        id="check{{$avis->id}}"><span class="custom-control-label"></span>
                </label>
            </td>
            <td>
                @if($avis->client != null)
                <a href="{{route('clients.show', $avis->client->id)}}">
                    {{$avis->client->firstname}} {{$avis->client->surname}}
                </a>
                @else
                No existing customer
                @endif
            </td>
            <td>{{$avis->note}}</td>
            <td>{{$avis->message}}</td>
            <td>

                @php
                $entreprise = $avis->avisable_type::find($avis->avisable_id)
                @endphp
                {{$entreprise->nom}}

            </td>
            <td>
                <a type="button" class="btn btn-primary" href={{ route("avis.edit", $avis) }}><i
                        class="ti-pencil c-orange-700"></i></a>
                </a>
                <button type="button"
                    onclick="document.querySelector('#check{{$avis->id}}').setAttribute('checked', 'checked');(confirm('Êtes-vous sûr de vouloir supprimer cet(s) avis ?')? document.deleteForm.submit():console.log('annulé'));"
                    class="btn btn-danger">
                    <span class=""><i class="ti-trash c-red-600"></i></span>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>