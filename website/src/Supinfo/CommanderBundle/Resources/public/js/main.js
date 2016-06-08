/**
 * Created by Luciole on 27/01/2016.
 */


$(document).ready(function(){

    //Code Konami
    var k = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65],
        n = 0;
    $(document).keydown(function (e) {
        if (e.keyCode === k[n++]) {
            if (n === k.length) {
                alert('Konami !!!'); // à remplacer par votre code
                n = 0;
                return false;
            }
        }
        else {
            n = 0;
        }
    });


    //cookie law
    $('.cookies_law .close_banner_btn').click(function(){
        $(".cookies_law").hide();
    });

    $('.choice tr').click(function(){
        var target = $(this).attr('data-target')
        $('.result').each(function(){
           if("#" + $(this).attr('id') != target){
              if($(this).hasClass('in'))
                  $(this).removeClass('in');
           }
        });
    });

})
