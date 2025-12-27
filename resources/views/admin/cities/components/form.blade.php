 <div class="form-body">
     <div class="row">
         @foreach ($fields as $field)
             <div class="col-md-4 col-lg-2">
                 <label>{{ $field['name'] }} {{ isset($field['required']) ? '*' : '' }}</label>
             </div>
             <div class="col-md-8 col-lg-10 form-group">
                 <input type="{{ $field['type'] }}" id="{{ $field['field_name'] }}" class="form-control"
                     name="{{ $field['field_name'] }}" placeholder="{{ $field['placeholder'] }}" value="{{ $field['value'] ?? "" }}"
                     {{ isset($field['required']) ? 'required' : '' }}>
             </div>
         @endforeach


         <div class="col-sm-12 d-flex justify-content-end">
             <a href="{{ route('orphanages.index') }}" class="btn btn-light-secondary me-1 mb-1">Retour</a>
             <button type="submit" class="btn btn-primary me-1 mb-1">Enregistrer</button>
         </div>
     </div>
 </div>
