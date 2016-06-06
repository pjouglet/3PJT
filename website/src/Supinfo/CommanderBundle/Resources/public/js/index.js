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
    $("#search_travel_form_start_day_month").children('option').each(function(){
        if($(this).attr('value') == today.getMonth() +1)
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_start_day_day").children('option').each(function(){
        if($(this).attr('value') == today.getDate())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_start_day_year").children('option').each(function(){
        if($(this).attr('value') == today.getFullYear())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_start_time_hour").children('option').each(function(){
        if($(this).attr('value') == today.getHours())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_start_time_minute").children('option').each(function(){
        if($(this).attr('value') == today.getMinutes())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_end_day_month").children('option').each(function(){
        if($(this).attr('value') == today.getMonth() +1)
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_end_day_day").children('option').each(function(){
        if($(this).attr('value') == today.getDate())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_end_day_year").children('option').each(function(){
        if($(this).attr('value') == today.getFullYear())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_end_time_hour").children('option').each(function(){
        if($(this).attr('value') == today.getHours())
            $(this).attr('selected', 'selected');
    });

    $("#search_travel_form_end_time_minute").children('option').each(function(){
        if($(this).attr('value') == today.getMinutes())
            $(this).attr('selected', 'selected');
    });

})