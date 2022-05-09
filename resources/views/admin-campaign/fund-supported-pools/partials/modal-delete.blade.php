{{-- Modal for  --}}
<div class="modal fade" id="pool-delete-modal" tabindex="-1" role="dialog" aria-labelledby="poolModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="poolModalLabel">Confirm to delete Fund Support Pool?</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>

		<div class="modal-body">

            <form id="pool-delete-modal-form" action="" method="post">        
            @csrf
            @method('delete')

            <div class="alert alert-danger" role="alert">
            </div>


            <input type="hidden" class="form-control"  name="id" value="" disabled>

			<div class="form-group row">
                <label for="region" class="col-sm-2 col-form-label">Region:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="region" value="" disabled>
                </div>
            </div>

            <div class="form-group row">
                <label for="start_date" class="col-sm-2 col-form-label">Start Date:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="start_date" value="" disabled>
                </div>
            </div>
            </form>
		</div>

		<div class="modal-footer">
           <button type="submit" class="btn btn-primary" id="pool-delete-modal-button" data-id="">Delete</button>
		   <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
		</div>

	  </div>
	</div>
</div>


