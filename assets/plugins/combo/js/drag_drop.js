/* drag & drop
 * @autor pp galvan - ldrmx
 */

var obj = $(".publisher-box");

obj.on('dragenter', function (e) 
{   e.stopPropagation();
    e.preventDefault();
    $(this).addClass('DropReady DropReadyPhoto WantsDragDrop');
	$('._119').addClass('child_is_active').addClass('child_was_focused').addClass('child_is_focused');
});

obj.mouseout(function() {
    $(this).removeClass('DropReady DropReadyPhoto WantsDragDrop');
});

obj.on('dragover', function(e) 
{    e.stopPropagation();
     e.preventDefault();
});

obj.on('drop', function (e) 
{
     $(this).removeClass('DropReady DropReadyPhoto WantsDragDrop');
     e.preventDefault();
     var droppedFiles = e.originalEvent.dataTransfer.files;
    $('.js_publisher_photos .x-uploader input[type="file"]').prop('files', droppedFiles);
});

$('body').on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});

$('body').on('dragover', function (e) 
{
  e.stopPropagation();
  e.preventDefault();
  obj.removeClass('DropReady DropReadyPhoto WantsDragDrop');
});

$('body').on('drop', function (e) 
{   e.stopPropagation();
    e.preventDefault();
});