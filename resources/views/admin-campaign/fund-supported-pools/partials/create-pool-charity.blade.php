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
            <select name="charities[]" class="form-control select2 @error('charities.'.$index) is-invalid @enderror" >
                
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
            @error( 'charities.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-2">
            <label for="status[]">Status</label>
            <select name="status[]" class="form-control @error('status.'.$index) is-invalid @enderror">
                <option value="A">Active</option>
                <option value="I">Inactive</option>
            </select>
            @error('status.'.$index)
                <span class="invalid-feedback">{{  $message  }}</span>
            @enderror
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
            <input type="text" name="names[]" class="form-control @error('names.'.$index) is-invalid @enderror" 
                value="{{ old('names.' . $index) ?? '' }}" />
            @error( 'names.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="descriptions">Supported Program Description</label>
            <textarea type="text" name="descriptions[]" class="form-control @error('descriptions.'.$index) is-invalid @enderror"
                >{{ old('descriptions.' . $index) ?? '' }}</textarea>
            @error( 'descriptions.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-2">
            <label for="percentages">Allocation (%)</label>
            <input type="text" name="percentages[]" class="form-control @error('percentages.'.$index) is-invalid @enderror" 
                value="{{ old('percentages.' . $index) ?? '' }}" />
            @error( 'percentages.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-5">
            <label for="contact_names">Charity Program Contact Name</label>
            <input type="text" name="contact_names[]" class="form-control @error('contact_names.'.$index) is-invalid @enderror" 
                value="{{ old('contact_names.' . $index) ?? '' }}" />
            @error( 'contact_names.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-5">
            <label for="contact_titles">Charity Program Contact Title</label>
            <input type="text" name="contact_titles[]" class="form-control @error('contact_titles.'.$index) is-invalid @enderror" 
                value="{{ old('contact_titles.' . $index) ?? '' }}" />
            @error( 'contact_titles.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="contact_emails">Charity Program Contact Email</label>
            <input type="text" name="contact_emails[]" class="form-control @error('contact_emails.'.$index) is-invalid @enderror" 
                value="{{ old('contact_emails.' . $index) ?? '' }}" />
            @error( 'contact_emails.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-8">
            <label for="notes">Notes</label>
            <input type="text" name="notes[]" class="form-control @error('notes.'.$index) is-invalid @enderror" 
                value="{{ old('notes.' . $index) ?? '' }}" />
            @error( 'notes.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>    

    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="image">
                <label for="images">Add Image</label>
                <input type="file" class="form-control-file @error('images.'.$index) is-invalid @enderror" 
                        name="images[]" value="{{ old('images.'.$index) }}">
                @error( 'images.'.$index )
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>  
</td>        
