<td class="mx-0 px-0">
    <h5 class="text-secondary">
        {{ is_numeric($index) ?  $index + 1 : 'YYY'}}|
    </h5>
</td>
<td>
    <div class="form-row">
        <div class="form-group col-md-11">
            <label for="charities">Charity CRA Organization Name and Business Number</label>
            <select name="charities[]" class="form-control select2 @error('charities.'.$index) is-invalid @enderror" 
                        style="width: 100%">
                
                @isset($pledge_charity)
                    <option value="{{ $pledge_charity->charity_id }}" selected>{{ $pledge_charity->charity->charity_name }}</option>
                @endisset

            </select>
            @error( 'charities.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        

        <div class="form-group col-md-1 text-right pt-4">
            <div type="button" class="btn btn-danger  delete_this_row" data-id="charity{{ $index }}">
                <i class="fas fa-trash-alt"></i></div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="additional">Program or Branch name</label>
            <input type="text" name="additional[]" class="form-control @error('additional.'.$index) is-invalid @enderror" 
                value="{{ isset($pledge_charity) ? $pledge_charity->additional : '' }}" />
            @error( 'additional.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-2">
            <label for="percentages">Allocation (%)</label>
            <input type="text" name="percentages[]" class="form-control @error('percentages.'.$index) is-invalid @enderror" 
                value="{{ isset($pledge_charity) ? $pledge_charity->percentage : '' }}" />
            @error( 'percentages.'.$index )
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-2">
            <label for="percentages">Pay Period Amount </label>
            <input type="text" name="pay_period_allocated_amount[]" class="form-control amount" 
                value="" disabled/>
        </div>

        <div class="form-group col-md-2">
            <label for="percentages">One Time Amount </label>
            <input type="text" name="one_time_allocated_amount[]" class="form-control amount" 
                value="" disabled/>
        </div>


    </div>    

</td>        
