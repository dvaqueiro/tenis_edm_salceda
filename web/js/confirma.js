
$(document).ready(function () {
    $('.del-resultado').on('click', function () {
        showConfirmMessage($(this));
    });
});

function showConfirmMessage(element) {
    swal({
        title: "Estás seguro?",
        text: "Si borras el resultado no podrás recuperarlo.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Si, borrarlo.",
        closeOnConfirm: false
    }, function () {
        tmpString = element.attr('id');
        elementIdArr = tmpString.split("_");
        deleted = performDeletePlayer(elementIdArr[1]);
        console.log(elementIdArr[1]);
        if (deleted == true) {
            swal("Borrado", "El resultado ha sido eliminado satisfactoriamente", "success");
        } else {
            swal("Error", "Se ha producido un error eliminando el resultado", "error");
        }
    });
}

function performDeletePlayer(idJugador) {
    var deleted = false;

    $.ajax({
        url: "/admin/result/" + idJugador,
        method: "DELETE",
        async: false
    }).done(function (resp) {
        deleted = true; 
        $("#lg_"+idJugador).remove();
    }).fail(function (resp) {
        deleted = false;        
    });

    return deleted;
}
