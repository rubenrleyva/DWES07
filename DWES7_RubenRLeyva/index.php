<?php

/* 
 * DWES7 - Aplicaciones web hibridas
 * Tarea 7: Ciclismo
 * Autor: Rubén Ángel Rodriguez Leyva
 */

// Archivos requeridos
require_once("include/funciones.php");

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>DWES 7 - Aplicaciones web híbridas.</title>

        <?php 
            // Código propio javascript
            $xajax->printJavascript();
        ?>
        
        <style>
           *{
                text-align: center;
            }
            
            table, th, td {

                vertical-align:middle;
                margin: 0 auto;
                padding: 10px;
                border: 1px solid black;
            }
            
            #mapdiv {    
                height: 500px;
                width: auto;
                margin: 0 auto;
                border: 1px solid black;
            }
            
            .boton{
                background-color: #4CAF50;
            }
            
            #botonnuevo, #botoncerrar{
                margin: 0 auto;
            }
 
        </style>
        
        <script type="text/javascript" src="include/funciones.js"></script>
        <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
        
    </head>
    <body>
        
        <header>
            <h1 align="center">DWES 7 - Ciclismo</h1>
        </header>
        
        <div id="carreras">
            <script type="text/javascript">muestraCarreras();</script>
         
        </div>
        </br>
        <div id="nueva">  
            <input type='button' class='boton' id="botonnuevo" onclick="nuevaCarrera();" value='Nueva Carrera' />
        </div>
        
        <div id='nuevo' style="display: none">
        </br>
        <form id="nuevacarrera" action="javascript:void(null);" onsubmit="ingresarCarrera();">
            <fieldset>
                <legend align="center">Nueva Carrera</legend>
                <div align="center">
                    <span id="errorfecha" class="error" for="fecha" style="color: red"></span></br>
                    <label for='fecha' >Fecha:</label><br/>
                    <input type='date' name='fecha' id='fecha' step="1" min="<?php echo date("Y-m-d");?>" required/><br/>
                    </br><span id="errornombre" class="error" for="nombre" style="color: red"></span></br>
                    <label for='nombre' >Nombre:</label><br/>
                    <input type='text' name='nombre' id='nombre' maxlength="50" /><br/>
                    </br><span id="errorpuntoinicio" class="error" for="puntoinicio" style="color: red"></span></br>
                    <label for='puntoinicio' >Punto de salida:</label><br/>
                    <input type='text' name='puntoinicio' id='puntoinicio' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='puntoiniciolat' >lat:</label>
                    <input type='text' name='puntoiniciolat' id='puntoiniciolat' readonly style="background-color: green;"/>
                    <label for='puntoiniciolon' >lon:</label>
                    <input type='text' name='puntoiniciolon' id='puntoiniciolon' readonly style="background-color: green;"/>
                    <label for='puntoinicioalt' >alt:</label>
                    <input type='text' name='puntoinicioalt' id='puntoinicioalt' readonly style="background-color: green;"/><br/><br/>
                    <input class='boton' type='button' id='solicitarcoordenadasinicio' onclick="solicitarCoordenadasInicio();" value='Solicitar coordenadas a Google' /><br/><br/> 
                    <span id="errorpuntofinal" class="error" for="puntofinal" style="color: red"></span></br>
                    <label for='puntofinal' >Punto de llegada:</label><br/>
                    <input type='text' name='puntofinal' id='puntofinal' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='puntofinallat' >lat:</label>
                    <input type='text' name='puntofinallat' id='puntofinallat' readonly style="background-color: green;"/>
                    <label for='puntofinallon' >lon:</label>
                    <input type='text' name='puntofinallon' id='puntofinallon' readonly style="background-color: green;"/>
                    <label for='puntofinalalt' >alt:</label>
                    <input type='text' name='puntofinalalt' id='puntofinalalt' readonly style="background-color: green;"/><br/><br/>
                    <input class='boton' type='button' id='solicitarcoordenadasfinal' onclick="solicitarCoordenadasFinal();" value='Solicitar coordenadas a Google' /><br/><br/>      
                    <input class='boton' type='submit' id='insertar' name='insertar' value='Insertar carrera' /><input type='button' class='boton' onclick="cancelarNuevaCarrera();" value='Cancelar' />
                </div>
            </fieldset>
        </form>
        </div>
        
        <div id='edicion' style="display: none">
        </br>
        <form id="editacarrera" action="javascript:void(null);" onsubmit="editarCarrera();">
            <fieldset>
                <legend align="center">Edita Carrera</legend>
                <div align="center">
                    <input type='hidden' name='codigoe' id='codigoe'/>
                    <span id="errorfechae" class="error" for="fecha" style="color: red"></span></br>
                    <label for='fechae' >Fecha:</label><br/>
                    <input type='date' name='fechae' id='fechae' step="1" min="<?php echo date("Y-m-d");?>" required/><br/>
                    <span id="errornombree" class="error" for="nombre" style="color: red"></span></br>
                    <label for='nombree' >Nombre:</label><br/>
                    <input type='text' name='nombree' id='nombree' maxlength="50" /><br/>
                    <span id="errorpuntoinicio" class="error" for="puntoinicio" style="color: red"></span></br>
                    <div id="puntospaso">
                    </div>
                    <p>NOTA: Al pulsar en 'Guardar cambios' solo se actualizarán los datos de Fecha y Nombre.</p>
                    <p>NOTA: Los datos de los puntos de paso se guardan automáticamente al crearlos, por lo que para eliminar alguno de los mismos pulsa sobre el botón 'ELIMINAR'.</p>
                    <br/><input type="submit" class='boton' id="insetar" name="insetar" value="Guardar cambios" /><input type="button" class='boton' onclick="cancelarEdicionCarrera();" value="Cancelar" />
                </div>
            </fieldset>
        </form>
        </div>
        
        <div id='agrega' style="display: none">
        </br>
        <form id="agregapuntos" action="javascript:void(null);" onsubmit="agregarPunto();">
            <fieldset>
                <legend align="center">Agregar Punto</legend>
                <div align="center">
                    <label for='codigoa' >Código Carrera:</label><br/>
                    <input type='text' name='codigoa' id='codigoa' readonly/><br/><br/>
                    <label for='calle' >Calle:</label><br/>
                    <input type='text' name='calle' id='calle' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='ciudad' >Ciudad:</label><br/>
                    <input type='text' name='ciudad' id='ciudad' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='pais' >País:</label><br/>
                    <input type='text' name='pais' id='pais' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='cp' >CP:</label><br/>
                    <input type='text' name='cp' id='cp' maxlength="5" /><br/><br/>
                    <input type='button' class='boton' id='solicitarcoordenadaspunto' onclick="solicitarCoordenadasPunto(true);" value='Solicitar coordenadas a Google' /><br/><br/> 
                    <table style="width:100%">
                        <tr>
                            <th><label for='puntolata' >Latitud:</label></th>
                            <th><label for='puntolona' >Longitud:</label></th> 
                            <th><label for='puntoalta' >Altitud:</label></th>
                        </tr>
                        <tr>
                            <td><input type='text' name='puntolata' id='puntolata' readonly style="background-color: green;"/></td> 
                            <td><input type='text' name='puntolona' id='puntolona' readonly style="background-color: green;"/></td>
                            <td><input type='text' name='puntoalta' id='puntoalta' readonly style="background-color: green;"/></td>
                        </tr>
                    </table>
                    <br/>
                    <input type='submit' class='boton' id='insertarpunto' name='insertarpunto' value='Agregar punto de paso' /><input type='button' class='boton' onclick="cerrarAgregarPunto();" value='Cerrar' />
                </div>
            </fieldset>
        </form>
        </div>
        
        <div id='edita' style="display: none">
        </br>
        <form id="editapunto" action="javascript:void(null);" onsubmit="editarPunto();">
            <fieldset>
                <legend align="center">Editar Punto</legend>
                <div align="center">
                    <label for='codigoc'>Código carrera:</label><br/>
                    <input type='text' name='codigoc' id='codigoc' readonly/><br/><br/>
                    <label for='codigop'>Código punto:</label><br/>
                    <input type='text' name='codigop' id='codigop' readonly/><br/><br/>
                    <label for='direccion' >Dirección Completa:</label><br/>
                    <input type='text' name='direccion' id='direccion' style="width:50%;" readonly/><br/><br/>
                    <p>NOTA: Arriba se indica la dirección completa, edítala en las siguientes casillas.</p>
                    <label for='calle' >Calle:</label><br/>
                    <input type='text' name='callep' id='callep' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='ciudad' >Ciudad:</label><br/>
                    <input type='text' name='ciudadp' id='ciudadp' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='pais' >País:</label><br/>
                    <input type='text' name='paisp' id='paisp' style="width:50%;" maxlength="150" /><br/><br/>
                    <label for='cp' >CP:</label><br/>
                    <input type='text' name='cpp' id='cpp' maxlength="5" /><br/><br/>
                    <input type='button' class='boton' id='solicitarcoordenadaspunto' onclick="solicitarCoordenadasPunto(false);" value='Solicitar coordenadas a Google' /><br/><br/> 
                    <table style="width:100%">
                        <tr>
                            <th><label for='puntolatap' >Latitud:</label></th>
                            <th><label for='puntolonap' >Longitud:</label></th> 
                            <th><label for='puntoaltap' >Altitud:</label></th>
                        </tr>
                        <tr>
                            <td><input type='text' name='puntolatap' id='puntolatap' readonly style="background-color: green;"/></td> 
                            <td><input type='text' name='puntolonap' id='puntolonap' readonly style="background-color: green;"/></td>
                            <td><input type='text' name='puntoaltap' id='puntoaltap' readonly style="background-color: green;"/></td>
                        </tr>
                    </table>
                    <br/>
                    <input type='submit' class='boton' id='actualizarpunto' name='actualizarpunto' value='Actualizar punto' /><input type='button' class='boton' onclick="cerrarAgregarPunto();" value='Cerrar' />
                </div>
            </fieldset>
        </form>
        </div>
        
        </br>
        <div id="mapdiv" style="display: none">
            
        </div>
        </br>
        <input type='button' class='boton' id="botoncerrar" style="display: none" onclick="cerrarMapa();" value='Cerrar Mapa' />
         <script async defer
                 accesskey=""src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8wYkxk_W0CTaPfR5eaOqzaY7MVDq5IeM&callback">
        </script>
        <footer>
            <h2 align="center">Rubén Ángel Rodriguez Leyva</h2>
        </footer>
    </body>
</html>
