$(document).ready(function(){
    $('.has_submenu>a').click(function(){
        $('.maintab .active').removeClass('active');
        $(this).parent().addClass('active');
        $(this).parent().find('.submenu').toggle();
        return false;
    });

    //page employé
    $(".delete_employee").click(function(){
        var url = $(this).children('span').text();
        swal({
            title: "Attention",
            text: "Cette action sera définitive. L'employé sera supprimé, continuer ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            closeOnConfirm: false
        },
        function (isConfirm) {
            window.location.href = url;
        });
        return false;
    })
});