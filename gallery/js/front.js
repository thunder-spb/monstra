$(function () {
    $("a[data-action=gallery]").live("click", function () {
        var uid = $(this).attr('data-key');
        var all = $(this).attr('data-pages');
        var page = $(this).attr('data-page');
        var sort = $(this).attr('data-sort');
        var order = $(this).attr('data-order');

        $.ajax({
            url: '/gallery/?slug=' + uid + '&page=' + page + '&pages=' + all + '&sort=' + sort + '&order=' + order + '&t=' + $.now(),
            type: "POST",
            dataType: "json",
            success: function (result) {
                $('[name=image]').html(result.images);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                alert("Error '" + jqXhr.status + "' (textStatus: '" + textStatus + "', errorThrown: '" + errorThrown + "')");
            }
        });
    });
});
