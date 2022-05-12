{{-- Modal for Create a new organization --}}
<div class="modal fade" id="organization-create-modal" tabindex="-1" role="dialog" aria-labelledby="organizationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="organizationModalLabel">Add a new organization</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="organization-create-model-form">

			<div class="form-group row">
                <label for="code" class="col-sm-2 col-form-label">Code:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" id="code" name="code">
                </div>
            </div>

			<div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Name:</label>
                <div class="col-sm-10">
					<input type="text" class="form-control" id="name" name="name">
            	</div>
            </div>

			<div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Status:</label>
                <div class="col-sm-4">
					<select id="status" name="status" class="form-control" required>
						<option value="A" selected>Active</option>
						<option value="I">Inactive</option>
					</select>
                </div>
            </div>

			<div class="form-group row">
                <label for="effdt" class="col-sm-2 col-form-label">Effective date:</label>
                <div class="col-sm-4">
					<input type="date" class="form-control" id="effdt" name="effdt">
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