@extends('adminlte::page')

@section('content_header')
    
    @include('system-security.partials.tabs')

@endsection
@section('content')

    <div class="d-flex p-2 ">
        <h4>Upload File</h4>
        <div class="flex-fill"></div>
    </div>

    @if ($message = Session::get('success'))
    <div class="mx-1 my-2">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form id="upload-form" action="{{ route("system.upload-files.store") }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <h6 class="text-primary font-weight-bold pb-3">Upload seed file in json format.</h6>

                <div class="form-group row">
                    <label for="location" class="col-sm-2 col-form-label col-form-label-sm">File location :</label>
                    <div class="col-sm-10">
                        <select class="form-control col-6" name="location" id="location" 
                                {{ (env('APP_ENV') == 'prod') ? 'disabled' : ''}} >
                            <option value="0" {{ $location == 0 ? 'selected' : ''}}>storage/app/uploads</option>
                        @if (env('APP_ENV') <> 'prod')
                            <option value="1" {{ $location == 1 ? 'selected' : ''}}>public/img/uploads/_adminer</option>
                        @endif
                        </select>
                    </div>
                </div>
               

                <div class="form-row">
                    <div class="form-group col-md-10">
                        <div class="file-upload">
                            <div class="file-select">
                                <div class="file-select-button" id="fileName">Choose File</div>
                                <div class="file-select-name" id="noFile">No file chosen...</div>
                                <input type="file" accept=".json" name="uploaded_file" id="uploaded_file">
                            </div>
                        </div>
                        <span class="uploaded_file_error is-invalid">
                            @error( 'uploaded_file' )
                                <span class="invalid-feedback" style="display:block;">{{ $message }}</span>
                            @enderror
                        </span>
                    </div>

                    <div class="col-md-1" id="remove-upload-area" style="display: none;">
                        <div class="pt-1"><button id="remove-upload-file" class="btn btn-danger">
                            <i class="fas fa-trash-alt fa-lg"></i></button></div>
                    </div>

                </div>

                <div class="form-row pt-3">
                    <div class="form-group col-md-6 float-right">
                        <input class="btn btn-outline-secondary" id="cancel-btn" type="button" value="Cancel">
                        <input class="btn btn-primary " type="submit" value="Submit">
                    </div>
                </div>

            </form>

        </div>
    </div>
    <div class="card">
      
        <div class="card-body">

            <table class="table  table-bordered">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">File name</th>
                    <th scope="col" class="text-right">File size (in KB)</th>
                    <th scope="col">Last Modified</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($files as $index => $file)
                    <tr>
                        <th scope="row">{{ $index + 1}}</th>
                        <td><a href="{{ route('system.upload-files.show', $location .'_'. $file->getFilename()) }}"> {{ $file->getFilename() }}</a></td>
                        <td class="text-right">{{ number_format(round($file->getSize() / 1024,0),0) }}</td>
                        <td>{{ $file->last_modified }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

    </div>


</div>
@endsection



@push('css')

    <style>

.file-upload {display:block;text-align:center;font-family: Helvetica, Arial, sans-serif;font-size: 12px;}
.file-upload .file-select{display:block;border: 2px solid #dce4ec;color: #34495e;cursor:pointer;height:40px;line-height:40px;text-align:left;background:#FFFFFF;overflow:hidden;position:relative;}
.file-upload .file-select .file-select-button{background:#dce4ec;padding:0 10px;display:inline-block;height:40px;line-height:40px;}
.file-upload .file-select .file-select-name{line-height:40px;display:inline-block;padding:0 10px;}
.file-upload .file-select:hover{border-color:#34495e;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload .file-select:hover .file-select-button{background:#34495e;color:#FFFFFF;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload.active .file-select{border-color:#3fa46a;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload.active .file-select .file-select-button{background:#3fa46a;color:#FFFFFF;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload .file-select input[type=file]{z-index:100;cursor:pointer;position:absolute;height:100%;width:100%;top:0;left:0;opacity:0;filter:alpha(opacity=0);}
.file-upload .file-select.file-select-disabled{opacity:0.65;}
.file-upload .file-select.file-select-disabled:hover{cursor:default;display:block;border: 2px solid #dce4ec;color: #34495e;cursor:pointer;height:40px;line-height:40px;margin-top:5px;text-align:left;background:#FFFFFF;overflow:hidden;position:relative;}
.file-upload .file-select.file-select-disabled:hover .file-select-button{background:#dce4ec;color:#666666;padding:0 10px;display:inline-block;height:40px;line-height:40px;}
.file-upload .file-select.file-select-disabled:hover .file-select-name{line-height:40px;display:inline-block;padding:0 10px;}

    </style>
@endpush

@push('js')
   
    <script>

    $(function() {

  
        // Functions for handling the upload file
        $('#uploaded_file').bind('change', function () {
            var filename = $("#uploaded_file").val();
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#noFile").text("No file chosen...");

                $('.uploaded_file_error').html();
            }
            else {
                $(".file-upload").addClass('active');
                $("#noFile").text(filename.replace("C:\\fakepath\\", ""));

                $('#remove-upload-area').show();
            }
        });

        $(document).on("click", "#remove-upload-file, #cancel-btn" , function(e) {
            e.preventDefault();
            $("input[name='uploaded_file']").val(null);
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
            $('#remove-upload-area').hide();

        });

        $(document).on("change", "#location" , function(e) {
            window.location.href = '/system/upload-files?location=' + $('#location').val(); 
        });

    });

    </script>
@endpush
