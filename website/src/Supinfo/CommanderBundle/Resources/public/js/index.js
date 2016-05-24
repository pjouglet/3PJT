/**
 * Created by Luciole on 03/02/2016.
 */
$(document).ready(function(){
    //Autocompletion sur le choix des gares
    $.ajax({
        url: "http://notemonminou.hol.es/api/stations",
        crossDomain: true,
        datatype: 'json',
        success: function (data) {
            var gares = []
            $.each(data, function (key, value) {
                gares.push(value.name);
            });
            console.log(gares);
            $("#gare_depart").autocomplete({
                source : gares
            });
        }
    });

    //Datepicker pour la date de départ
    $("#date_voyage").datepicker({
        todayHighlight: true,
        todayBtn: "linked"
    });
})