@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Create a New Fund Supported Pool</h4>
        <div class="flex-fill"></div>
    </div>
@endsection

@section('content')

<div class="card">
    {{-- <div class="card-header bg-light">
        <span class="text-dark font-weight-bold">Create New Pool</span>
    </div> --}}

    <div class="card-body">
        <form id="create_pool" action="{{ route("settings.fund-supported-pools.store") }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="region_id">Region</label>
                    {{-- <input type="email" class="form-control" id="inputEmail4" placeholder="Email"> --}}
                    <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror">
                        <option value="">Choose Region...</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}
                                >{{ $region->name }} ({{ $region->code }})</option>
                        @endforeach
                    </select>
                    <span id="region_id_errors">
                          @error('region_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                    </span>


                </div>
                <div class="form-group col-md-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                            id="start_date" value="{{ old('start_date') }}">
                  <span id="start_date_errors">
                       @error('start_date')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                </div>
                <div class="form-group col-md-2">
                    <label for="pool_status">Status</label>
                    <select id="pool_status" name="pool_status" class="form-control @error('pool_status') is-invalid @enderror">
                        <option value="A">Active</option>
                        <option value="I">Inactive</option>
                    </select>
                   <span id="pool_status_errors">
                       @error('pool_status')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                   </span>

                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <span class="text-dark font-weight-bold">Charity Information</span>
                </div>

                <div class="card-body">
                    <table class="table" id="fspools_table">
                        {{-- <thead>
                            <tr>
                                <th>Charity</th>
                                <th>Percentage</th>
                            </tr>
                        </thead> --}}
                        <tbody>
                            @foreach (old('charities', ['']) as $index => $oldCharity)
                            <tr id="charity{{ $index }}">
                                @include('admin-campaign.fund-supported-pools.partials.create-pool-charity', ['index' => $index, 'charity' => $oldCharity])
                            {{-- <tr>
                                <td>
                                    <select name="charities[]" class="form-control select2" >
                                        <option value="">-- choose product --</option>
                                         @foreach ($products as $product)
                                            <option value="{{ $product->id }}"{{ $oldProduct == $product->id ? ' selected' : '' }}>
                                                {{ $product->name }} (${{ number_format($product->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantities[]" class="form-control" value="{{ old('quantities.' . $index) ?? '1' }}" />
                                </td>
                            --}}
                            </tr>
                            @endforeach
                            <tr id="charity{{ count(old('charities', [''])) }}"></tr>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-12">
                            <button id="add_row" class="btn btn-primary pull-left">+ Add Row</button>
                            {{-- <button id='delete_row' class="pull-right btn btn-danger">- Delete Row</button> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <input class="btn btn-primary" type="submit" value="Save">
                <a class="btn btn-outline-primary"  href="{{ route('settings.fund-supported-pools.index') }}">Cancel</a>
            </div>
        </form>
<div id="persist_upload">

</div>

    </div>
</div>
@endsection

@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
    .select2 {
        width:100% !important;
    }
    .select2-selection--multiple{
        overflow: hidden !important;
        height: auto !important;
        min-height: 38px !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

</style>

@endpush

@push('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>


<script type="x-tmpl" id="charity-tmpl">
    @include('admin-campaign.fund-supported-pools.partials.create-pool-charity', ['index' => 'XXX'] )
</script>

<script>

    function loadFile(event) {
        $(event.target).parents("label").siblings("img").attr('src', URL.createObjectURL(event.target.files[0]));
        $(event.target).parents("label").siblings("img").css("display","block");
    }

    $(document).ready(function()
    {




        $("#create_pool").submit(function(e)
        {
            e.preventDefault();
            var form = document.getElementById("create_pool");
            var formData = new FormData();
            $("select[name='charities[]']").each(function(){
                formData.append('charities[]',     $(this).val());
            });
            formData.append('pool_status',   $('#pool_status').val());
            formData.append('region_id',   $('#region_id').val());
            $("select[name='status[]']").each(function(){
                formData.append('status[]',   $(this).val());
            });
            $("input[name='names[]']").each(function(){
                formData.append('names[]',  $(this).val());
            });
            $("textarea[name='descriptions[]']").each(function(){
                formData.append('descriptions[]',  $(this).val());
            });
            $("input[name='percentages[]']").each(function(){
                formData.append('percentages[]', $(this).val());
            });
            $("input[name='contact_names[]']").each(function(){
                formData.append('contact_names[]', $(this).val());
            });
            $("input[name='contact_titles[]']").each(function(){
                formData.append('contact_titles[]',  $(this).val());
            });
            $("input[name='notes[]']").each(function(){
                formData.append('notes[]', $(this).val());
            });
            $("input[name='contact_emails[]']").each(function(){
                formData.append('contact_emails[]',  $(this).val());
            });

            formData.append('start_date', $('#start_date').val());

            $("input[name='images[]']").each(function(){
                formData.append('images[]',  $(this)[0].files[0]);
            });

            $("#create_pool").fadeTo("slow",0.2);
            $.ajax({
                url: "{{ route("settings.fund-supported-pools.store") }}",
                type:"POST",
                data: formData,
                headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
                processData: false,
                cache: false,
                contentType: false,
                dataType: 'json',
                success:function(response){
                    $(".create_pool").fadeTo("slow",1);
                    $('.successMsg').show();
                    $('.region_id_errors').html("");
                    $('.start_date_errors').html("");
                    $('.pool_status_errors').html("");
                    $('.status_errors').html("");
                    $('.charities_errors').html("");
                    $('.names_errors').html("");
                    $('.descriptions_errors').html("");
                    $('.percentages_errors').html("");
                    $('.contact_names_errors').html("");
                    $('.contact_titles_errors').html("");
                    $('.contact_emails_errors').html("");
                    $('.images_errors').html("");
                    window.location = response[0];
                    console.log(response);
                },
                error: function(response) {
                    $("#create_pool").fadeTo("slow",1);
                    $('.region_id_errors').html("");
                    $('.start_date_errors').html("");
                    $('.pool_status_errors').html("");
                    $('.status_errors').html("");
                    $('.charities_errors').html("");
                    $('.names_errors').html("");
                    $('.descriptions_errors').html("");
                    $('.percentages_errors').html("");
                    $('.contact_names_errors').html("");
                    $('.contact_titles_errors').html("");
                    $('.contact_emails_errors').html("");
                    $('.images_errors').html("");

                    if(response.responseJSON.errors){
                        errors = response.responseJSON.errors;
                        for(const prop in response.responseJSON.errors){
                            count = prop.substring(prop.indexOf(".")+1);
                            tag = prop.substring(0,prop.indexOf("."))
                            $("#charity"+count).find("."+tag+"_errors").html('<span class="invalid-feedback">'+errors[prop]+'</span>')
                            $("#" + prop + "_errors").html('<span class="invalid-feedback">'+errors[prop]+'</span>')
                        }
                    }
                    $(".invalid-feedback").css("display","block");
                },
            });

        });
    });

$(function() {



    //function to initialize select2
    function initializeSelect2(selectElementObj) {
        selectElementObj.select2({
            placeholder: 'select charity',
            allowClear: true,
            ajax: {
                url: '/settings/fund-supported-pools/charities'
                , dataType: 'json'
                , delay: 250
                , data: function(params) {
                    var query = {
                        'q': params.term
                    , }
                    return query;
                }
                , processResults: function(data) {
                    return {
                        results: data
                        };
                }
                , cache: false
            }
        });
    }

    //onload: call the above function
    $("select[name='charities[]']").each(function() {
        initializeSelect2($(this));
    });

    // variable for keep track lines
    let row_number = {{ count(old('charities', [''])) }};

    $("#add_row").click(function(e){
        e.preventDefault();
        let new_row_number = row_number - 1;

        text = $("#charity-tmpl").html();
        text = text.replace(/XXX/g, row_number);
        text = text.replace(/YYY/g, row_number + 1);
        $('#charity' + row_number).html( text );

        // Initialize select2 on new add row
        $('#charity' + row_number).find("select[name='charities[]']").each(function() {
            initializeSelect2($(this));
        });

        // $('#charity' + row_number).html($('#charity' + new_row_number).html()).find('td:first-child');
        $('#fspools_table').append('<tr id="charity' + (row_number + 1) + '"></tr>');
        row_number++;

    });

    $(document).on("click", "div.delete_this_row" , function(e) {
        e.preventDefault();

        Swal.fire({
            text: 'Are you sure to delete this line ?'  ,
            // icon: 'question'
            //showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            //denyButtonText: `Don't save`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                // Swal.fire('Saved!', '', '')
                //$(this).parent().parent().parent().parent().remove();
                el = '#' + $(this).attr('data-id');
                $(el).remove();

            }
        })
    });


    // $("#delete_row").click(function(e){
    //     e.preventDefault();
    //     if(row_number > 1){
    //         $("#charity" + (row_number - 1)).html('');
    //         row_number--;
    //     }
    // });


});

</script>

@endpush
