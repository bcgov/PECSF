{{-- Modal for Create a new business unit --}}
<div class="modal fade" id="bu-create-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
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

			<div class="form-group row">
                <label for="as_of_date" class="col-sm-3 col-form-label">As of Date:</label>
                <div class="col-sm-4">
					<input type="date" class="form-control" id="as_of_date" name="as_of_date">
            	</div>
            </div>

			<div class="form-group row">
                <label for="donors" class="col-sm-3 col-form-label">No. of Donor:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" id="donors" name="donors">
                </div>
            </div>

			<div class="form-group row">
                <label for="dollars" class="col-sm-3 col-form-label">Total Amount:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" id="dollars" name="dollars">
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