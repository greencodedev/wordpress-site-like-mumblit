var frame_box = 400;
var border = 20;
var frame_img = '';

function frame_box_size(Width,Height){
	if(Width>=420){
		frame_box = 400;
	} else {
		frame_box = (Width-border);
	}
	$('.photoframe .preview').css({'width': frame_box, 'height': frame_box});
	
	//box and border
	var frame_box_o = (frame_box+border);
	$('.photoframe .cont').css({'width': frame_box_o});
	
	//new size image png
	$('.fg').css({'width': frame_box});
	
	//linebox width
	var frame_box_c_1 = ((frame_box*420)/400);
	//lineimg
	var frame_box_c_2 = ((frame_box*80)/400);	
	//linebox height
	var frame_box_c_3 = frame_box_c_2+20;
	
	//change line
	$('.photoframe .designs').css({'width': frame_box_c_1, 'height': frame_box_c_3});
	$('.photoframe .design').css({'height': frame_box_c_2});
	if(frame_img){
		$('#crop-area').html('');
		window.croppie = new Croppie(document.getElementById("crop-area"), {
			'url': frame_img,
			boundary: {
				height: frame_box,
				width: frame_box
			},
			viewport: {
				width: frame_box,
				height: frame_box
			}
		});
	}
}

//NEW SIZE RESIZE
$(window).resize(function(){ 
	var Height = $(window).height();
	var Width = $(window).width();
	frame_box_size(Width,Height);
});

//START NEWS SIZE	 
$(function(){
	var Height = $(window).height();
	var Width = $(window).width();
	frame_box_size(Width,Height);
});
 

function dataURItoBlob(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);
    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ia], {type:mimeString});
}


window.uploadPicture = function(){ 
	croppie.result({ size: "viewport"
	}).then(function(dataURI){
		var formData = new FormData();
		formData.append("design", $(".fg").attr("data-design"));
		formData.append("image", dataURItoBlob(dataURI));  //console.log(dataURItoBlob(dataURI)); 
		$.ajax({
			url: site_ajax+'?f=photoframe&s=upload_frame&only=combo',
			data: formData,
			type: "POST",
			contentType: false,
			processData: false,
			success: function(r){
				$(".download").html("Uploaded");
				/*$('.photoframe').addClass('hidden');
				$(".download").html("Add Frame");*/
				location.reload();
			},
			error: function(){
				$(".download").html("Try more late!");
			},
			xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if(myXhr.upload){
					myXhr.upload.addEventListener('progress', function(e){
						if(e.lengthComputable){
							var max = e.total;
							var current = e.loaded;	
							var percentage = Math.round((current * 100)/max);
							$(".download").html("Uploading... Please Wait... " + percentage + "%");
						}
					}, false);
				}
				return myXhr;
			}
		});
	});
}


//create preview
window.updatePreview = function(url) {
	$('#crop-area').html('');
	window.croppie = new Croppie(document.getElementById("crop-area"), {
		'url': url,
		boundary: {
			height: frame_box,
			width: frame_box,
			type: 'circle'
		},
		viewport: {
			width: frame_box,
			height: frame_box
		}
	});

	$("body").on('touchstart mouseover', '.fg', function(){
		$(".fg").css({'z-index':-1});
	});
		
	$("body").on('touchend mouseleave', '.cr-boundary', function(){
		$(".fg").css({'z-index':10});
	});

	$("body").on('click', '.download', function(){
		$(this).html("Uploading... Please wait...");
		uploadPicture();
	});
}


//load photo frame
$(function(){
	$("body").on("click", '.design', function(){
		$(".fg").attr({"src": $(this).attr("src"), "data-design": $(this).data("design") });
		$(".design.active").removeClass("active");
		$(this).addClass("active");
	});
});


function show_photo_frame(){
	$('.photoframe').removeClass('hidden');
	if(frame_img == ''){
		frame_img = avatar_full;
		updatePreview(avatar_full);		
	}
	var Height = $(window).height();
	var Width = $(window).width();
	frame_box_size(Width,Height);
}