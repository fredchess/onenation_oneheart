@extends('admin.layouts.app')
{{-- Titre de la page --}}
@section('title')
    Villes
@endsection

@section('subtitle', 'Ajoutez une ville ici.')

@section('css')
    <link rel="stylesheet" href="{{ asset('admin_assets/vendors/toastify/toastify.css') }}">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Création d'une ville</h4>
                <small>Tous les champs avec (*) sont obligatoires</small>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form class="form form-horizontal" method="POST" action="{{ route('cities.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @include("admin.cities.components.form", ["fields" => $fields])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- filepond validation -->
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <!-- image editor -->
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-filter/dist/filepond-plugin-image-filter.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>

    <!-- toastify -->
    <script src="{{ asset('admin_assets/vendors/toastify/toastify.js') }}"></script>
    <!-- TinyMCE -->
    <script src="{{ asset('admin_assets/vendors/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendors/tinymce/plugins/code/plugin.min.js') }}"></script>
    <!-- filepond -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        // EDITEUR DE TEXTE
         tinymce.init({
            selector: '#public_content',
            plugins: "image code",
            toolbar: 'undo image redo styleselect bold italic alignleft aligncenter alignright bullist numlist outdent indent code',
            a11y_advanced_options: true
        });

        // Filepond: Multiple Files
        FilePond.create(document.querySelector('.image-preview-filepond'), {
            allowMultiple: true,
            allowFileEncode: false,
            required: false,
            allowImagePreview: true,
        });
    </script>
@endsection
