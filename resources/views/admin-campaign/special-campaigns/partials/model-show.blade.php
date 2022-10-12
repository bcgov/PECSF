{{-- Modal for  --}}
<div class="modal fade" id="bu-show-modal" tabindex="-1" role="dialog" aria-labelledby="buModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="buModalLabel">Existing special campaign details</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form id="bu-show-model-form">
            <input type="hidden" class="form-control"  name="id" value="" disabled>

			<div class="card">
				<h5 class="card-header bg-light">
					<span class="text-primary font-weight-bold">Campaign Details</span>
				</h5>

				<div class="card-body py-2 px-2">
				  
					<div class="form-row">
						<div class="form-group col-md-6">
						  <label for="name">Special campaign name</label>
						  <input type="text" id="name" name="name" class="form-control" disabled>
						</div>

					</div>

					<div class="form-row">
						<div class="form-group col-md-9">
							<label for="charity_name">Charity Name</label>
							<input type="text" id="charity_name" name="charity_name" class="form-control" disabled>
						</div>

						<div class="form-group col-md-3">
							<label for="registration_number">Charity Business number</label>
							<input type="text" id="registration_number" name="registration_number" class="form-control" disabled>
						</div>
	
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
						  <label for="start_date">Campaign start date</label>
						  <input type="date" id="start_date" name="start_date" class="form-control" disabled >
						</div>
						<div class="form-group col-md-6">
						  <label for="end_date">Campaign end date</label>
						  <input type="date" id="end_date" name="end_date" class="form-control" disabled>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="description">Campaign description</label>
							<textarea type="text" id="description" name="description" readonly
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

						<figure class="logo_image pt-2">
							<img src="" width="auto" height="150">
							<figcaption ><span class="font-weight-bold">File name: </span>
								<span class="logo_image_filename"></span>
							</figcaption>
						</figure> 
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
						  <input type="text" id="banner_text" name="banner_text" class="form-control" disabled>
						</div>
					</div>

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


