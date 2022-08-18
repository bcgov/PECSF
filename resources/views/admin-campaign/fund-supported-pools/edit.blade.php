@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Edit Fund Supported Pool</h4>
        <div class="flex-fill"></div>
    </div>
@endsection

@section('content')

<div class="card">
    {{-- <div class="card-header bg-light">
        <span class="text-dark font-weight-bold">Edit an existing Fund Supported Pool</span>
    </div> --}}

    <div class="card-body">
        <form action="{{ route("settings.fund-supported-pools.update", $pool->id) }}" method="POST" 
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="region_id" class="form-control" 
                id="region_id" value="{{ old('region_id', $pool->region_id) }}" readonly>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="region">Region</label>
                    {{-- <input type="email" class="form-control" id="inputEmail4" placeholder="Email"> --}}
                    <input type="text" name="region" class="form-control" 
                            id="region" value="{{ old('region', $pool->region->name) }}" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="startd_date">Start Date</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                            id="start_date" value="{{ old('start_date', $pool->start_date) }}">
                    @error('start_date')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="pool_status">Status</label>
                    <select name="pool_status" class="form-control @error('pool_status') is-invalid @enderror">
                        <option value="A" {{ old('pool_status', $pool->status) == 'A' ? 'selected' : '' }}>Active</option>
                        <option value="I" {{ old('pool_status', $pool->status) == 'I' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('pool_status')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                </div>
            </div>

            <div class="card">
                <div class="px-3 py-2 bg-light container-fluid">
                    <div class="row ">
                        <div class="col-md-10">
                            <h6 class="text-dark font-weight-bold">Charity Information</h6>
                        </div>
                        <div class="col-md-2 float-right">
                            <div class="accordion-option">
                                <a href="javascript:void(0)" class="toggle-accordion text-sm" 
                                accordion-id="#accordion"></a>
                            </div>
                          {{-- <button class="btn btn-primary" style="margin-left: 1em" 
                    (click)="onAddCategoieModal(content)">Edit</button> --}}
                         </div>
                      </div>
                    
                    {{-- <div class="p-0">
                        
                    </div> --}}
                </div>

                <div class="card-body ">
                    <table class="table" id="fspools_table">
                        {{-- <thead>
                            <tr>
                                <th>Charity</th>
                                <th>Percentage</th>
                            </tr>
                        </thead> --}}
                        <tbody id="accordion">
                            @foreach (old('charities', $pool->charities->pluck('charity_id') ) as $index => $oldCharity)
                            <tr id="charity{{ $index }}">
                                @if (count($errors) == 0)
                                    @php ( $pool_charity = $pool->charities[$index] )                                    
                                @else
                                    @php ( $pool_charity = new \App\Models\FSPoolCharity )                                    
                                @endif
                                @include('admin-campaign.fund-supported-pools.partials.edit-pool-charity', ['index'=> $index, 'charity' => $oldCharity, 'pool_charity' => $pool_charity ])
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
                            <tr id="charity{{ count(old('charities', $pool->charities->pluck('charity_id') )) }}"></tr>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-12">
                            <button id="add_row" class="btn btn-success pull-left">+ Add Row</button>
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


    </div>
</div>
@endsection

@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
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


    [data-toggle="collapse"] .fa:before {  
        /* font-family: 'FontAwesome';   */
        /* content: "\f103"; */
          /* content: "\f139"; */
          content: "\f078"; 
    }

    [data-toggle="collapse"].collapsed .fa:before {
        /* content: "\f13a"; */
        /* content: "\f102";  */
        /* content: "\f077";  */
        content: "\f053"; 
    }

    /* .accordion-option {
        width: 100%;
        float: left;
        clear: both;
        margin: 15px 0;
    } */
    /* .accordion-option .title {
        font-size: 20px;
        font-weight: bold;
        float: left;
        padding: 0;
        margin: 0;
    } */
    .accordion-option .toggle-accordion {
        float: right;
        font-size: 16px;
        color: #6a6c6f;
    }
    .accordion-option .toggle-accordion:before {
        content: "Expand All";
    }
    .accordion-option .toggle-accordion.active:before {
        content: "Collapse All";
    }

</style>

@endpush

@push('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>


<script type="x-tmpl" id="charity-tmpl">
    @php ( $pool_charity = new \App\Models\FSPoolCharity ) 
    @include('admin-campaign.fund-supported-pools.partials.edit-pool-charity', ['index' => 'XXX', 'pool_charity' => $pool_charity] )
</script>

<script>

$(function() {    

    //function to initialize select2
    function initializeSelect2(selectElementObj) {
        selectElementObj.select2({
            placeholder:  'Select charity',
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

        $(selectElementObj).on('select2:clear', function (e) {
            // when clear the selection in select2, need to update option for null selection
            console.log('clearing called');
            console.log( $(this) );
            
            // $(this).append('<option value selected>select 555<option>');
            
            if ( $(this).find('option[value=""]').length > 0 ) {
                $(this).find('option[value=""]').prop("selected", true);
            } else {
                $(this).append(new Option("Select charity", "", true, true));
            }
            console.log( $(this).find('option[value=""]').text() );
            console.log( $(this).placeholder );
            // $(this).val('').trigger('change');
        });

    }


    //onload: call the above function 
    $("select[name='charities[]']").each(function() {
        initializeSelect2($(this));
    });

    // variable for keep track lines 
    let row_number = {{ count(old('charities', $pool->charities->pluck('charity_id') )) }};

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
            text: 'Are you sure you want to remove this charity ?' ,
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


    $(".toggle-accordion").on("click", function() {
        var accordionId = $(this).attr("accordion-id"),
        numPanelOpen = $(accordionId + ' .collapse.show').length;
        console.log( accordionId );
        // $(this).toggleClass("active");

        if (numPanelOpen == 0) {
            // openAllPanels(accordionId);
            $(accordionId + ' .collapse:not(".show")').collapse('show');
            $(this).addClass("active");
            
        } else {
        // closeAllPanels(accordionId);
            $(accordionId + ' .collapse.show').collapse('hide');
            $(this).removeClass("active");
            
        }
    })

    // openAllPanels = function(aId) {
    //     console.log("setAllPanelOpen");

    // }

    // closeAllPanels = function(aId) {
    //     console.log("setAllPanelclose");
    //     $(aId + ' .collapse.show').collapse('hide');
    // }

    $("#accordion").on('shown.bs.collapse', function () {
        // do something
        el = $('a.toggle-accordion');
        if ( !el.hasClass("active")) {
            el.addClass( "active");
        }
    });

    $("#accordion").on('hidden.bs.collapse', function () {

        count = $('#accordion .collapse.show').length;
        if (count == 0) {
            el = $('a.toggle-accordion');
            if ( el.hasClass("active")) {
                el.removeClass( "active");
            }
        }
    });
     

    $('.collapse').each( function (index) { if  ($(this).find('.invalid-feedback').length > 0 ) { $(this).collapse('show') }  });


});

</script>

@endpush