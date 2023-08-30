@extends('adminlte::page')

@section('content_header')
    
     {{-- <h2>Reporting</h2> --}}
     @include('system-security.partials.tabs')

     <div class="d-flex mt-3">
         <h4>Announcement</h4>
     </div>

@endsection
@section('content')

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }} 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('system.announcement.store') }}" class="form form-horizontal">               
        @csrf

            <div class="form-row">
                <div class="form-group col-md-11">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title') ? old('title') : $announcement->title }}" />
                    @error('title')
                    <span class="invalid-feedback d-block">
                        {{  $message  }}
                    </span>
                    @enderror
                </div> 
            </div>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="status">Status</label>
                    <select class="form-control" name="status">
                        <option value="A" {{ old('status') ? (old('status') == 'A' ? 'selected' : '') : ($announcement->status == 'A' ? 'selected' : '') }}>Active</option>
                        <option value="I" {{ old('status') ? (old('status') == 'I' ? 'selected' : '') : ($announcement->status == 'I' ? 'selected' : '') }}>Inactive</option>
                    </select>
                    @error('status')
                    <span class="invalid-feedback d-block">
                        {{  $message  }}
                    </span>
                    @enderror

                </div>
                <div class="form-group col-md-2">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control" name="start_date"
                        value="{{ old('start_date') ? old('start_date') : $announcement->start_date }}" />
                    @error('start_date')
                    <span class="invalid-feedback d-block">
                        {{  $message  }}
                    </span>
                    @enderror

                </div>
                <div class="form-group col-md-2">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control" name="end_date"
                        value="{{ old('end_date') ? old('end_date') : $announcement->end_date }}" />
                    @error('end_date')
                    <span class="invalid-feedback d-block">
                        {{  $message  }}
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                </div>
                <div class="col-md-1 pt-5 text-right">
                    <button type="button" class="preview-btn btn btn-info">Preview</button>
                </div>
            </div>

            <div class="form-row ">      
                <div class="form-group col-md-11">
                    <label>Body</label>
                    <textarea class="form-control editor" name="body" rows="8">{{ old('body') ? old('body') : $announcement->body }}</textarea> 
                    @error('body')
                    <span class="invalid-feedback d-block">
                        {{  $message  }}
                    </span>
                    @enderror
                </div>  
            </div>
            
            <div class="form-row">      
                <div class="form-group">
                    <label></label>
                    <input type="submit" value="Save" class="btn btn-primary"/>
                </div> 
            </div>
        </form>             

    </div>
</div>   

@include('system-security.announcements.partials.modal')



@endsection

@push('css')

<link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
    .ck-editor__editable_inline {
        min-height: 300px;
        /* max-width: 1100px; */
    }

</style>
   
@endpush

@push('js')

<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
<script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}" ></script>

<script>

$(function() {    

    window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);


    ClassicEditor
        .create( document.querySelector( '.editor' ),{
            licenseKey: '',
            ckfinder: {
                uploadUrl: '{{route('system.announcement.image.upload').'?_token='.csrf_token()}}',
            }
        })
        .then( editor => {
                window.editor = editor;
            } )
        .catch( error => {
            console.error( 'Oops, something went wrong!' );
            console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
            console.warn( 'Build id: lu84jocs3y82-nohdljl880ze' );
            console.error( error );
        } );

    ClassicEditor
        .create( document.querySelector( '.announcement_preview' ),{
            licenseKey: '',
        })
        .then( editor => {
            window.preview = editor;
            const toolbarElement = editor.ui.view.toolbar.element;
            
            editor.enableReadOnlyMode( 'my-feature-id' );
            toolbarElement.style.display = 'none';
        })
        .catch( error => {
            console.error( 'Oops, something went wrong!' );
            console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
            console.warn( 'Build id: lu84jocs3y82-nohdljl880ze' );
            console.error( error );
        } );    

    $(document).on("click", ".preview-btn" , function(e) {

        title = $('input[name="title"]').val(); 
        $('h5.modal-title').html( title );

        content = editor.getData();
        preview.setData( content );

        $('#announcementModal').modal('show');

    });


});    



</script> 
    
@endpush
