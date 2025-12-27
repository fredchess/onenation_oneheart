 <div class="form-body">
     <div class="row">
         @foreach ($fields as $field)
             <div class="col-md-4 col-lg-2">
                 <label>{{ $field['name'] }} {{ isset($field['required']) ? '*' : '' }}</label>
             </div>
             <div class="col-md-8 col-lg-10 form-group">
                 <input type="{{ $field['type'] }}" id="{{ $field['field_name'] }}" class="form-control"
                     name="{{ $field['field_name'] }}" placeholder="{{ $field['placeholder'] }}"
                     value="{{ $field['value'] ?? '' }}" {{ isset($field['required']) ? 'required' : '' }}>
             </div>
         @endforeach


         <div class="col-md-4 col-lg-2">
             <label>Mini description</label>
         </div>
         <div class="col-md-8 col-lg-10 form-group">
             <textarea name="description" placeholder="mini description (1 ou 2 lignes)" id="" cols="30" rows="3"
                 class="form-control">{{ isset($user) ? $user->datas['description'] ?? '' : '' }}</textarea>
         </div>
         <div class="col-12 col-md-8 offset-md-4 col-lg-10 offset-lg-2 form-group">
             <div class="form-check">
                 <div class="checkbox">
                     <input type="checkbox" id="checkbox1" class="form-check-input" name="visible"
                         {{ isset($user) && isset($user->datas['visible']) ? ($user->datas['visible'] == 1 ? 'checked' : '') : '' }}>
                     <label for="checkbox1">L'utilisateur est visible sur le site ? </label>
                 </div>
             </div>
         </div>

         <div class="col-md-4 col-lg-2">
             <label>Photo de l'utilisateur</label>
         </div>
         <div class="col-md-8 col-lg-10 form-group">
             <!-- File uploader with multiple files upload -->
             <input type="file" class="image-preview-filepond-" name="file">
             @if (isset($user))
                 <br>
                 <br>
                 <img src="{{ $user->getFirstMediaUrl() }}" style="height: 150px" alt="">
             @endif
         </div>

         <div class="col-sm-12 d-flex justify-content-end">
             <a href="{{ route('users.index') }}" class="btn btn-light-secondary me-1 mb-1">Retour</a>
             <button type="submit" class="btn btn-primary me-1 mb-1">Enregistrer</button>
         </div>
     </div>
 </div>
