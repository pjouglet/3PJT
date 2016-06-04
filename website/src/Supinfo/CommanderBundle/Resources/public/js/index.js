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
            $("#start_station").autocomplete({
                source : gares
            });
            $("#end_station").autocomplete({
                source : gares
            });
        }
    });

    $('.input-group select').each(function(){
        $(this).addClass('form-control');
    });

    var today = new Date();
    console.log(today.getMonth());
    /*$("#search").click(function(){
        var start_station = $('#start_station');
        var end_station = $('#end_station').attr('value');
        console.log(start_station);
        console.log(end_station);
        return false;
    });*/
})