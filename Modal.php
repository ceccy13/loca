<?php
	
	echo '
		<!-- Modal -->
		<div class="modal fade" id="filterModal" role="dialog">
			<div class="modal-dialog">

			  <!-- Modal content-->
			  <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Filter</h4>
				</div>
				
				<div class="modal-body">
					<div class="container">

						<div id="filter">
							<form id="form" method="POST">
								<div>
									<label>
										<b>Years:</b>
									</label>
	';				
								if($years)
								{
									foreach($years as $year){
										$checked = '';
										if(isset($_SESSION['years']) && in_array($year ,$_SESSION['years']))
										{
											$checked = 'checked';
										}

										echo '
											<div>
												<input type="checkbox" name="years[]" value="'.$year.'" '.$checked.'>
												<label>'.$year.'</label>
											</div>
										';
									}
								}
						
	echo '
									<div style="display: inline-block;">
										<button id="filter_all" type="button" class="btn btn-outline-primary">Select All</button>
										<button id="filter_none" type="button" class="btn btn-outline-primary">Deselect All</button>
									</div>
								</div>
							</form>
						</div>

					</div>
				</div>

				<div class="modal-footer">
					<button id="apply_filter" type="button" class="btn btn-primary" data-dismiss="modal">Apply</button>
					<button style="border: 1px solid #D8D8D8" type="button" class="btn btn-outline-light text-dark" data-dismiss="modal">Close</button>
				</div>
			  </div>
			  
			</div>
		</div>
	';