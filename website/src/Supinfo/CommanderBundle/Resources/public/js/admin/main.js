$(document).ready(function(){
    $('.has_submenu>a').click(function(){
        $('.maintab .active').removeClass('active');
        $(this).parent().addClass('active');
        $(this).parent().find('.submenu').toggle();
        return false;
    });
});