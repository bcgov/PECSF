{{-- Modal for  --}}
<div class="modal fade" id="bu-show-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Existing business unit details</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="bu-show-model-form">
            <input type="hidden" class="form-control"  name="id" value="" disabled>

			<div class="form-group row">
                <label for="code" class="col-sm-2 col-form-label">Code:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="code" value="" disabled>
                </div>
            </div>

			<div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Name:</label>
                <div class="col-sm-10">
					<input type="text" class="form-control"  name="name" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Status:</label>
                <div class="col-sm-4">
					<select name="status" class="form-control" disabled>
						<option value="A">Active</option>
						<option value="I">Inactive</option>
					</select>
                </div>
            </div>

			<div class="form-group row">
                <label for="effdt" class="col-sm-2 col-form-label">Effective date:</label>
                <div class="col-sm-4">
					<input type="date" class="form-control"  name="effdt" value="" disabled>
            	</div>
            </div>

			<div class="form-group row">
                <label for="notes" class="col-sm-2 col-form-label">Notes:</label>
                <div class="col-sm-10">
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


