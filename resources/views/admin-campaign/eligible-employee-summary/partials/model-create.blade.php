{{-- Modal for Create a new business unit --}}
<div class="modal fade" id="ee-create-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Add a daily campaign summary record</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="bu-create-model-form">

			<div class="form-group row">
                <label for="campaign_year" class="col-sm-3 col-form-label">Campaign Year:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" id="campaign_year" name="campaign_year">
                </div>
            </div>

			{{-- <div class="form-group row">
                <label for="as_of_date" class="col-sm-3 col-form-label">As of Date:</label>
                <div class="col-sm-4">
					<input type="date" class="form-control" id="as_of_date" name="as_of_date">
            	</div>
            </div> --}}

			<div class="form-group row">
                <label for="organization_code" class="col-sm-3 col-form-label">Organization</label>
                <div class="col-sm-5">
					<select class="form-control" style="width:100%;" name="organization_code">
						<option value="">select organization</option>
						@foreach ($organizations as $org)
							<option value="{{ $org->code }}" data-bu="{{ $org->business_unit->name . ' (' . $org->bu_code . ')' }}">
								{{ $org->name . ' (' . $org->code . ')' }}</option>
						@endforeach
					</select>
					{{-- <input type="text" class="form-control" name="organization_code" disabled> --}}
                </div>
            </div>

			<div class="form-group row">
                <label for="business_unit" class="col-sm-3 col-form-label">Business Unit</label>
                <div class="col-sm-6">
					<input type="text" class="form-control"  name="business_unit" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="ee_count" class="col-sm-3 col-form-label">Employee Count</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="ee_count" value="">
            	</div>
            </div>

			<div class="form-group row">
                <label for="notes" class="col-sm-3 col-form-label">Notes:</label>
                <div class="col-sm-8">
					<textarea type="text" class="form-control" id="notes" name="notes"></textarea>
            	</div>
            </div>
		
		  </form>
		</div>
		<div class="modal-footer">
		  <button type="button" id="create-confirm-btn" value="11"  class="btn btn-primary" >Create</button>
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		</div>
	  </div>
	</div>
</div>