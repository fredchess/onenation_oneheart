 <div class="form-body">
     <div class="row">
         @foreach ($fields as $field)
             @if($field['field_name'] == 'description')
                 <div class="col-md-4 col-lg-2">
                     <label>{{ $field['name'] }} {{ isset($field['required']) ? '*' : '' }}</label>
                 </div>
                 <div class="col-md-8 col-lg-10 form-group">
                     <textarea class="form-control" name="description" id="description" required placeholder="{{ $field['placeholder'] ?? "" }}">
                         {{ $field['value'] ?? "" }}
                     </textarea>
                 </div>
                 @else
                     <div class="col-md-4 col-lg-2">
                         <label>{{ $field['name'] }} {{ isset($field['required']) ? '*' : '' }}</label>
                     </div>
                     <div class="col-md-8 col-lg-10 form-group">
                         <input type="{{ $field['type'] }}" id="{{ $field['field_name'] }}" class="form-control"
                                name="{{ $field['field_name'] }}" placeholder="{{ $field['placeholder'] }}" value="{{ $field['value'] ?? "" }}"
                             {{ isset($field['required']) ? 'required' : '' }}>
                     </div>
             @endif
         @endforeach

         <div class="form-group row mb-5">
             <div class="col-md-4 col-lg-2">
                 <label>Image du partenaire</label>
             </div>
             <div class="col-md-8 col-lg-10">
                 <input type="file" class="image-preview-filepond-" name="image">
             </div>
         </div>

         @if(isset($partner))
             <div class="form-group row mb-5">
                 <div class="col-md-4 col-lg-2">
                     <label>Image actuelle</label>
                 </div>
                 <div class="image">
                     {{ $partner->getFirstMedia('images') }}
                 </div>
             </div>
         @endif


         <div class="col-sm-12 d-flex justify-content-end">
             <a href="{{ route('orphanages.index') }}" class="btn btn-light-secondary me-1 mb-1">Retour</a>
             <button type="submit" class="btn btn-primary me-1 mb-1">Enregistrer</button>
         </div>
     </div>
 </div>

 <style>
     .image {
         height: 200px;
         width: 200px;
     }
     .image img {
         height: 100%;
     }
 </style>

@section('scripts')

<!-- TinyMCE -->
<script src="{{asset("admin_assets/vendors/tinymce/tinymce.min.js")}}"></script>
<script src="{{asset("admin_assets/vendors/tinymce/plugins/code/plugin.min.js")}}"></script>
<script>
    // EDITEUR DE TEXTE
     tinymce.init({
            selector: '#description',
            plugins: "image code",
            toolbar: 'undo image redo styleselect bold italic alignleft aligncenter alignright bullist numlist outdent indent code',
            a11y_advanced_options: true
        });
</script>

@endsection
