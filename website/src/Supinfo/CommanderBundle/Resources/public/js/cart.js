/**
 * Created by Luciole on 03/02/2016.
 */

$(document).ready(function(){
    var total = 0;
    $(".trip_price").each(function(){
        total += parseInt($(this).html());
    });
    $(".cart-totals .subtotals .amount").html(total + "€");
    $(".cart-totals .grand .amount").html($(".cart-totals .subtotals .amount").html());
});
