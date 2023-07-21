function ajax(name){
	$.ajax({
        url: "win_dir_picker/Http.php",
        type: "POST",
        data: {dir:name},
        success: function (response) {
			
			var res = JSON.parse(response);

			$('#breadcrumb').html('');
			$('#breadcrumb').html(res['breadcrumb']);
			
			$('#window').html('');
			$('#window').html(res['window']);
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
}