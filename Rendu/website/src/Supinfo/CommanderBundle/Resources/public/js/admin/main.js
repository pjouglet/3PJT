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

    $('.list_show .list-group-item').click(function(){
        $('.list_show .list-group-item').each(function(){
            $(this).removeClass('active');
        });
        $(this).addClass('active');
    });

    $('.add_travel_to_list').click(function(){
        if($('.list_show .active').html() != undefined){
            $('.list_add .list-group').append('<li class="list-group-item">' + $('.list_show .active').html() + '</li>');
            $('.list_show .active').addClass('hidden');
            $('.form-group #stations').val($('.form-group #stations').val() + $('.list_show .active .id').html() + ';')
        }
    });

    $('.delete_travel_from_list').click(function(){
        $('.list_add li').each(function(){
            $(this).remove();
        });
        $('.list_show li.hidden').each(function(){
            $(this).removeClass('hidden');
        });
        $('.form-group #stations').val('');
    });

    $('#start_time select').each(function(){
       $(this).addClass('form-control');
    });

    //page de maintenance

    $("#add_ip").click(function(){
        //alert($('.user_ip').attr('value'));
        $('#ip_list').attr('value', $('#ip_list').attr('value') + ';' + $('.user_ip').attr('value'));
    });
});