<td class="mx-0 px-0">
    <h5 class="text-secondary">
        {{ is_numeric($index) ?  $index + 1 : 'YYY'}}|
    </h5>
</td>
<td>
        
        <div class="form-row bg-light">
            <div class="form-group col-md-8">
                <label for="charities">Charity CRA Organization Name and Business Number</label>
                <select name="charities[]" class="form-control select2 @error('charities.'.$index) is-invalid @enderror" >

                    @if ( session()->get('charity'.$index.'_selected') )
                        <option value="{{ session()->get('charity'.$index.'_selected')->id }}">
                                {{ session()->get('charity'.$index.'_selected')->charity_name }}
                                ({{ session()->get('charity'.$index.'_selected')->registration_number }})
                            </option>
                    @else 
                        <option value="" selected>select charity</option>
                    @endif
                </select>
                @error( 'charities.'.$index )
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-2">
                <label for="status[]">Status</label>
                <select name="status[]" class="form-control @error('status.'.$index) is-invalid @enderror">
                    <option value="A" {{ old('status.'.$index, $pool_charity->status) == 'A' ? 'selected' : '' }}>Active</option>
                    <option value="I" {{ old('status.'.$index, $pool_charity->status) == 'I' ? 'selected' : '' }}>Inactive</option>

                </select>
                @error('status.'.$index)
                    <span class="invalid-feedback">{{  $message  }}</span>
                @enderror
            </div>
            <div class="form-group col-md-1  text-right" >
                <label >&nbsp;</label>
                <div type="button" class="form-control btn btn-danger delete_this_row" data-id="charity{{ $index }}">Delete</div>
            </div>
            <div class="form-group col-md-1 text-right">
                <a data-toggle="collapse" href="#collapse{{ $index }}" aria-expanded="true" class="collapsed"
                aria-controls="collapse{{ $index }}">
    
                <label >&nbsp;</label>
                {{-- <div type="button" class="btn btn-danger delete_this_row" data-id="charity{{ $index }}">Delete</div> --}}
                <div type="button" class="form-control btn btn-outline-primary  " data-id="charity{{ $index }}">
                    <i class="fa"></i></div>
                </a>
            </div>  
        
        </div>


    <div id="collapse{{ $index }}" class="collapse""
        role="tabpanel" aria-labelledby="heading{{ $index }}">
        <div class="card-body bg-muted">

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="names">Supported Program Name</label>
                    <input type="text" name="names[]" class="form-control @error('names.'.$index) is-invalid @enderror" 
                        value="{{ old('names.' . $index) ?? (count($errors) == 0 ? $pool_charity->name : '') }}" />
                    @error( 'names.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="descriptions">Supported Program Description</label>
                    <textarea type="text" name="descriptions[]" class="form-control @error('descriptions.'.$index) is-invalid @enderror"
                    >{{ old('descriptions.' . $index) ?? (count($errors) == 0 ? $pool_charity->description : '')
                        }}</textarea>
                    @error( 'descriptions.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="percentages">Allocation (%)</label>
                    <input type="text" name="percentages[]" class="form-control @error('percentages.'.$index) is-invalid @enderror" 
                        value="{{ old('percentages.' . $index) ?? (count($errors) == 0 ? $pool_charity->percentage : '') }}" />
                    @error( 'percentages.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-5">
                    <label for="contact_names">Charity Program Contact Name</label>
                    <input type="text" name="contact_names[]" class="form-control @error('contact_names.'.$index) is-invalid @enderror" 
                        value="{{ old('contact_names.' . $index) ?? (count($errors) == 0 ? $pool_charity->contact_name : '') }}" />
                    @error( 'contact_names.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-5">
                    <label for="contact_titles">Charity Program Contact Title  (Optional)</label>
                    <input type="text" name="contact_titles[]" class="form-control @error('contact_titles.'.$index) is-invalid @enderror" 
                        value="{{ old('contact_titles.' . $index) ?? (count($errors) == 0 ? $pool_charity->contact_title : '') }}" />
                    @error( 'contact_titles.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="contact_emails">Charity Program Contact Email</label>
                    <input type="text" name="contact_emails[]" class="form-control @error('contact_emails.'.$index) is-invalid @enderror" 
                        value="{{ old('contact_emails.' . $index) ?? (count($errors) == 0 ? $pool_charity->contact_email : '') }}" />
                    @error( 'contact_emails.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-8">
                    <label for="notes">Notes  (Optional)</label>
                    <input type="text" name="notes[]" class="form-control @error('notes.'.$index) is-invalid @enderror" 
                        value="{{ old('notes.' . $index) ?? (count($errors) == 0 ? $pool_charity->notes : '') }}" />
                    @error( 'notes.'.$index )
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>    

            <div class="form-row">
                <div class="form-group col-md-4 current-image">
                    @php (  $image_name = old('current_images.' . $index) ?? (count($errors) == 0 ? $pool_charity->image : '')  )
                    <input name="current_images[]" type="hidden" value="{{ $image_name  }}">
                    @if ( $image_name ) 
                    <figure>
                        <img src="{{asset('img/uploads/fspools')}}/{{ $image_name }}" width="auto" height="150">
                        <figcaption ><span class="font-weight-bold">Current file name: </span>{{ $image_name }}</figcaption>
                    </figure>    
                    <button class='delete-current-image-button btn btn-sm btn-outline-danger ml-4' 
                            type='button' data-name='{{ $image_name }}'>Delete the previous image</button>
                    @endif

                </div>

                <div class="form-group col-md-6">
                    
                    <div class="image">
                        <label>Add Image</label><br>
                        <label>
                            <input id="images" accept=".png,.jpg,.jpeg,.bmp" style="display:none;" onchange="loadFile(event)" type="file" class="form-control-file @error('images.'.$index) is-invalid @enderror"
                                   name="images[]" value="{{ old('images.'.$index) }}">
                            <span style="font-weight:normal;background:#efefef;border:#000 1px solid; padding:5px;">Choose an Image (suggested file types: .png, .jpg, .svg)</span></label>
                            <br>
                        <img style="width:auto;height:150px;display:none;" id="output" />
                        <div class="delete-image-button-area pt-3"> 
                        </div>
                        {{-- <label for="images">Upload new image file (suggested file types: .png, .jpg, .svg)</label>
                        <input type="file" class="form-control-file @error('images.'.$index) is-invalid @enderror" 
                                onchange="loadFile(event)" name="images[]" value="{{ old('images.'.$index) }}"> --}}
                        @error( 'images.'.$index )
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>  

        </div>
    </div> 
</td>        
