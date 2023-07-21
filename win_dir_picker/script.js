$(document).ready(function(){

	$(document).on('dblclick','.folder',function(e){
        e.preventDefault();
	});

	var currpage    = window.location.href;
    var lasturl     = sessionStorage.getItem("last_url");

    if(lasturl == null || lasturl.length === 0 || currpage !== lasturl ){
        sessionStorage.setItem("last_url", currpage);
        //alert("New page loaded");
    }else{
        //alert("Refreshed Page");
    }
	
	$('#pickerModal').on('hidden.bs.modal', function () {
		// alert('close');
		setTimeout(function(){
			ajax('session_destroy');
			window.location.reload();
		}, 0);
	});
	ajax([]);
	
});

// post clicked dir of tree dirs
$(document).on('click','.folder', function(){
	var dir = $(this).attr('value');

	// Disable Button Folder after been clicked
	if($(this).hasClass('directory_list')){
		$(this).attr('disabled', 'diabled');
	}
	
	ajax(dir);

	// get value of dynamic input
	setTimeout(function(){
		var path = $('#path').val();
		var position = 2;
		var path_symbolic = path.substring(0, position - 1) + path.substring(position, path.length);
		$('#path_dest').val(path);
		$('#path_dest_symbolic').val(path_symbolic);
	}, 300);

});

function setDirPath(){
	$('#loader').modal('show');
	var symbolic_dir_path = $('#path_dest_symbolic').val();
	$('#symbolic_dir_path').val(symbolic_dir_path);
	var dir_path = $('#symbolic_dir_path').val();
	setCookie('dir_path', symbolic_dir_path, 365);
	//alert(getCookie('dir_path'));
}
