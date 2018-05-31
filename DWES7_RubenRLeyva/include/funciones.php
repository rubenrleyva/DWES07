<?php

/* 
 * DWES7 - Aplicaciones web hibridas
 * Tarea 7: Ciclismo
 * Autor: Rubén Ángel Rodriguez Leyva
 */

// Archivos requeridos para el funcionamiento de la aplicación.
require_once 'xajax_core/xajax.inc.php';
require_once 'BD.class.php';
require_once 'Carrera.class.php';
require_once 'Punto.class.php';

// Incluimos la API de Google
require_once 'include/google-api-php-client/src/Google_Client.php';
require_once 'include/google-api-php-client/src/contrib/Google_TasksService.php';


/**
 * Función encargada de mostrar las carreras en la tabla.
 * 
 * @return \xajaxResponse
 */
function tablaCarreras()
{
    
    $cod_carrera = null; // Ponemos en null el código de la carrera.
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento
    $respuesta = new xajaxResponse();
    
    // Buscamos en la BD las carreras
    $carreras = BD::carreras($cod_carrera);
    
    // Comenzamos la creación de la tabla
    $salida = "<fieldset>".
                "<legend align='center'>Carreras</legend>";
        
    // Si se nos devuelven datos
    if($carreras)
    {
            
            // Continuamos con la edición de la tabla.
            $salida .= "<table align='center'>".      
                "<tr>".
                    "<th>Fecha</th>".
                    "<th>Nombre de la carrera</th>".
                    "<th>Punto de inicio</th>".
                    "<th>Punto de fin</th>".
                    "<th>Editar</th>".
                    "<th>Emilinar</th>".
                    "<th>Mapa</th>".
                "</tr>";
            
            // Recorremos el array con las carreras y sus diferentes datos.
            foreach ($carreras as $carrera)
            {
                
                $punto_inicial = $carrera->getPunto_inicio(); // Sacamos el punto inicial
                $punto_final = $carrera->getPunto_fin(); // Sacamos el punto final

                // Sacamos el punto inicial
                $puntoI = BD::mostrarPunto($punto_inicial); 

                // Recogemos en una variable los datos del punto inicial.
                $puntoInicial = $puntoI['latitud'].','.$puntoI['longitud'].','.$puntoI['altura'];

                // Sacamos el punto final
                $puntoF = BD::mostrarPunto($punto_final);

                // Recogemos en una varible los datos del punto final.
                $puntoFinal = $puntoF['latitud'].','.$puntoF['longitud'].','.$puntoF['altura'];
                
                // Pasamos las diferentes varibles a sus respectivos datos en la web
                $respuesta->assign("puntofinallate", "value", $puntoF['latitud']); // Para el valor de la latitud.
                $respuesta->assign("puntofinallone", "value", $puntoF['longitud']); // Para el valor de la longitud.
                $respuesta->assign("puntofinalalte", "value", $puntoF['altura']); // Para el valor de la altura.
                
                $salida .= "</tr>".
                    "<td align='center'>".$carrera->getFecha()."</td>".
                    "<td align='center'>".$carrera->getNombre()."</td>".
                    "<td align='center'>(".$puntoInicial.")</td>".
                    "<td align='center'>(".$puntoFinal.")</td>".
                    '<td align="center"><input type="button" class="boton" onclick="mostrarCarrera(\''.$carrera->getCod_carrera().'\')" value="EDITAR"></button></td>'.
                    '<td align="center"><input type="button" class="boton" onclick="eliminarCarrera(\''.$carrera->getCod_carrera().'\')" value="ELIMINAR"></button></td>'.
                    '<td align="center"><input type="button" class="boton" onclick="mostrarMapa(\''.$carrera->getCod_carrera().'\')" value="MAPA"></button></td>'.
                "</tr>";
            }
        $salida .= "</table>";
    // en caso de que no existan carreras.              
    }
    else
    {
        $salida .= "<p align=center>NO HAY CARRERAS</p>"; // Avisamos mediante mensaje.
    }
    
    $salida .= "</fieldset>";
    
    // Asignamos la salida.
    $respuesta->assign("carreras", "innerHTML", $salida);
    
    // Retornamos el resultado.
    return $respuesta;  
}

