{{-- Modal for  --}}
<div class="modal fade" id="event-pledge-show-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Event Plege details</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="event-pledge-show-model-form">

			<div class="form-group row">
                <label for="code" class="col-sm-3 col-form-label">Tran ID</label>
                <div class="col-sm-2">
					<input type="text" class="form-control" name="id" disabled>
                </div>
            </div>

			<div class="form-group row">
                <label for="code" class="col-sm-3 col-form-label">Deposit Date</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="deposit_date" value="" disabled>
                </div>
            </div>

			<div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label">Deposit Amount:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control"  name="formatted_deposit_amount" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="description" class="col-sm-3 col-form-label">Event Name:</label>
                <div class="col-sm-8">
					<textarea type="text" class="form-control"  name="description" disabled></textarea>
            	</div>
            </div>

			<div class="form-group row">
                <label for="linked_bu_code" class="col-sm-3 col-form-label">Employment City:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="employment_city" value="" disabled>
                </div>
            </div>

			<hr/>

			<div class="form-group row">
                <label for="created_by_name" class="col-sm-3 col-form-label">Created By :</label>
                <div class="col-sm-3">
					<input type="text" class="form-control"  name="created_by_name" disabled>
            	</div>

				<label for="formatted_created_at" class="col-sm-3 col-form-label">Created at :</label>
                <div class="col-sm-3">
					<input type="text" class="form-control"  name="formatted_created_at" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="updated_by_name" class="col-sm-3 col-form-label">Updated By :</label>
                <div class="col-sm-3">
					<input type="text" class="form-control"  name="updated_by_name" disabled>
            	</div>

				<label for="formatted_updated_at" class="col-sm-3 col-form-label">Updated at :</label>
                <div class="col-sm-3">
					<input type="text" class="form-control"  name="formatted_updated_at" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="updated_by_name" class="col-sm-3 col-form-label">Approved By :</label>
                <div class="col-sm-3">
					<input type="text" class="form-control"  name="approved_by_name" disabled>
            	</div>

				<label for="formatted_updated_at" class="col-sm-3 col-form-label">Approved at :</label>
                <div class="col-sm-3">
					<input type="text" class="form-control"  name="approved_at" disabled>
            	</div>
            </div>

		  </form>

		</div>

		<div class="modal-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		</div>
	  </div>
	</div>
</div>


