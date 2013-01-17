var token = 5;//seconds
var jj = true;
$(document).ready(function() {

    $(".box").bind('click', function() {
        if(jj) {
            $('span').each(function() {
                $(this).removeClass('box').addClass('disabled');
            });
            $(this).addClass('clicked');
            $.post("index.php",{
                    j: this.id,
                    jk: jj },
                function (data, status) {
                    jj = false;
                    $('#payout').html(data.message).slideDown();
                    $.timer(1000, function(timer) {
                        var msg = $('#message');
                        msg.html(data.reset + token + ' seconds').slideDown();
                        if(token <= 0) {
                            window.location.reload(true);
                        }
                        token--;
                    });
            },
            "json");
        }
    });
});