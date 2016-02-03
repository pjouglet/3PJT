/**
 * Created by Luciole on 03/02/2016.
 */
$(document).ready(function(){

    //Autocompletion sur le choix des gares
    var gares = ["Lille Europe", "Lille Flandres", "Maubeuge", "Toulouse", "Paris"];
    $("#gare_depart").autocomplete({
        source : gares
    });


    //Datepicker pour la date de départ
    $("#date_voyage").datepicker({
        todayHighlight: true,
        todayBtn: "linked"
    });
})