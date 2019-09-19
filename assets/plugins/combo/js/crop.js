function OpenCropModal() {
	$('#cropImage').modal('show');
	setTimeout(function () {
    	$('#image-to-crop img').rcrop({
            minSize : [100,100],
            preserveAspectRatio : true,
            grid : true
        });
    }, 1000);
}

function CropImage() {
    values = $('#image-to-crop img').rcrop('getValues');
    $path = $('#image-to-crop img').attr('data-image');
    if (!$path) {
    	return false;
    	$('#cropImage').modal('hide');
    }
    $('#cropImage').find('.ball-pulse').css('display', 'block');
    $.post(Wo_Ajax_Requests_File() + '?f=crop-avatar', {user_id:995, path: $path, x: values.x, y:values.y, height: values.height, width:values.width}, function(data, textStatus, xhr) {
        if (data.status == 200) {
        	location.reload(false);
        } else {
        	$('#cropImage').modal('hide');
        }
        $('#cropImage').find('.ball-pulse').css('display', 'none');
    });
}
/*
<div class="modal fade" id="cropImage" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></button>
				<h4 class="modal-title"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg> Crop your avatar</h4>
			</div>
			<div class="modal-body">
				<div id="image-to-crop">
					<img src="https://www.funbook-pk.com/upload/photos/2018/07/UI5IHheFnxKmG2nC593y_11_cdeb9f5c30f2849ee1e9c061c1358b60_avatar_full.jpg?123" alt="Clown Clown" data-image ="upload/photos/2018/07/UI5IHheFnxKmG2nC593y_11_cdeb9f5c30f2849ee1e9c061c1358b60_avatar_full.jpg" style="max-width: 100%; border: 1px solid #ccc;" crossorigin="anonymous">
				</div>
			</div>
			<div class="modal-footer">
				<div class="ball-pulse"><div></div><div></div><div></div></div>
				<button type="button" class="btn btn-main" onclick="CropImage();">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><polygon points="14 2 18 6 7 17 3 17 3 13 14 2"></polygon><line x1="3" y1="22" x2="21" y2="22"></line></svg> Save				</button>
			</div>
		</div>
	</div>
</div>
*/