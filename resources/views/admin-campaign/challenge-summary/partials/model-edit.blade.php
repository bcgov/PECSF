{{-- Modal for  --}}
<div class="modal fade" id="bu-edit-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Edit an existing daily camapign summary record</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="bu-edit-model-form">
            <input type="hidden" class="form-control"  name="id" value="" readonly>

			<div class="form-group row">
                <label for="campaign_year" class="col-sm-3 col-form-label">Campaign Year:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="campaign_year" value="" readonly>
                </div>
            </div>

			<div class="form-group row">
                <label for="as_of_date" class="col-sm-3 col-form-label">As of Date</label>
                <div class="col-sm-4">
					<input type="date" class="form-control"  name="as_of_date" value="">
            	</div>
            </div>

			<div class="form-group row">
                <label for="donors" class="col-sm-3 col-form-label">No. of Donor:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="donors" value="">
            	</div>
            </div>

			<div class="form-group row">
                <label for="dollars" class="col-sm-3 col-form-label">Total Amount:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="dollars" value="">
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
		  <button type="button" id="save-confirm-btn"  class="btn btn-primary" >Save</button>
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		</div>
	  </div>
	</div>
</div>

