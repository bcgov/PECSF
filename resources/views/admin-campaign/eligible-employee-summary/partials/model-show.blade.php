{{-- Modal for  --}}
<div class="modal fade" id="ee-show-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Existing eligible employee summary</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="bu-show-model-form">
            <input type="hidden" class="form-control"  name="id" value="" disabled>

			<div class="form-group row">
                <label for="campaign_year" class="col-sm-3 col-form-label">Campaign Year</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="campaign_year" value="" disabled>
                </div>
            </div>

			<div class="form-group row">
                <label for="as_of_date" class="col-sm-3 col-form-label">As of Date:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="as_of_date" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="organization_code" class="col-sm-3 col-form-label">Organization</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="organization_code" disabled>
                </div>
            </div>

			<div class="form-group row">
                <label for="business_unit_code" class="col-sm-3 col-form-label">Business Unit Code</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="business_unit_code" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="business_unit_name" class="col-sm-3 col-form-label">Business Unit Name</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="business_unit_name" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="ee_count" class="col-sm-3 col-form-label">Employee Count</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="ee_count" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="notes" class="col-sm-3 col-form-label">Notes:</label>
                <div class="col-sm-8">
					<textarea type="text" class="form-control"  name="notes" disabled></textarea>
            	</div>
            </div>

			<hr/>

			<div class="form-group row">
                <label for="created_by_name" class="col-sm-2 col-form-label">Created By :</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="created_by_name" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="formatted_created_at" class="col-sm-2 col-form-label">Created at :</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="formatted_created_at" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="updated_by_name" class="col-sm-2 col-form-label">Updated By :</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="updated_by_name" disabled>
            	</div>
            </div>
			
			<div class="form-group row">
                <label for="formatted_updated_at" class="col-sm-2 col-form-label">Updated at :</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="formatted_updated_at" disabled>
            	</div>
            </div>

		  </form>

		</div>


		<div class="modal-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		</div>
	  </div>
	</div>
</div>


