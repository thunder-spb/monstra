/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function () {
    'use strict';
    $('.upload').hide();
    var item = 0;
    var pro = 0;
    var upload = 0;

    $(document).ajaxSend(function() {
        if ($('#upf').val() == '0' && upload > 0)
            item++;
    });

    $(document).ajaxComplete(function() {
        if ($('#upf').val() == '0' && item != 0 && upload > 0)
        {
            pro = item;
            item--;

            var progress = parseInt(item/pro * 100, 10);
            $('.progress .bar').css(
                'width',
                120-progress + '%'
            );
        }
        if (item == 0 && upload > 0)
            location.reload();
    });
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'index.php?id=gallery&upload=files&guid=' + $('#gid').val() + '&t=' + $.now(),
        dataType: 'json',
        add: function (e, data) {
            upload = 1;
            data.context = $('.upload').show()
                .click(function () {
                    $(this).html('<i class="icon-retweet icon-white"></i>');
                    $(this).hide();
                    data.submit();
                });
        },
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                resize(file);
            });
        },
        progressall: function (e, data) {
            $('.span5.fileupload-progress.fade').removeClass('fade');
            var progress = parseInt(data.loaded / data.total * 25, 10);
            $('.progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });

    function resize(file)
    {
        $.ajax({
            url: 'index.php?id=gallery&rename=true&guid=' + $('#gid').val() + '&t=' + $.now(),
            type: "POST",
            data: {file: file.name, type: file.type},
            done: function()
            {
                $('#res').text(item);
            }
        });
    }

    $("a[data-action=delete]").on("click", function () {
        var text = $(this).attr('data-confirm');
        if (confirm(text))
        {
            var csrf = $('#csrf').val();
            var items = new Array();
            $('input[name="key"]:checked').each(function() {items.push($(this).val());});

            if (items.length == 0) {
                alert("Please select item(s) to delete.");
            } else {
                $.ajax({
                    url: 'index.php?id=gallery&delete=true&t=' + $.now(),
                    type: "POST",
                    data: {token: csrf, items: items},
                    success: function (text) {
                        window.location = 'index.php?id=gallery&action=items&gallery_id='+ $('#gid').val();
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        alert("Error '" + jqXhr.status + "' (textStatus: '" + textStatus + "', errorThrown: '" + errorThrown + "')");
                    }
                });
            }
        }
        return false;
    });

    $("span[data-action=close]").on("click", function () {
        $('#imgModal').modal('hide');
    });

    $("input[data-action=checked]").on("click", function () {
        if($(this).prop('checked')){
            $('input[name="key"]').prop('checked',true);
        } else {
            $('input[name="key"]').prop('checked',false);
        }
    });

    $("span[data-action=image]").on("click", function () {
        var uid = $(this).attr('data-key');

        $.ajax({
            url: 'index.php?id=gallery&image=edit&guid=' + uid + '&t=' + $.now(),
            type: "POST",
            dataType: "json",
            success: function (result) {
                $('#uid').val(result.id);
                $('#gallery_title').val(result.title);
                $('#gallery_media').val(result.media);
                $('#gallery_desc').val(result.desc);
                $('#imgModal').modal('show');
            },
            error: function (jqXhr, textStatus, errorThrown) {
                alert("Error '" + jqXhr.status + "' (textStatus: '" + textStatus + "', errorThrown: '" + errorThrown + "')");
            }
        });
    });

    $("span[data-action=add_media]").on("click", function () {
        $('#imgModal').modal('show');
    });

});
