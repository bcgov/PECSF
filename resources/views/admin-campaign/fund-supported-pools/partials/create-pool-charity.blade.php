<td class="mx-0 px-0">
    <h5 class="text-secondary">
        {{ is_numeric($index) ?  $index + 1 : 'YYY'}}|
    </h5>
</td>
<td>
    <div class="form-row">
{{--
        <div class="card-header" role="tab" id="heading{{ $index }}">
            <a data-toggle="collapse" href="#collapse{{ $index }}" aria-expanded="true"
              aria-controls="collapse{{ $index }}">
              <h5 class="mb-0">
                Collapsible Group Item #1 <i class="fas fa-angle-down rotate-icon"></i>
              </h5>
            </a>
        </div>
        <div id="collapse{{ $index }}" class="collapse show" role="tabpanel" aria-labelledby="headingOne1">
            <div class="card-body">
            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3
            wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum
            eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla
            assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
            nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
            farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus
            labore sustainable VHS.
            </div>
        </div> --}}
        <div class="form-group col-md-8">
            <label for="charities">Charity CRA Organization Name and Business Number</label>
            <select id="charities" name="charities[]" class="form-control select2 @error('charities.'.$index) is-invalid @enderror" >

                @if ( old('charities.' . $index ) && session()->get('charity'.$index.'_selected') )
                    <option value="{{ session()->get('charity'.$index.'_selected')->id }}">
                            {{ session()->get('charity'.$index.'_selected')->charity_name }}
                            ({{ session()->get('charity'.$index.'_selected')->registration_number }})
                        </option>
                @else
                    <option value="" selected>-- choose charity --</option>
                @endif
                {{-- @foreach ($products as $product)
                    <option value="{{ $product->id }}"{{ $oldProduct == $product->id ? ' selected' : '' }}>
                        {{ $product->name }} (${{ number_format($product->price, 2) }})
                    </option>
                @endforeach --}}
            </select>
            <span id="charities_errors">
                  @error( 'charities.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            </span>


        </div>

        <div class="form-group col-md-2">
            <label for="status[]">Status</label>
            <select id="status" name="status" class="form-control @error('status.'.$index) is-invalid @enderror">
                <option value="A">Active</option>
                <option value="I">Inactive</option>
            </select>
            <span id="status_errors">
                @error('status.'.$index)
                <span class="invalid-feedback">{{  $message  }}</span>
            @enderror
            </span>

        </div>
        <div class="form-group col-md-1 " >
            {{-- <div type="button" class="form-control  btn  btn-danger delete_this_row" data-id="charity{{ $index }}">Delete</div> --}}
        </div>
        <div class="form-group col-md-1 text-right p-2">
            <div type="button" class="btn btn-danger  delete_this_row" data-id="charity{{ $index }}">
                <i class="fas fa-trash-alt"></i></div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="names">Supported Program Name</label>
            <input type="text" id="names" name="names" class="form-control @error('names.'.$index) is-invalid @enderror"
                value="{{ old('names.' . $index) ?? '' }}" />

            <span id="names_errors">
                 @error( 'names.'.$index )
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </span>


        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="descriptions">Supported Program Description</label>
            <textarea type="text" id="descriptions" name="descriptions" class="form-control @error('descriptions.'.$index) is-invalid @enderror"
                >{{ old('descriptions.' . $index) ?? '' }}</textarea>
           <span id="descriptions_errors">
               @error( 'descriptions.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
           </span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-2">
            <label for="percentages">Allocation (%)</label>
            <input type="text" id="percentages" name="percentages" class="form-control @error('percentages.'.$index) is-invalid @enderror"
                value="{{ old('percentages.' . $index) ?? '' }}" />

            <span id="percentages_errors">
                @error( 'percentages.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            </span>

        </div>
        <div class="form-group col-md-5">
            <label for="contact_names">Charity Program Contact Name</label>
            <input type="text" id="contact_names" name="contact_names" class="form-control @error('contact_names.'.$index) is-invalid @enderror"
                value="{{ old('contact_names.' . $index) ?? '' }}" />
           <span id="contact_names_errors">
                 @error( 'contact_names.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
           </span>
        </div>
        <div class="form-group col-md-5">
            <label for="contact_titles">Charity Program Contact Title (Optional)</label>
            <input type="text" id="contact_titles" name="contact_titles" class="form-control @error('contact_titles.'.$index) is-invalid @enderror"
                value="{{ old('contact_titles.' . $index) ?? '' }}" />
           <span id="contact_titles_errors">
                 @error( 'contact_titles.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
           </span>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="contact_emails">Charity Program Contact Email</label>
            <input type="text" id="contact_emails" name="contact_emails" class="form-control @error('contact_emails.'.$index) is-invalid @enderror"
                value="{{ old('contact_emails.' . $index) ?? '' }}" />
           <span id="contact_emails_errors">
               @error( 'contact_emails.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
           </span>
        </div>
        <div class="form-group col-md-8">
            <label for="notes">Notes (Optional)</label>
            <input id="notes" type="text" name="notes" class="form-control @error('notes.'.$index) is-invalid @enderror"
                value="{{ old('notes.' . $index) ?? '' }}" />
            @error( 'notes.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="image">
                <label for="images">Add Image</label><br>
                <input id="images" style="display:none;" onchange="loadFile(event)" type="file" class="form-control-file @error('images.'.$index) is-invalid @enderror"
                        name="images[]" value="{{ old('images.'.$index) }}">
                <label for="images"><span style="font-weight:normal;background:#efefef;border:#000 1px solid; padding:5px;">Choose an Image, Supported File Types Are: *.png, *.jpg</span></label>
                    <br>
                <img style="width:auto;height:300px;border:#ccc 1px solid;padding:1px;display:none;" id="output"/>
                <script>
                    var loadFile = function(event) {
                        var output = document.getElementById('output');
                        output.src = URL.createObjectURL(event.target.files[0]);
                        output.onload = function() {
                            URL.revokeObjectURL(output.src) // free memory
                        }
                        $("#output").css("display","block");
                    };
                </script>
                <span id="images_errors">
                    @error( 'images.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
               </span>
                <br>
            </div>
        </div>
    </div>
</td>
