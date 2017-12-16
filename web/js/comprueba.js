setsLocal = 0;
setsVisitante = 0;

$(document).ready(function () {
    $('#resultado_submit').on('click', function () {
        setsLocal = 0;
        setsVisitante = 0;
        error = validarResultado();
        if (error) {
            $('#resultado_error').text(error);
            console.log(setsLocal);
            console.log(setsVisitante);
        } else {
            $('#formulario_resultado').submit();
        }
    });
});

function validarResultado()
{
    if ($('#jugadorVisitante').val() == '') {
        return 'Debe seleccionar un oponente!';
    }

    if (!validarSet(1)) {
        return 'El resultado del primer set no es correcto!';
    }

    if (!validarSet(2)) {
        return 'El resultado del segundo set no es correcto!';
    }

    if (setsLocal == setsVisitante) {
        return 'Es necesario cubrir el resultado del tercer set';
    }

    if (Math.abs(setsLocal - setsVisitante) == 1 && !validarSet(3)) {
        return 'El resultado del tercer set no es correcto!';
    }

    return '';
}

function validarSet(numeroSet)
{
    juegosLocal = $('#set' + numeroSet + '_JuegosLocal').val();
    juegosVisitante = $('#set' + numeroSet + '_JuegosVisitante').val();

    if (juegosLocal == '' || juegosVisitante == '') {
        return false;
    }

    if (juegosLocal > juegosVisitante) {
        juegosGanador = juegosLocal;
        juegosPerdedor = juegosVisitante
        setsLocal += 1;
    } else {
        juegosGanador = juegosVisitante;
        juegosPerdedor = juegosLocal
        setsVisitante += 1;
    }

    if (juegosGanador < 6) {
        return false;
    }

    if (juegosGanador == 6 && juegosPerdedor > 4) {
        return false;
    }

    if (juegosGanador == 7 && (juegosPerdedor < 5 || juegosPerdedor == 7)) {
        return false;
    }

    return true;
}