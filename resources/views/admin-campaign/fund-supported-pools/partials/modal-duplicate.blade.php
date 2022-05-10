{{-- Modal for  --}}
<div class="modal fade" id="pool-duplicate-modal" tabindex="-1" role="dialog" aria-labelledby="poolModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="poolModalLabel">Confirm to duplicate Fund Support Pool?</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>

		<div class="modal-body">

            <form id="pool-duplicate-modal-form" action="" method="post">        
            @csrf

            <div class="alert alert-danger" role="alert">
            </div>


            <input type="hidden" class="form-control" name="id" value="" disabled>

			<div class="form-group row">
                <label for="region" class="col-sm-3 col-form-label">From Region:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="region" value="" disabled>
                </div>
            </div>

            <div class="form-group row">
                <label for="old_start_date" class="col-sm-3 col-form-label">From Start Date:</label>
                <div class="col-sm-4">
					<input type="text" class="form-control" name="old_start_date" value="" disabled>
                </div>
            </div>

			<hr/>

            <div class="form-group row">
                <label for="start_date" class="col-sm-3 col-form-label">To Start Date:</label>
                <div class="col-sm-4">
					<input type="date" class="form-control" name="start_date" value="">
                </div>
            </div>

            </form>
		</div>

		<div class="modal-footer">
           <button type="submit" class="btn btn-primary" id="pool-duplicate-modal-button" data-id="">Duplicate</button>
		   <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
		</div>

	  </div>
	</div>
</div>


