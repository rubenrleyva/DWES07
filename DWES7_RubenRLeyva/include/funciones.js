/* 
 * DWES7 - Aplicaciones web hibridas
 * Tarea 7: Ciclismo
 * Autor: Rubén Ángel Rodriguez Leyva
 */

// Variables globales que se utilizarán.
var xajax;
var limpiar;
var cod_carrera;
var insertarPunto = false;
var insertarInicio = false;
var insertarFinal = false;

/**
 * Función encargada de mostrar una tabla con las carreras.
 */
function muestraCarreras()
{
    xajax_tablaCarreras();
}

/**
 * Función encargada de mostrar el fomulario para ingresar una nueva carrera.
 */
function nuevaCarrera()
{
    document.getElementById('nuevo').style.display = 'block';
    document.getElementById('insertar').disabled = true;
    document.getElementById('botonnuevo').style.display = 'none';
}

/**
 * Función encargada de mostrar la carrera según su código de carrera.
 * 
 * @param {type} cod_carrera El código de la carrera
 */
function mostrarCarrera(cod_carrera)
{
    limpiar = "carreras";
    document.getElementById('edicion').style.display = 'block';
    document.getElementById('nuevo').style.display = 'none';
    document.getElementById('botonnuevo').style.display = 'none';
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';

    xajax_editaCarrera(cod_carrera);
    xajax_tablaPuntos(cod_carrera);
    
}

/**
 * Función encargada mostrar el mapa
 * 
 * @param {type} cod_carrera El código de carrera
 */
function mostrarMapa(cod_carrera)
{
    
    document.getElementById('edicion').style.display = 'none';
    document.getElementById('nuevo').style.display = 'none';

    xajax_devolverPuntosMapa(cod_carrera);
}

/**
 * Función que muestra el mapa.
 * 
 * @param {type} puntos Los diferentes puntos.
 */
function muestraMapa(puntos)
{

    document.getElementById('mapdiv').style.display = 'block';
    document.getElementById('botoncerrar').style.display = 'block';
    document.getElementById('edicion').style.display = 'none';
    document.getElementById('nuevo').style.display = 'none';
    
    var directionsService = new google.maps.DirectionsService;
    var directionsDisplay = new google.maps.DirectionsRenderer;

    var map = new google.maps.Map(document.getElementById('mapdiv'), {

        zoom: 8,

        center: {lat: 37.17, lng: -3.59}
    });

    directionsDisplay.setMap(map);

    var puntillos = null;

    if(puntos)
    {
        
        puntillos = [];

        for (var i = 2; i < puntos.length; i++)
        {
            puntillos.push({
              location: new google.maps.LatLng(puntos[i][0], puntos[i][1]),
              stopover: true
            });
        };
    }


    directionsService.route({

        origin: new google.maps.LatLng(puntos[0][0], puntos[0][1]),

        destination: new google.maps.LatLng(puntos[1][0], puntos[1][1]),

        waypoints: puntillos,

        optimizeWaypoints: true,

        travelMode: "BICYCLING"
        
    }, function(response, status)
    {

        if (status === 'OK')
        {
            directionsDisplay.setDirections(response);
        }
    });
  
    
    /*
     
     
     var map = new google.maps.Map(document.getElementById('mapdiv'), {
        zoom: 8,
        center: {lat: 37.17, lng: -3.59}
    });
     
    var marker, i, titulo;

    for (i = 0; i < puntos.length; i++) {  
        if(i === 0){
            titulo = "Comienzo";
        }else if(i === 1){
            titulo = "Final";
        }else{
            titulo = "Punto de paso: "+(i-1);
        }
        marker = new google.maps.Marker({
          position: new google.maps.LatLng(puntos[i][0], puntos[i][1]),
          title: titulo,
          map: map
        });
    }*/
}

/**
 * Función encargada de cerrar el mapa
 */
function cerrarMapa()
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
}

/**
 * Función encargada de cancelar la nueva carrera
 */
function cancelarNuevaCarrera()
{
    document.getElementById('nuevo').style.display = 'none';
    document.getElementById('botonnuevo').style.display = 'block';
    limpia("nuevacarrera");
}

/**
 * Función encargada de cancelar la edición de la carrera
 */
function cancelarEdicionCarrera()
{
    document.getElementById('edicion').style.display = 'none';
    document.getElementById('botonnuevo').style.display = 'block';
    limpia("editacarrera");
}

/**
 * Función encargada de solicitar las coordenadas de inicio
 */