/**
 * Función encargada de obtener las coordenadas de inicio de la carrera.
 * 
 * @param type $parametros Los datos de las coordenadas.
 * @return \xajaxResponse
 */
function obtenerCoordenadasInicio($parametros)
{
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento
    $respuesta = new xajaxResponse();
    
    // Hacemos la consulta a Google pasándole los parámetros necesaarios.
    $searchCoordenadas = 'https://maps.googleapis.com/maps/api/geocode/xml?address='.$parametros.'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlCoordenadas = simplexml_load_file($searchCoordenadas);
    
    // Recuperamos los parámetros de la latitud y la longitud
    $latitud = (string) $xmlCoordenadas->result[0]->geometry->location->lat;
    $longitud = (string) $xmlCoordenadas->result[0]->geometry->location->lng;
    
    // Los establecemos en la web
    $respuesta->assign("puntoiniciolat", "value", $latitud);
    $respuesta->assign("puntoiniciolon", "value", $longitud);
    
    // Hacemos la consulta a Google pasándole los parámetros de latitud y longitud
    $searchAltitud = 'https://maps.googleapis.com/maps/api/elevation/xml?locations='.$latitud.','.$longitud.'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlAltitud = simplexml_load_file($searchAltitud);
    
    // Recuperamos el parámetro de la altura
    $respuesta->assign("puntoinicioalt", "value", (string) $xmlAltitud->result[0]->elevation);
    
    // Cambiamos el valor del botón consultar mientras se realiza la consulta.
    $respuesta->assign("solicitarcoordenadasinicio", "value", "Solicitar coordenadas a Google.");
    $respuesta->assign("solicitarcoordenadasinicio", "disabled", false);
    
    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de obtener las coordenadas finales de la carrera.
 * 
 * @param type $parametros Los datos de las coordenadas.
 * @return \xajaxResponse
 */
function obtenerCoordenadasFinal($parametros)
{
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento
    $respuesta = new xajaxResponse();
    
    // Hacemos la consulta a Google pasándole los parámetros necesaarios.
    $searchCoordenadas = 'https://maps.googleapis.com/maps/api/geocode/xml?address='.$parametros.'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlCoordenadas = simplexml_load_file($searchCoordenadas);
    
    // Recuperamos los parámetros de la latitud y la longitud
    $latitud = (string) $xmlCoordenadas->result[0]->geometry->location->lat;
    $longitud = (string) $xmlCoordenadas->result[0]->geometry->location->lng;
    
    // Los establecemos en la web
    $respuesta->assign("puntofinallat", "value", $latitud);
    $respuesta->assign("puntofinallon", "value", $longitud);
    
     // Hacemos la consulta a Google pasándole los parámetros de latitud y longitud.
    $searchAltitud = 'https://maps.googleapis.com/maps/api/elevation/xml?locations='.$latitud.','.$longitud.'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlAltitud = simplexml_load_file($searchAltitud);
    
    // Recuperamos la altura y la asignamos en la web
    $respuesta->assign("puntofinalalt", "value", (string) $xmlAltitud->result[0]->elevation);
    
    // Cambiamos el valor del botón consultar mientras se realiza la consulta.
    $respuesta->assign("solicitarcoordenadasfinal", "value", "Solicitar coordenadas a Google.");
    $respuesta->assign("solicitarcoordenadasfinal", "disabled", false);
    
    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de obtener las coordenadas de un punto de paso.
 * 
 * @param type $parametros Los datos de las coordenadas.
 * @return \xajaxResponse
 */
function obtenerCoordenadasPunto($parametros, $edicion)
{
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento
    $respuesta = new xajaxResponse();
    
    // Hacemos la consulta a Google pasándole los parámetros necesaarios.
    $searchCoordenadas = 'https://maps.googleapis.com/maps/api/geocode/xml?address='.$parametros.'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlCoordenadas = simplexml_load_file($searchCoordenadas);
    
    // Recuperamos los parámetros de la latitud y la longitud
    $latitud = (string) $xmlCoordenadas->result[0]->geometry->location->lat;
    $longitud = (string) $xmlCoordenadas->result[0]->geometry->location->lng;
    
    // Si es para editar
    if($edicion)
    {
        
        // Asigamos a la web los datos
        $respuesta->assign("puntolatap", "value", $latitud);
        $respuesta->assign("puntolonap", "value", $longitud);
        
    
    }
    else // en caso contrario
    {
        
        // Asigamos a la web los datos
        $respuesta->assign("puntolata", "value", $latitud);
        $respuesta->assign("puntolona", "value", $longitud);
    }

    // Hacemos la consulta a Google pasándole los parámetros de latitud y de longitud.
    $searchAltitud = 'https://maps.googleapis.com/maps/api/elevation/xml?locations='.$latitud.','.$longitud.'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlAltitud = simplexml_load_file($searchAltitud);
    
    // Si es para editar
    if($edicion)
    {
        
        // recuperamos y asignamos el resultado a la web.
        $respuesta->assign("puntoaltap", "value", (string) $xmlAltitud->result[0]->elevation);
    }
    else // en caso contrario
    {
        // recuperamos y asignamos el resultado a la web.
        $respuesta->assign("puntoalta", "value", (string) $xmlAltitud->result[0]->elevation);
    }
   
    // Cambiamos el valor del botón consultar mientras se realiza la consulta.
    $respuesta->assign("solicitarcoordenadaspunto","value","Solicitar coordenadas a Google.");
    $respuesta->assign("solicitarcoordenadaspunto","disabled", false);
    
    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de validar la carrera.
 * 
 * @param type $carrera La carrera
 * @return \xajaxResponse
 */
function validarNuevaCarrera($carrera){
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Ponemos los errores en false.
    $error = false;
    
    // Validamos que exista el campo de la latitud inicial
    if (!sinCamposVacios($carrera['puntoiniciolat'])) {
        $respuesta->assign("errorpuntoinicio", "innerHTML", "Faltan datos pulsa sobre Solicitar coordenadas a Google.");
        $error = true;
    }else{
        $respuesta->clear("errorpuntoinicio", "innerHTML");
    }
    
    // Validamos que exista el campo de la longitud inicial
    if (!sinCamposVacios($carrera['puntoiniciolon'])) {
        $respuesta->assign("errorpuntoinicio", "innerHTML", "Faltan datos pulsa sobre Solicitar coordenadas a Google.");
        $error = true;
    }else{
        $respuesta->clear("errorpuntoinicio", "innerHTML");
    }
    
    // Validamos que exista el campo de la altitud inicial
    if (!sinCamposVacios($carrera['puntoinicioalt'])) {
        $respuesta->assign("errorpuntoinicio", "innerHTML", "Faltan datos pulsa sobre Solicitar coordenadas a Google.");
        $error = true;
    }else{
        $respuesta->clear("errorpuntoinicio", "innerHTML");
    }
    
    // Validamos que exista el campo de la latitud final
    if (!sinCamposVacios($carrera['puntofinallat'])) {
        $respuesta->assign("errorpuntofinal", "innerHTML", "Pulsa sobre Obtener coordenadas Google.");
        $error = true;
    }else{
        $respuesta->clear("errorpuntofinal", "innerHTML");
    }
    
    // Validamos que exista el campo de la longitud final
    if (!sinCamposVacios($carrera['puntofinallon'])) {
        $respuesta->assign("errorpuntofinal", "innerHTML", "Pulsa sobre Obtener coordenadas Google.");
        $error = true;
    }else{
        $respuesta->clear("errorpuntofinal", "innerHTML");
    }
    
    // Validamos que exista el campo de la altitud final
    if (!sinCamposVacios($carrera['puntofinalalt'])) {
        $respuesta->assign("errorpuntofinal", "innerHTML", "Pulsa sobre Obtener coordenadas Google.");
        $error = true;
    }else{
        $respuesta->clear("errorpuntofinal", "innerHTML");
    }
    
    // Validamos el nombre.
    if (!sinCamposVacios($carrera['nombre'])) {
        $respuesta->assign("errornombre", "innerHTML", "El nombre no debe de estar vacío.");
        $error = true;
    }else{
        $respuesta->clear("errornombre", "innerHTML");
    }
    
    // Validamos la fecha
    if(!validarFecha($carrera['fecha'])){
        $respuesta->assign("errorfecha", "innerHTML", "La fecha no puede estar vacía.");
        $error = true;
    }else{
        $respuesta->clear("errorfecha", "innerHTML");
    }
    

    // En caso de no existir errores.
    if (!$error) {

        // Actualizamos la base de datos.
        BD::introducirCarrera($carrera);
        
        // Devolvemos el valor a la tabla.
        $respuesta->setReturnValue(true);
    }
     
    // Retornamos el resultado.
    return $respuesta;    
}

/**
 * Función encargada de validar la edición de una carrera carrera.
 * 
 * @param type $carrera
 * @return \xajaxResponse
 */
function validarEdicionCarrera($carrera){
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Ponemos los errores en false.
    $error = false;
    
    // Asignamos el nombre de la carrera a su variable en la web
    $respuesta->assign("nombree", "value", $carrera['nombree']);
    
    // Validamos el nombre.
    if (!sinCamposVacios($carrera['nombree'])) {
        $respuesta->assign("errornombree", "innerHTML", "El nombre no debe de estar vacío.");
        $error = true;
    }else{
        $respuesta->clear("errornombre", "innerHTML");
    }
    
    // Validamos la fecha
    if(!validarFecha($carrera['fechae'])){
        $respuesta->assign("errorfechae", "innerHTML", "La fecha no puede estar vacía.");
        $error = true;
    }else{
        $respuesta->clear("errorfecha", "innerHTML");
    }
    

    // En caso de no existir errores.
    if (!$error) {
        
        // Actualizamos la base de datos.
        BD::actualizarCarrera($carrera);
        
        $respuesta->setReturnValue(true);
    }
     
    // Retornamos el resultado.
    return $respuesta;    
}


/**
 * Función encargada de validar el email de voluntario.
 * 
 * @param type $fecha El email.
 * @return type El formato de email.
 */
function validarFecha($fecha){
    if($fecha !== null){
        return true;
    }else{
        return false;
    }
}
 
/**
 * Función encargada de validar el login del usuario.
 * 
 * @param type $campo El login del voluntario
 * @return boolean True o False
 */
function sinCamposVacios($campo){
    if(strlen($campo) > 0){
        return true;
    }else{
        return false;
    }
}

/**
 * Función que muestra los datos de la carrera a editar.
 * 
 * @param type $cod_carrera El código de la carrera.
 * @return \xajaxResponse
 */
function editaCarrera($cod_carrera){
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Devolvemos la carrera según su código.
    $carrera = BD::carreras($cod_carrera);
    
    // En caso de existir carreras
    if($carrera)
    {
        // Asignamos los valores de las variables de la web.
        $respuesta->assign("codigoe", "value", $carrera['cod_carrera']);
        $respuesta->assign("fechae", "value", $carrera['fecha']);
        $respuesta->assign("nombree", "value", $carrera['nombre']);
    }

    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función que muestra los diferentes puntos de la carrera en una tabla.
 * 
 * @param type $cod_carrera El código de la carrera.
 * @return \xajaxResponse
 */
function tablaPuntos($cod_carrera)
{

     // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Devolvemos el voluntario según su login.
    $carrera = BD::carreras($cod_carrera);
    
    // Asigamos las variables de la web con sus respectivos datos.
    $respuesta->assign("fechae", "value", $carrera['fecha']);
    $respuesta->assign("nombree", "value", $carrera['nombre']);
    
    // Recogemos los datos del punto de inicio y del punto final.
    $punto_inicial = $carrera['punto_inicio'];
    $punto_final = $carrera['punto_fin'];
    
    // Averiguamos los datos del punto inical.
    $puntoI = BD::mostrarPunto($punto_inicial);
    
    // Averiguamos los datos del punto final.
    $puntoF = BD::mostrarPunto($punto_final);
    
    // Averiguamos los datos de los diferentes puntos intermedios si existen
    $puntosIntermedios = BD::mostrarPuntos($cod_carrera, $punto_inicial, $punto_final);
    
    // Creamos una tabla con los diferentes datos.
    $salida =  '<table style="width:100%">'.
            '<tr>'.
            '<th><input type="button" class="boton" onclick="agregaPunto(\''.$carrera['cod_carrera'].'\')" value="Agregar punto" /></th>'.
                '<th><label for="puntoiniciolat" >Latitud:</label></th>'.
                '<th><label for="puntoiniciolon" >Longitud:</label></th>'.
                '<th><label for="puntoinicioalt" >Altitud:</label></th>'.
                '<th><label for="puntoinicioalt" >Editar:</label></th>'.
                '<th><label for="puntoinicioalt" >Eliminar:</label></th>'.
            '</tr>'.
            '<tr>'.
                '<td><label for="puntoinicio" >Punto de salida:</label></td>'.
                '<td><input type="text" name="puntoiniciolate" id="puntoiniciolate" value='.$puntoI['latitud'].' readonly style="background-color: green;"/></td>'.
                '<td><input type="text" name="puntoiniciolone" id="puntoiniciolone" value='.$puntoI['longitud'].' readonly style="background-color: green;"/></td>'.
                '<td><input type="text" name="puntoinicioalte" id="puntoinicioalte" value='.$puntoI['altura'].' readonly style="background-color: green;"/></td>'.
                '<td colspan="2">DATOS NO EDITABLES</td>'.
            '</tr>';
    
    // Si existen puntos intermedios
    if($puntosIntermedios)
    {
        
        // los recorremos
        foreach ($puntosIntermedios as $puntosI)
        {
            
            // y los incluimos en la tabla.
            $salida .= '<td><label for="puntoinicio" >Punto de paso:</label></td>'.
                '<td><input type="text" name="punto" id="punto" value='.$puntosI['latitud'].' style="background-color: green;"/></td>'.
                '<td><input type="text" name="punto" id="punto" value='.$puntosI['longitud'].' style="background-color: green;"/></td>'. 
                '<td><input type="text" name="punto" id="punto" value='.$puntosI['altura'].' style="background-color: green;"/></td>'. 
                '<td align="center"><input type="button" class="boton" onclick="editaPunto(\''.$puntosI['cod_punto'].'\')" value="EDITAR"></button></td>'.
                '<td align="center"><input type="button" class="boton" onclick="eliminaPunto(\''.$puntosI['cod_punto'].'\')" value="ELIMINAR"></button></td>'."</tr>";
        }
    }
    
    // continuamos con la tabla
    $salida .= '<tr>'.
            '<td><label for="puntoinicio" >Punto de llegada:</label></td>'.
            '<td><input type="text" name="puntoiniciolate" id="puntoiniciolate" value='.$puntoF['latitud'].' readonly style="background-color: green;"/></td>'.
            '<td><input type="text" name="puntoiniciolone" id="puntoiniciolone" value='.$puntoF['longitud'].' readonly style="background-color: green;"/></td>'.
            '<td><input type="text" name="puntoinicioalte" id="puntoinicioalte" value='.$puntoF['altura'].' readonly style="background-color: green;"/></td>'.
            '<td colspan="2">DATOS NO EDITABLES</td>'.
        '</tr>';
        
    $salida .= "</table>"; // finalizamos la tabla.
    
    // Asignamos la tabla al div
    $respuesta->assign("puntospaso", "innerHTML", $salida);
    
    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de eliminar la carrea y sus diferentes puntos.
 * 
 * @param type $cod_carrera El código de la carrera.
 * @return boolean
 */
function eliminaCarrera($cod_carrera)
{
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Eliminamos la carrera de la BD
    $carrera = BD::eliminarCarrera($cod_carrera);
    
    // En caso de eliminarlo correctamente.
    if($carrera)
    {
        $respuesta = true; // devolvemos true.
    }
    else
    {
        $respuesta = false; // en caso contrarion false.
    }

    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de eliminar un punto de la carrera.
 * 
 * @param type $cod_punto El código del punto a eliminar.
 * @return boolean
 */
function eliminarPunto($cod_punto)
{
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Eliminamos el punto de la BD
    $carrera = BD::eliminarPunto($cod_punto);
    
    // En caso de eliminarlo correctamente.
    if($carrera)
    {
        $respuesta = true; // devolvemos true.
    }
    else
    {
        $respuesta = false; // en caso contrarion false.
    }

    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de agregar un punto a la carrera.
 * 
 * @param type $punto El código del punto.
 * @return \xajaxResponse
 */
function agregarPunto($punto)
{
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Agregamos el punto.
    $nuevoPunto = BD::añadirPunto($punto);
    
    // Si se agrega correctamente.
    if($nuevoPunto){
        $respuesta = true; // devolvemos true.
    }else{
        $respuesta = false; // en caso contrarion false.
    }

    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de devolver los puntos del mapa.
 * 
 * @param type $cod_carrera
 * @return \xajaxResponse
 */
function devolverPuntosMapa($cod_carrera)
{
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Se devuelven los puntos de la carrera.
    $puntos = BD::mostrarPuntos($cod_carrera, null, null);

    // Los pasamos con un array
    $puntoArray = array_values($puntos);

    // Y llamamos a la función encargada de mostrar el mapa.
    $respuesta->call("muestraMapa", $puntoArray);

    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de editar un punto: mostrando en pantalla los datos del mismo.
 * 
 * @param type $cod_punto El código del putno.
 * @return \xajaxResponse
 */
function editaPunto($cod_punto)
{
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Averiguamos los datos del punto a mostrar
    $punto = BD::mostrarPunto($cod_punto);
    
    // Asignamos a la cada etiqueta del la web su dato.
    $respuesta->assign("codigoc", "value", $punto['cod_carrera']);
    $respuesta->assign("codigop", "value", $punto['cod_punto']);
    $respuesta->assign("puntolatap", "value", $punto['latitud']);
    $respuesta->assign("puntolonap", "value", $punto['longitud']);
    $respuesta->assign("puntoaltap", "value", $punto['altura']);
    
    // Averiguamos mediante la latitud y la longitud la dirección
    $searchCoordenadas = 'https://maps.googleapis.com/maps/api/geocode/xml?latlng='.$punto['latitud'].','.$punto['longitud'].'&key=AIzaSyDEmDdwCzWwwyhFKUG3HM6LikettswuCqA';
    $xmlCoordenadas = simplexml_load_file($searchCoordenadas);
    
    // Se la pasamos a una variable
    $direccionCompleta = (string) $xmlCoordenadas->result[0]->formatted_address;
    
    // Se la asignamos a la etiqueta correspondiente en la web
    $respuesta->assign("direccion", "value", $direccionCompleta);

    // Retornamos el resultado.
    return $respuesta;
}

/**
 * Función encargada de actualizar un punto.
 * 
 * @param type $punto Los datos del punto.
 * @return \xajaxResponse
 */
function actualizaPunto($punto)
{
    
    // Se instancia y utiliza para devolver al navegador los comandos resultado del procesamiento.
    $respuesta = new xajaxResponse();
    
    // Hacemos la actualización en la base de datos.
    $actualizacion = BD::actualizarPunto($punto);

    // En caso de eliminarlo correctamente.
    if($actualizacion)
    {
        $respuesta = true; // devolvemos true.
    }
    else
    {
        $respuesta = false; // en caso contrarion false.
    }
    
    // Retornamos el resultado.
    return $respuesta;
}


// Objeto de la clase xajax
$xajax = new xajax();

// Conjunto de funciones PHP del servidor que estarán disponibles para ser ejecutadas de forma asíncrona.

$xajax->register(XAJAX_FUNCTION, "tablaCarreras");
$xajax->register(XAJAX_FUNCTION, "tablaPuntos");
$xajax->register(XAJAX_FUNCTION, "obtenerCoordenadasInicio");
$xajax->register(XAJAX_FUNCTION, "obtenerCoordenadasFinal");
$xajax->register(XAJAX_FUNCTION, "obtenerCoordenadasPunto");
$xajax->register(XAJAX_FUNCTION, "validarNuevaCarrera");
$xajax->register(XAJAX_FUNCTION, "validarEdicionCarrera");
$xajax->register(XAJAX_FUNCTION, "eliminaCarrera");
$xajax->register(XAJAX_FUNCTION, "eliminarPunto");
$xajax->register(XAJAX_FUNCTION, "editaCarrera");
$xajax->register(XAJAX_FUNCTION, "agregarPunto");
$xajax->register(XAJAX_FUNCTION, "devolverPuntosMapa");
$xajax->register(XAJAX_FUNCTION, "editaPunto");
$xajax->register(XAJAX_FUNCTION, "actualizaPunto");

// Configuración de la ruta de acceso a la carpeta xajax-js
$xajax->configure('javascript URI', 'include');

// Método encargado de procesaar las llamadas que reciba
$xajax->processRequest();