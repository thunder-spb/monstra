$(function () {
    $("span[data-action=close]").on("click", function () {
        $('#imgModal').modal('hide');
    });

    $("span[data-action=tag]").on("click", function () {
        var uid = $(this).attr('data-key');

        $.ajax({
            url: 'index.php?id=catalog&edit=tag&tid=' + uid + '&t=' + $.now(),
            type: "POST",
            dataType: "json",
            success: function (result) {
				$('.modal-header h3').text(result.h3);
				$('#catalog_uid').val(result.id);
				$('#catalog_title').val(result.title);
				$('#catalog_sort').val(result.sort);
                $('#imgModal').modal('show');
            },
            error: function (jqXhr, textStatus, errorThrown) {
                alert("Error '" + jqXhr.status + "' (textStatus: '" + textStatus + "', errorThrown: '" + errorThrown + "')");
            }
        });
    });

	$("span[data-action=save_img]").on("click", function () {
		$('#catalog_upload').after('<div style="width: 200px; height: 150px; line-height: 150px;">'+$('.fileupload-preview').html()+'</div>');
		$('#upPhoto').modal('hide');
	});
});
