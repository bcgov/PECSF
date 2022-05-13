
<!-- Modal (Charity detail) -->
<div class="modal fade" id="charity-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-charity-name" id="charity-modal-label">Charity Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <!-- content will be load here -->                          
            <div id="charity-modal-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12 offset-md-0">
            <div id="search">
                                      
                <form id="charity-searchform" action="{{route('donate.save.select')}}" method="post">
                  @csrf
                  <div class="input-group">
                        {{-- <label class="text-muted">Search by name and address</label> --}}
                        <span class="position-relative w-100">
                          <input id="charity-name" type="search" name="title" value="{{request()->get('charity-name','')}}" 
                               class="form-control bg-white" placeholder="Search for your CRA Charity"/>
                          <small id="charity-name-error" class="text-danger">
                              {{ $errors->first('id') }}
                          </small>
                        </span>
                  </div>

                  <div id="search-charity" class="position-relative pb-3"  >

                    <div id='search-filter' class="position-absolute w-100 p-2 bg-white" style="display:none; min-height:300px;"> 
                        
                      <div class="d-flex bd-highlight">                   
                        
                        <div class="mr-auto bd-highlight">

                            <a data-toggle="collapse" href="#collapseCharityFilter" role="button" aria-expanded="false" aria-controls="collapseCharityFilter" class="XXXadvanced">
                              <small>
                              Advance Search With Filters <i class="fa fa-angle-down"></i> 
                              </small>
                            </a>
                            <span class="pl-5"> 
                              <small>
                              <span id="filter-count">0</span> selected | 
                              <span id="filter-reset"><a href="#" class="btn-link text-secondary" id="filter-reset-button">Reset <i class="fa fa-times"></i></a></span>
                              </small>
                            </span>   
                        </div>
                        
                        <span>
                          <button type="button" class="btn btn-primary btn-sm search-clear">
                            <i class="fas fa-times"></i>&nbsp;&nbsp;Close
                          </button>
                        </span>
                      </div>
                
                      <div class="collapse border-0" id="collapseCharityFilter">
                          <div class="card pt-3" style="box-shadow:none;">
                              <div class="row">
                                  <div class="col-md-4"> 
                                    <select  class="form-control input-sm refresh_search_result" id="designation_code" name="designation_code">
                                      <option value="">Select Designation</option>
                                      @foreach ($designation_list as $key => $value)
                                        <option value="{{ $key }}" {{ old('designation_code') == $value ?? 'selected'}}>{{ $value }}</option>
                                      @endforeach
                                    </select>     
                                    </div>
                                    <div class="col-md-4"> 
                                      <select  class="form-control input-sm refresh_search_result" id="category_code" name="category_code">
                                        <option value="">Select Category</option>
                                        @foreach ($category_list as $key => $value)
                                          <option value="{{ $key }}" {{ old('category_code') == $value ?? 'selected'}}>{{ $value }}</option>
                                        @endforeach
                                      </select>     
                                    </div>
                                    <div class="col-md-4"> 
                                      <select  class="form-control input-sm refresh_search_result" id="province" name="province">

                                        <option value="" {{  old('province') ? '' : 'selected' }}>Select Province</option>
                                        @foreach ($province_list as $key => $value)
                                          <option value="{{ $key }}" {{ old('province') == $value ?? 'selected'}}>{{ $value }}</option>
                                        @endforeach
                                      </select>     

                                    </div>
                              </div>
                          </div>
                      </div>

                      <div class="mt-2 fas fa-spinner fa-spin fa-3x fa-fw loading-spinner" role="status" style="display:none">
                        <span class="sr-only">Loading...</span>
                      </div>
                      <div class= "container-fluid p-0 m-0 ">  
                        <div id="pagination_data" 
                            style="min-heightXXX: 320px; ">                  
                          @include("donate.partials.charity-pagination",["charities"=>$charities])
                      </div>
                    </div>
                  </div>
                </div>

                <div class="m-2 py-2 pl-2 bg-light"> 
                  <div id="selected-charity-list">
                  </div>
                </div>


                <div class="mt-2">
                  <button  name="cancel" value='cancel' class="btn btn-lg btn-outline-primary">Cancel</button>
                  <button class="btn btn-lg btn-primary" type="submit">Next</button>
                </div>
            </form>    
            </div>
        </div>        
    </div>
</div>


@push('css')

@endpush

@push('js')


<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script type="x-tmpl" id="charity-tmpl">
  <div class="m-1 bg-light">
      <div class="charity pl-2 m-0">
          <b>${this.text}</b>
          <button type="button" class="btn btn-light float-end clear-charity" data-id="${this.id}">&times;</button>
      </div>
      <label class="w-100">
          <input type="hidden" name="id[]" value="${this.id}">
          <input type="text" name="additional[]" class="form-control form-control-sm additional-text" data-id="${this.id}" value="${this.additional}" placeholder="If you have specific community or initiative in mind, enter it here.">
      </label>
  </div>
  <hr class="m-0 p-0"/>
