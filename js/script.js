(function ($) {

    $('#loadData button').click(function () {
        var number = $(this).index() + 1;
        $('textarea').load("data/dataSet" + number + ".data");
    });

    $('#submit').click(function () {
        $("#result").html('Starting...');
        var data = $('form').serialize();
        $.ajax({
            type: "POST",
            url: "wallConstructor.php",
            data: data,
            dataType: "html",
            success: function (response) {
                $("#result").html(response);
            },
            error: function (response) {
                $("#result").html("?????? ??? ???????? ?????");
            }
        })
    });

})(jQuery);


