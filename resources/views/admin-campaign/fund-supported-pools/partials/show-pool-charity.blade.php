<td class="mx-0 px-0">
    <h5 class="text-secondary">
        {{ is_numeric($index) ?  $index + 1 : 'YYY'}}|
    </h5>
</td>
<td>
        
        <div class="form-row bg-light">
            <div class="form-group col-md-8">
                <label for="charities">Charity CRA Organization Name and Business Number</label>
                <input type="text" name="charities[]" class="form-control" 
                    value="{{ $pool_charity->charity->charity_name . ' (' . $pool_charity->charity->registration_number . ')' }}" disabled/>
            </div>

            <div class="form-group col-md-2">
                <label for="status[]">Status</label>
                <select name="status[]" class="form-control" disabled>
                    <option value="A" {{ $pool_charity->status == 'A' ? 'selected' : '' }}>Active</option>
                    <option value="I" {{ $pool_charity->status == 'I' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="form-group col-md-1  text-right" >
                {{-- <label >&nbsp;</label>
                <div type="button" class="form-control btn btn-danger delete_this_row" data-id="charity{{ $index }}">Delete</div> --}}
            </div>
            <div class="form-group col-md-1 text-right">
                <a data-toggle="collapse" href="#collapse{{ $index }}" aria-expanded="true" class="collapsed"
                    aria-controls="collapse{{ $index }}">
    
                <label >&nbsp;</label>
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
                    <input type="text" name="names[]" class="form-control" 
                        value="{{ $pool_charity->name }}" disabled/>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="descriptions">Supported Program Description</label>
                    <textarea type="text" name="descriptions[]" class="form-control" disabled
                    >{{ $pool_charity->description}}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="percentages">Allocation (%)</label>
                    <input type="text" name="percentages[]" class="form-control" disabled 
                        value="{{ $pool_charity->percentage }}" />
                </div>
                <div class="form-group col-md-5">
                    <label for="contact_names">Charity Program Contact Name</label>
                    <input type="text" name="contact_names[]" class="form-control" disabled
                        value="{{ $pool_charity->contact_name }}" />
                </div>
                <div class="form-group col-md-5">
                    <label for="contact_titles">Charity Program Contact Title</label>
                    <input type="text" name="contact_titles[]" class="form-control" disabled
                        value="{{ $pool_charity->contact_title }}" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="contact_emails">Charity Program Contact Email</label>
                    <input type="text" name="contact_emails[]" class="form-control" disabled
                        value="{{ $pool_charity->contact_email }}" />
                </div>

                <div class="form-group col-md-8">
                    <label for="notes">Notes</label>
                    <input type="text" name="notes[]" class="form-control" disabled
                        value="{{ $pool_charity->notes }}" />
                </div>
            </div>    

            <div class="form-row">
                <div class="form-group col-md-4">
                    @php (  $image_name = $pool_charity->image )
                    <input name="current_images[]" type="hidden" value="{{ $image_name  }}">
                    @if ( $image_name ) 
                    <figure>
                        {{-- <img src="{{asset('img/uploads/fspools')}}/{{ $image_name }}" width="auto" height="150"> --}}
                        <img src="{{asset('storage/fspools')}}/{{ $image_name }}" width="auto" height="150"> 
                        <figcaption ><span class="font-weight-bold">Current file name: </span>{{ $image_name }}</figcaption>
                    </figure>    
                    @endif
                </div>

            </div>  

        </div>
    </div> 
</td>        
