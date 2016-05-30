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
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });

    //page customers
    $(".delete_customer").click(function(){
        var url = $(this).children('span').text();
        swal({
                title: "Attention",
                text: "L'utilisateur ne sera pas supprimé, son compte sera juste désactivé. Continuer ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });

    $(".activate_customer").click(function(){
        var url = $(this).children('span').text();
        swal({
                title: "Attention",
                text: "Réactiver le compte utilisateur permettra à celui-ci de se connecter à nouveau. Continuer ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });

    //page zone
    $(".delete_zone").click(function(){
        var url = $(this).children('span').text();
        swal({
                title: "Attention",
                text: "Une zone supprimée ne pourra plus être récupérée. Continuer ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });

    //page stations
    $(".delete_station").click(function(){
        var url = $(this).children('span').text();
        swal({
                title: "Attention",
                text: "Une station supprimée ne pourra plus être récupérée. Continuer ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });

    //page segment
    $(".delete_segment").click(function(){
        var url = $(this).children('span').text();
        swal({
                title: "Attention",
                text: "En supprimant ce segment, vous supprimez aussi tout les trajets associés. Continuer ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });

    //page travel
    $(".delete_travel").click(function(){
        var url = $(this).children('span').text();
        swal({
                title: "Attention",
                text: "Vous allez supprimer ce trajet. Continuer ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if(isConfirm)
                    window.location.href = url;
            });
        return false;
    });
});