{{-- Modal for  --}}
<div class="modal fade" id="region-edit-modal" tabindex="-1" role="dialog" aria-labelledby="regionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="regionModalLabel">Edit an existing region</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="region-edit-model-form">
            <input type="hidden" class="form-control"  name="id" value="" readonly>

			<div class="form-group row">
                <label for="code" class="col-sm-2 col-form-label">Code:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="code" value="" readonly>
                </div>
            </div>

			<div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Name:</label>
                <div class="col-sm-10">
					<input type="text" class="form-control"  name="name" value="">
            	</div>
            </div>

			<div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Status:</label>
                <div class="col-sm-4">
					<select name="status" class="form-control">
						<option value="A">Active</option>
						<option value="I">Inactive</option>
					</select>
                </div>
            </div>

			<div class="form-group row">
                <label for="effdt" class="col-sm-2 col-form-label">Effective date:</label>
                <div class="col-sm-4">
					<input type="date" class="form-control"  name="effdt" value="">
            	</div>
            </div>

			<div class="form-group row">
                <label for="notes" class="col-sm-2 col-form-label">Notes:</label>
                <div class="col-sm-10">
					<textarea type="text" class="form-control"  name="notes"></textarea>
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


