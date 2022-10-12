{{-- Modal for  --}}
<div class="modal fade" id="bu-edit-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Edit an existing special campaign</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="bu-edit-model-form"  enctype="multipart/form-data">
			@method('PUT')

            <input type="hidden" class="form-control"  name="id" value="" readonly>
			<input type="hidden" class="form-control"  name="image" value="" readonly>

			<div class="card">
				<h5 class="card-header bg-light">
					<span class="text-primary font-weight-bold">Campaign Details</span>
				</h5>

				<div class="card-body py-2 px-2">
				  
					<div class="form-row">
						<div class="form-group col-md-6">
						  <label for="name">Special campaign name</label>
						  <input type="text" id="name" name="name" class="form-control">
						</div>

					</div>

					<div class="form-row">
						<div class="form-group col-md-9">

							<label for="charity_id">Charity Name</label>
							<select name="charity_id" class="form-control select2"></select>
							<span class="charity_id_errors"></span>	
				  
						</div>

						<div class="form-group col-md-3">
							<label for="registration_number">Charity Business number</label>
							<input type="text"  class="form-control registration_number" disabled>
						</div>
	
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
						  <label for="start_date">Campaign start date</label>
						  <input type="date" id="start_date" name="start_date" class="form-control" >
						</div>
						<div class="form-group col-md-6">
						  <label for="end_date">Campaign end date</label>
						  <input type="date" id="end_date" name="end_date" class="form-control">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="description">Campaign description</label>
							<textarea type="text" id="description" name="description" 
								class="form-control"></textarea>
						</div>
					</div>
				</div>
				
			</div>

			<div class="card">
				<h5 class="card-header bg-light">
					<span class="text-primary font-weight-bold">Campaign logo</span>
				</h5>

				<div class="card-body py-2 px-2">
					
					<div class="form-row"> 
						<div class="form-group col-md-5">
							<div class="file-upload">
								<div class="file-select">
									<div class="file-select-button" id="fileName">Choose File</div>
									<div class="file-select-name">No file chosen...</div> 
									<input type="file" accept=".jpeg,.jpg,.png,.svg" name="logo_image_file"> 
								</div>
							</div>
							
						</div>

						<div class="col-md-1 remove-upload-area" style="display: none;">
							<div class="pt-1"><button type='button'  class="btn btn-danger remove-upload-file">
								<i class="fas fa-trash-alt fa-lg"></i></button></div> 
						</div>

					</div>

					<img class="upload-logo-image" style="width:auto;height:150px;display:none;">

					<div class="logo-image-file-error">
						@error( 'logo_image_file' )
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>

				</div>
				
			</div>


			<div class="card">
				<h5 class="card-header bg-light text-primary">
					<span class="text-primary font-weight-bold">Campaign banner</span>
				</h5>

				<div class="card-body py-2 px-2">
					<div class="form-row">
						<div class="form-group col-md-12">
						  <label class="mb-0" for="banner_text">Banner text</label>
						  <p class="m-0 py-0 text-primary">A short-call-to-action prompting users to donate to the special campaign.</p>
						  <input type="text" id="banner_text" name="banner_text" class="form-control">
						</div>
					</div>

				</div>
				
			</div>

			<div class="modal-footer">
				<button type="button" id="save-confirm-btn"  class="btn btn-primary" >Save</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		
		  </form>
		</div>

	  </div>
	</div>
</div>


