<div class="container">

    @if ($message = Session::get('success'))
    <div class="mt-4 mb-0 alert alert-success alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <strong><?php echo $message ?></strong>
    </div>
    @endif
    @if ($message = Session::get('secondary'))
    <div class="mt-4 mb-0 alert alert-secondary alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <strong><?php echo $message ?></strong>
    </div>
    @endif
    @if ($message = Session::get('info'))
    <div class="mt-4 mb-0 alert alert-info alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <strong><?php echo $message ?></strong>
    </div>
    @endif

    @if (count($errors)>0)
    <div class="mt-4 mb-0 alert alert-danger alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <ul>
            @foreach($errors->all() as $error)
            <li><?php echo $error ?></li>
            @endforeach
        </ul>
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="mt-4 mb-0 alert alert-danger alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <strong><?php echo $message ?></strong>
    </div>
    @endif


    @if ($message = Session::get('warning'))
    <div class="mt-4 mb-0 alert alert-warning alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <strong><?php echo $message ?></strong>
    </div>
    @endif


    @if ($message = Session::get('info'))
    <div class="mt-4 mb-0 alert alert-info alert-block">
        {{-- <button type="button" class="btn close" data-dismiss="alert">×</button> --}}
        <strong><?php echo $message ?></strong>
    </div>
    @endif


    {{--
@if ($errors->any())
<div class="alert alert-danger">
	<button type="button" class="btn close" data-dismiss="alert">×</button>
	Please check the form below for errors
</div>
@endif --}}

</div>
