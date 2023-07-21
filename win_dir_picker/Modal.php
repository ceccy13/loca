<!-- Modal -->
<div class="modal fade" id="pickerModal" role="dialog">
	<div class="modal-dialog">
	  <!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Open</h4>
			</div>
			
			<div class="modal-body">
				<div class="container">
					<div id="breadcrumb" class="breadcrumb">

					</div>
					
					<div id="window" class="window">

					</div>
						
					<div>
						<div class="div-inline">Path:</div> 
						<div class="div-inline">
							<input type="text" readonly id="path_dest" value=""/>
							<input type="hidden" readonly id="path_dest_symbolic" value=""/>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button id="apply_filter" onclick="setDirPath();" type="button" class="btn btn-primary" data-dismiss="modal">Open</button>
				<button style="border: 1px solid #D8D8D8" type="button" class="btn btn-outline-light text-dark" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>