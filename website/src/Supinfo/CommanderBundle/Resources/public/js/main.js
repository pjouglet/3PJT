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

})