function solicitarCoordenadasInicio()
{   
    document.getElementById('nuevo').style.display = 'block';
    
    var direccionInicio = document.getElementById('puntoinicio').value;
    // Comprobamos que se haya introducido una dirección
    if(direccionInicio.length < 10)
    {
        alert("Introduzca una dirección de inicio válida.");
        return false;
    }
    else
    {
        
        xajax.$('solicitarcoordenadasinicio').disabled = true;
        xajax.$('solicitarcoordenadasinicio').value = "Esperando datos del servidor...";
        
         // Aquí se hace la llamada a la función registrada de PHP
        xajax_obtenerCoordenadasInicio(direccionInicio);
        
        insertarInicio = true;
    }

    if(insertarInicio && insertarFinal)
    {
        xajax.$('insertar').disabled = false;
        insertarInicio = false;
        insertarFinal = false;
        
    }
    else
    {
        xajax.$('insertar').disabled = true;
        
    }
    
    return false;
}

/**
 * Función encargada de solicitar las coordenadas del final
 */
function solicitarCoordenadasFinal()
{  
    document.getElementById('nuevo').style.display = 'block';
    
    var direccionFinal = document.getElementById('puntofinal').value;
    
    // Comprobamos que se haya introducido una dirección
    if(direccionFinal.length < 10)
    {
        alert("Introduzca una dirección de fin válida.");
        return false;
    }
    else
    {
        xajax.$('solicitarcoordenadasfinal').disabled = true;
        xajax.$('solicitarcoordenadasfinal').value = "Esperando datos del servidor...";
        
        // Aquí se hace la llamada a la función registrada de PHP
        xajax_obtenerCoordenadasFinal(direccionFinal);
        
        // Cambiamos el valor de la variable para activar el botón de ingreso
        insertarFinal = true;
    }
    
    if(insertarInicio && insertarFinal)
    {
        xajax.$('insertar').disabled = false;
        insertarInicio = false;
        insertarFinal = false;
        
    }
    else
    {
        xajax.$('insertar').disabled = true;
        
    }
    
    return false;
}

/**
 * Función encargada de solicitar las coordenadas de un punto.
 * 
 * @param {type} edita El código de la carrera
 * @returns {Boolean}
 */
function solicitarCoordenadasPunto(edita)
{
    // Variables a utilizar.
    var calle, ciudad, pais, cp;
    
    // Comprobamos si se va a utilizar para editar y recogemos los datos.
    if(edita)
    { 
        document.getElementById('agrega').style.display = 'block';
        calle = document.getElementById('calle').value;
        ciudad = document.getElementById('ciudad').value;
        pais = document.getElementById('pais').value;
        cp = document.getElementById('cp').value;
    }
    else
    {
        calle = document.getElementById('callep').value;
        ciudad = document.getElementById('ciudadp').value;
        pais = document.getElementById('paisp').value;
        cp = document.getElementById('cpp').value;
        
    }
    
    // Unimos la dirección en una sola línea
    var nuevaDireccion = calle+','+ciudad+','+pais+','+cp;
    
    // Comprobamos que se haya introducido una dirección
    if(nuevaDireccion.length < 10)
    {
        alert("Se necesitan más datos.");
        return false;
    }
    else
    {
        // Desabilitamos el botón
        xajax.$('solicitarcoordenadaspunto').disabled = true;
        
        // Mensaje mientras se encuentra desabilitado
        xajax.$('solicitarcoordenadaspunto').value = "Esperando datos del servidor...";
        
        // comprobamos si se va a utilizar para editar
        if(edita)
        {
           // Aquí se hace la llamada a la función encargada de obtener las coordenadas
            xajax_obtenerCoordenadasPunto(nuevaDireccion, false);
            insertarPunto = true;
        }
        else
        {
            // Aquí se hace la llamada a la función encargada de obtener las coordenadas
            xajax_obtenerCoordenadasPunto(nuevaDireccion, true);
            insertarPunto = true;
        }
    }

    // En el momento que se establecen las coordenadas correctamente se nos permite
    // pulsar el botón para agregar los datos.
    if(insertarPunto)
    {
        xajax.$('insertarpunto').disabled = false;
        xajax.$('actualizarpunto').disabled = false;
        insertar = false;
    }
    else
    {
        xajax.$('insertarpunto').disabled = true;
        xajax.$('actualizarpunto').disabled = true;
    }
    
    return false;
}

/**
 * Función encargada de eliminar la carrera.
 * 
 * @param {type} cod_carrera El código de la carrera
 */
function eliminarCarrera(cod_carrera)
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    
    // Preguntamos si se desea borrar la carrera
    if(confirm('¿Estás seguro de querer eliminar la carrera de nombre: '+cod_carrera+'?'))
    {
        xajax_eliminaCarrera(cod_carrera);
        muestraCarreras();
        cancelarEdicionCarrera();
    }
}

