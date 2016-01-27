/**
 * Created by Luciole on 27/01/2016.
 */

//javascript de la page de login

$(document).ready(function(){
    //Lorsque l'utilisateur se connecte
    $('#login_button').click(function(){
        $(".login").find(".form-group").each(function(){
            $(this).removeClass("has-error");
            $(this).removeClass("has-success");
        });

        var error = false;
        //Si le champ de mail ou de password est vide
        if($("#email_login").val().trim().length == 0){
            $("#email_login").parent().addClass("has-error");
            error = true;
        }
        else
            $("#email_login").parent().addClass("has-success");

        if($("#password_login").val().trim().length == 0){
            $("#password_login").parent().addClass("has-error");
            error =  true;
        }
        else
            $("#password_login").parent().addClass("has-success");

        return !error;
    });

    //lorsque l'utilisateur s'enregistre
    $('#signup_button').click(function(){

        $(".register").find(".form-group").each(function(){
            $(this).removeClass("has-error");
            $(this).removeClass("has-success");
        });

        var error = false;

        //Si l'email est vide
        if($("#email").val().trim().length == 0){
            $("#email").parent().addClass("has-error");
            error = true;
        }
        else
            $("#email").parent().addClass("has-success");

        //Si le mot de passe est vide
        if($("#password").val().trim().length == 0){
            $("#password").parent().addClass("has-error");
            error = true;
        }
        else
            $("#password").parent().addClass("has-success");

        //Si la confirmation du mot de passe est vide
        if($("#password_confirmation").val().trim().length == 0){
            $("#password_confirmation").parent().addClass("has-error");
            error = true;
        }
        else
            $("#password_confirmation").parent().addClass("has-success");

        //Si les mots de passes ne correspondent pas
        if($("#password").val() != $("#password").val()){
            $("#password").parent().addClass("has-error");
            $("#password_confirmation").parent().addClass("has-error");
            error = true;
        }else{
            $("#password").parent().addClass("has-success");
            $("#password_confirmation").parent().addClass("has-success");
        }

        //Si le prénom est vide
        if($("#firstname").val().trim().length == 0){
            $("#firstname").parent().addClass("has-error");
            error = true;
        }
        else
            $("#firstname").parent().addClass("has-success");

        //Si le nom est vide
        if($("#lastname").val().trim().length == 0){
            $("#lastname").parent().addClass("has-error");
            error = true;
        }
        else
            $("#lastname").parent().addClass("has-success");

        return !error;
    });
});
