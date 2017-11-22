function comprueba()
{
    if (document.forminscripcion.dni.value == '')
    {
        alert('Debe rellenar el DNI');
        document.forminscripcion.dni.focus();
        return false;
    }
    if (document.forminscripcion.nombre.value == '')
    {
        alert('Debe rellenar el nombre y apellidos');
        document.forminscripcion.nombre.focus();
        return false;
    }
    if (document.forminscripcion.telefono.value == '')
    {
        alert('Debe rellenar el teléfono de contacto');
        document.forminscripcion.telefono.focus();
        return false;
    }
    if (document.forminscripcion.email.value == '')
    {
        alert('Debe rellenar el e-mail');
        document.forminscripcion.email.focus();
        return false;
    }
    if (document.forminscripcion.contrasena.value == '')
    {
        alert('Debe rellenar la contraseña');
        document.forminscripcion.contrasena.focus();
        return false;
    }
}

function compruebajuego(juego)
{
    if (juego.value.length > 1)
    {
        alert('solo puede introducir una cifra');
        juego.value = '';
        juego.focus();
    }
    if (juego.value > 7)
    {
        alert('no puede introducir una cifra mayor a 7');
        juego.value = '';
        juego.focus();
    }
}