/**
 * Función encargada de eliminar un punto.
 * 
 * @param {type} cod_punto El código del punto
 */
function eliminaPunto(cod_punto){
    
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    
    // Preguntamos si se desea borrar el punto
    if(confirm('¿Estás seguro de querer eliminar el punto número: '+cod_punto+'?'))
    {
        xajax_eliminarPunto(cod_punto);
        muestraCarreras();
        mostrarCarrera(cod_carrera);
    }
}

/**
 * Función encargada de ingresar una carrera.
 */
function ingresarCarrera()
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    var respuesta = xajax.request({xjxfun:"validarNuevaCarrera"}, {mode:'synchronous', parameters: [xajax.getFormValues("nuevacarrera")]});

    if(respuesta)
    {
        alert('Se ha ingresado una nueva carrera con exito.');
        cancelarNuevaCarrera();
    }
    else
    {
        alert('No se ha podido ingresar la nueva carrera con exito.');
        
    }
    
    muestraCarreras();
}

/**
 * Función encargada de editar una carrera.
 */
function editarCarrera()
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    var respuesta = xajax.request({xjxfun:"validarEdicionCarrera"}, {mode:'synchronous', parameters: [xajax.getFormValues("editacarrera")]});

    if(respuesta)
    {
        alert('Se ha editado la carrera con exito.');
        mostrarCarrera(cod_carrera);
        cancelarEdicionCarrera();
    }
    else
    {
        alert('No se ha podido editar la carrera con exito.');
    }
    
    muestraCarreras();
}


/**
 * Función encargada de agregar el nuevo punto a la base de datos.
 */
function agregarPunto()
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    
    var respuesta = xajax.request({xjxfun:"agregarPunto"}, {mode:'synchronous', parameters: [xajax.getFormValues("agregapuntos")]});
    
    cod_carrera = document.getElementById('codigoa').value;

    if(respuesta)
    {
        alert('No se ha podido ingresar un nuevo punto con exito.');
    }
    else
    {
        alert('Se ha ingresado un nuevo punto con exito.');
        cerrarAgregarPunto();
        mostrarCarrera(cod_carrera);
    }
    
    muestraCarreras();
}

/**
 * Función encargada de abrir el formulario para insertar un nuevo punto.
 * 
 * @param {type} cod_carrera El código de la carrera
 */
function agregaPunto(cod_carrera)
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    document.getElementById('agrega').style.display = 'block';
    document.getElementById('codigoa').value = cod_carrera;
    document.getElementById('editacarrera').style.display = 'none';
    document.getElementById('nuevo').style.display = 'none';
    document.getElementById('botonnuevo').style.display = 'none'; 
    xajax.$('insertarpunto').disabled = true;
}

/**
 * Función encargada de cerrar el formulario para insertar un nuevo punto.
 */
function cerrarAgregarPunto()
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    document.getElementById('agrega').style.display = 'none';
    document.getElementById('editacarrera').style.display = 'block';
    document.getElementById('edita').style.display = 'none';
    limpia("agregapuntos");
    limpia("editapunto");
}

/**
 * Función encargada de mostrar los datos para editar un punto de la carrera.
 * 
 * @param {type} cod_punto El código del punto
 * @returns {undefined}
 */
function editaPunto(cod_punto)
{
    document.getElementById('mapdiv').style.display = 'none';
    document.getElementById('botoncerrar').style.display = 'none';
    document.getElementById('agrega').style.display = 'none';
    document.getElementById('editacarrera').style.display = 'none';
    document.getElementById('edita').style.display = 'block';
    xajax.$('actualizarpunto').disabled = true;
    xajax_editaPunto(cod_punto);  
}

/*
 * Función encargada de editar los datos de un punto.
 */
function editarPunto()
{
    
    var respuesta = xajax.request({xjxfun:"actualizaPunto"}, {mode:'synchronous', parameters: [xajax.getFormValues("editapunto")]});
    
    cod_carrera = document.getElementById('codigoc').value;
    limpiar = "editacarrera";
    
    if(respuesta)
    {
        alert('No se ha podido actualizar el punto con exito.');
        
    }
    else
    {
        alert('Se ha actualizado el punto con exito.');
        cerrarAgregarPunto();
        limpia(limpiar);
        mostrarCarrera(cod_carrera);
    }
    muestraCarreras();
}

/**
 * Función encargada de limpiar formaularios.
 * @param {type} limpiar El formulario a limpiar
 */
function limpia(limpiar)
{
    document.getElementById(limpiar).reset();
}




