function animateVideosList(){
	var v = $('.video-js')[0];
	// Listen for canplaythrough event
	v.addEventListener( "canplaythrough", function( e ) {

	  console.log( "canplaythrough fired!" );

	}, false );

	v = $('.mc-video-style');
	v.on('mouseenter', function(){
		$(this).get(0).play();
	});
	v.on('mouseleave', function(){
		//$(this).get(0).pause();
		$(this).get(0).load();
	});
}

function attrChangeListener(){
	//listener for attribute changed
	var element = document.querySelector('#current_video');
	var observer = new MutationObserver(function(mutations) {
		var step = 0;
		mutations.forEach(function(mutation) {
			step = step + 1;
			if (mutation.type === "attributes") {
				if($(element).hasClass('vjs-ended')) {
					if(step == mutations.length) {
						var key = $('#key').val();
						var new_key = (+key + 1);
						if(files_of_dir[new_key] === undefined) {
							new_key = 0;
						}
																					
						$('#key').val(new_key);
						load_to_paly_video();
					}
				}
			}
		});
	});

	observer.observe(element, {
	  attributes: true //configure it to listen to attribute changes
	});
}

function onScroll(){
	window.onscroll = function(e) {
		//alert((window.innerHeight + window.scrollY));
		//alert((document.body.scrollHeight));
		
		//var videos_list_num = document.getElementsByClassName('div-element').length;
		var videos_list_num = document.getElementsByClassName('load_to_paly_video').length;
		var videos_of_dir_num = Object.keys(files_of_dir).length;

		if ((window.innerHeight + window.scrollY) >= document.body.scrollHeight && videos_list_num < videos_of_dir_num) {
			load_more_videos();
			
			// Disable scrolling temporarily
			$('body').prop('class', 'stop-scrolling');
			// For mobile devices, you'll need to handle the touchmove event:
			$('body').bind('touchmove', function(e){e.preventDefault()});
			
			window.scrollTo(0, (window.scrollY - 200));
			console.log('Bottom of page');
			
			// Enable scrolling 
			$('body').prop('class', '');
		}
	}
}

function load_to_paly_video() {
	var key = $('#key').val();
	var url_param = files_of_dir[key];
	var new_url = '?v=' + url_param;
	window.history.pushState("object or string", "Title", new_url);

	$('#main_video_name').html(url_param);
	$('#current_video_html5_api').prop('src', url_param);	
	$('#current_video_html5_api')[0].play();
}

function load_more_videos() {
	// TODO
	// var dir = $('#symbolic_dir_path').val();
	var next_videos_to_load = video_loader();
	var html = '';
	
	//const fruits = [ { '10': 'zero' }, { '11': 'one' } ];
	if(!jQuery.isEmptyObject(next_videos_to_load)) {
		next_videos_to_load.forEach(function (obj, key_array) {
			for (let key in obj) {
				var file = obj[key];
				var id = file.trim();
				id = file.replaceAll('\\', '');
				id = id.replaceAll('.', '');
				id = id.replaceAll(' ', '');
				id = id.replace(/[^a-zA-Z0-9]/g, '');

				html += `
					<div class="row">
						<div class="col-md-12">
							<div class="div-element">
								<a href="" class="load_to_paly_video">
									<video class="video-js mc-video-style" muted="muted">
										<source src="${file}" type="video/mp4" />
									</video>
									<input id="${id}" type="hidden" value="${key}"/>
									<input name="file_name" type="hidden" value="${file}"/>
								</a>
							</div>
							<div class="div-info">
								<p><b>File:</b> ${file}</p>
								<p>${files_info[key]['file_size']}</p>
								<p>${files_info[key]['file_time_last_access']}</p>
								<p>${files_info[key]['file_last_changed']}</p>
							</div>
						</div>
					</div>
					<br>
					<br>						
				`;
			} 
		});
		
		//$("#next_videos_list").html('');
		//$("#next_videos_list").append(html);
		//$("#next_videos_list").clone().appendTo($('#dest'));
		$(html).appendTo($('#dest'));
	}
}

function video_loader() {
	var video_loader_step = $('#video_loader_step').val();
	var from = 0 + (+video_loader_step);
	var to = 10 + (+video_loader_step);
	$('#video_loader_step').val(+video_loader_step + 10);
	
	//Object.entries(obj).slice(0,2).map(entry => entry[1]);
	//["foo", "bar"]
	
	//var obj = {0: 'zero', 1: 'one', 2: 'two', 3: 'three', 4: 'four'};
	//var result = Object.keys(obj).slice(0,2).map(key => ({[key]:obj[key]}));
	//console.log(result);
	//[ { '0': 'zero' }, { '1': 'one' } ]

	var result = Object.keys(files_of_dir).slice(from,to).map(key => ({[key]:files_of_dir[key]}));

	return result;
}

function getCheckedBoxes()
{
	var check_box_values = $('#filter [type="checkbox"]:checked').map(function () {
		return this.value;
	}).get();
	
	return check_box_values;
}

$(window).on('load', function(){
	setTimeout(function(){
		$('#loader').modal('hide');
	}, 3000);
});

$(document).ready(function(){
	$('#loader').modal('show');

	// get Base URL and modify the URL in Web without reloading
	baseurl = window.location.origin+window.location.pathname;
	history.pushState({}, null, baseurl);

	const myTimeout = setTimeout(function(){
		// alert($('#current_video').duration);	
	}, 0);

	animateVideosList();
	attrChangeListener();
	onScroll();
	
	$('#filter_all').click(function(){
		$('#filter [type="checkbox"]').prop('checked', true);
	});

	$('#filter_none').click(function(){
		$('#filter [type="checkbox"]').prop('checked', false);
	});

	$('#apply_filter').click(function(){
		var checked = getCheckedBoxes();
		// alert(JSON.stringify(checked, null, 4));

		$('#form').submit();
	});
});

// bind click handler to element that is added later/dynamically
/*
document.addEventListener('click', function(e){
    if(e.target && e.target.id== 'myDynamicallyAddedElementID'){
         //do something
    }
});
*/

// Alternatively, if your using jQuery: (bind click handler to element that is added later/dynamically)
$(document).on('click','.load_to_paly_video',function(event){
	event.preventDefault();
	var url_param = $(this).find('input[name="file_name"]').val();
	url_param = url_param.trim();
	url_param = url_param.replaceAll('\\', '');
	url_param = url_param.replaceAll('.', '');
	url_param = url_param.replaceAll(' ', '');
	url_param = url_param.replace(/[^a-zA-Z0-9]/g, '');

	var new_key = $('#' + url_param).val();
	$('#key').val(new_key);
	load_to_paly_video();

	return false;
});

		/* test */
		/*
		function addinfofunction() {
		  document.querySelectorAll('.result')
			 .forEach(div => div.setAttribute('hidden', ''));
		  document.querySelectorAll('input[type=checkbox]:checked')
			 .forEach(el => document.querySelector(`#res-${el.getAttribute('id')}`)?.removeAttribute('hidden'));
		}
		*/
		/* test */
