$(document).ready(function(){
    $("#login_button").click(function(){
        var error = false;

        if($("#email").val().trim().length == 0){
            $("#email").parent().addClass("has-error");
            error = true;
        }
        else
            $("#email").parent().addClass("has-success");

        if($("#password").val().trim().length == 0){
            $("#password").parent().addClass("has-error");
            error =  true;
        }
        else
            $("#password").parent().addClass("has-success");
        return !error;
    });
});