</script>
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $(function () {
        let selectedCharity = {!!json_encode($selected_charities)!!};
        const $select = $("select");
        const tmplCharity = $("#charity-tmpl").html();

        const tmplParse = function (templateString, templateVars) {
            return new Function("return `" + templateString + "`;").call(templateVars);
        };
        renderSelectedCharity();

        // fuctions related to selected charity charity-choices                 
        $(document).on("click", ".charity-select-add" , function(e) {
            e.preventDefault();
            //console.log( 'Loop :' + $(this).attr('value') + ' ' + $(this).attr('value-text') );                        

            if (selectedCharity.findIndex((c) => c.id === parseInt($(this).val())) === -1) {
                selectedCharity.push({
                  id: parseInt($(this).attr('value') ),
                  text: $(this).attr('value-text'),
                  additional: ''
                });

              //$select.empty().trigger('change');
              renderSelectedCharity();
              $('#search-filter').hide();
              $('#charity-name').val('');
            }
        });

        function renderSelectedCharity() {

          $list = $("#selected-charity-list");
          $list.html("");
          for (charity of selectedCharity.reverse()) {
            $list.append(tmplParse(tmplCharity, charity));
          }
        }      

        $(document).on('click', '.clear-charity', function (e) {
          const idToRemove = $(this).data("id")
          selectedCharity = selectedCharity.filter((charity) => charity.id != idToRemove);
          $.post("/donate/remove",
              {
                  charity_id: idToRemove
              },
              function (data, status) {
                  console.log("Data: " + data + "\nStatus: " + status);
              });
          renderSelectedCharity();
        });

        $(document).on('change', '.additional-text', function () {
          const idToUpdate = $(this).data("id");
          const charity = selectedCharity.find((charity) => charity.id == idToUpdate);
          if (charity) {
              charity.additional = $(this).val();
          }
        });
           
        // ------ Autocomplete Search --------

        $(document).on("click", "#pagination a,#search_btn", function(e) {
          e.preventDefault();
          //get url and make final url for ajax 
          var url = $(this).attr("href");
          var append = url.indexOf("?") == -1 ? "?" : "&";
          var finalURL = url + append + $("#charity-searchform").serialize();

          //set to current url
          //window.history.pushState({}, null, finalURL);
          $.get(finalURL, function(data) {
            $("#pagination_data").html(data);
          });
          return false;
        })

        $('.refresh_search_result').change(function () {
          updateFilterCount();
          ajax_search_charities();
        });

        $('#filter-reset-button').click(function () {
            $("#designation_code").val('');
            $("#category_code").val('');
            $("#province").val('');
            updateFilterCount();
            ajax_search_charities();
        });

        function updateFilterCount() {
          var count = 0;
          if ( $("#designation_code").val() != '' ) { count += 1; }
          if ( $("#category_code").val() != '' ) { count += 1; }
          if ( $("#province").val() != '' ) { count += 1; }

          $('#filter-count').html(count);      

          if (count ==0 ) {
            $('#filter-reset-button').addClass('disabled');
          } else {
            $('#filter-reset-button').removeClass('disabled');
          }
        }

        function resetfilterValues() {
            $('#charity-name').val('');
            $("#designation_code").val('');
            $("#category_code").val('');
            $("#province").val('');
            updateFilterCount();
        }
        
        $('.search-clear').click(function () {
            $("#search-filter").slideUp( "slow", function() {
                // Animation complete.
                resetfilterValues();
            });
        });

        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });

        $('#charity-name').on('search', delay(function (e) {
              ajax_search_charities();
        }, 250));

        $('#charity-name').keyup(delay(function (e) {
          
          $('#charity-name-error').html('');
          if ($(this).val().length > 0 ) {
            ajax_search_charities();
          } else {
            resetfilterValues();
            $('#search-filter').hide();
          }
        }, 500));

        function delay(callback, ms) {
          var timer = 0;
          return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
              callback.apply(context, args);
            }, ms || 0);
          };
        }

        function ajax_search_charities() {

              //get url and make final url for ajax 
              var url = '{{url("donate")}}';
              var append = url.indexOf("?") == -1 ? "?" : "&";
              var finalURL = url + append + $("#charity-searchform").serialize();

              $.ajax({
                url: finalURL,
                type: 'GET',
                beforeSend: function() {
                  $(".loading-spinner").show();                    
                },
                success: function(data) {
                  $("#pagination_data").html(data);
                },
                complete: function() {
                  $(".loading-spinner").hide();
                  $('#search-filter').show();
                }
              });
      }

      // dispaly Detail in Modal
      $(document).on("click", ".charity-modal" , function(e) {
                e.preventDefault();

          $.ajax({
            url: '/donate/charities/' + $(this).attr('value') ,
            type: 'GET',
            dataType: 'html'
          })
          .done(function(data){
          
            //$('charity-modal-label').html('Charity detail');
            $('#charity-modal-body').html('');    
            $('#charity-modal-body').html(data); // load response 
            $('#charity-modal').modal('show');
            //$('#modal-loader').hide();        // hide ajax loader   
          })
          .fail(function(){
              $('#charity-modal-body').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
              //$('#modal-loader').hide();
              $('#charity-modal').modal('show');
          });

      });
   
    });

  </script>
@endpush
