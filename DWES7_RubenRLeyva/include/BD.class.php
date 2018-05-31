<?php

/* 
 * DWES7 - Aplicaciones web hibridas
 * Tarea 7: Ciclismo
 * Autor: Rubén Ángel Rodriguez Leyva
 */

/**
 * Clase encargada de manejar la conexión a la base de datos además
 * de manejar las diferentes consultas.
 *
 * @author RubenRL
 */
class BD {
      
     /**
     * Método público encargado de crear la conexión a la base de datos.
     * 
     * @return \PDO
     */ 
    public function accesoBD(){
        
        $localhost = "localhost"; // El localhost
        $nombreBD = "ciclistas7"; // Nombre de la DB
        $usuario = "dwes"; // Nombre del usuario de ls BD
        $clave = "dwes"; // Clave del usuarion de la BD
        
        try{
        
            // Creamos e instanciamos el objeto de la conexión
            $conexion = new PDO('mysql:host='.$localhost.'; dbname='.$nombreBD, $usuario, $clave);
            
            // Le pasamos algunos atributos
            $conexion->exec("set names utf-8");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $conexion; // Devolvemos la conexión.
            
        // en caso de que se produzca una excepción la controlamos.    
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    

    /**
     * Función encargada de recuperar las carreras o carrera que se le indique.
     * 
     * @param type $cod_carrera El código de la carrera.
     * @return boolean
     */
    public function carreras($cod_carrera){
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
            
            if($cod_carrera){
                // Preparamos la sentencia de la consulta.
                $sql = "SELECT cod_carrera, nombre, fecha, punto_inicio, punto_fin FROM carrera WHERE cod_carrera=:cod";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(":cod", $cod_carrera);
                
                $consulta->execute();
                
                return $consulta->fetch();
                
            }else{
                
                $sql = "SELECT cod_carrera, nombre, fecha, punto_inicio, punto_fin FROM carrera";
                $consulta = $conexion->prepare($sql);
                
                $consulta->execute();
            
                $carreras = array();

                if($consulta){

                    // Mientras existan movimientos
                    $row = $consulta->fetch();
                    while($row != null){

                        // Creamos un array de movimientos pasandole los datos de cada movimiento
                        $carreras[] = new Carrera($row);
                        $row = $consulta->fetch();
                    }
                }else{

                    $carreras = false;
                }

                return $carreras;
            }
             
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función encargada de introducir la carrera en la bd.
     * 
     * @param type $carrera Los datos de la carrera.
     */
    public function introducirCarrera($carrera){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
            
            // Preparamos la consulta para insertar parte de los datos de la carrera.
            $sqlCarrera = "INSERT INTO carrera (nombre, fecha) VALUES (:nom, :fech)";
            $consultaCarera = $conexion->prepare($sqlCarrera);
            $consultaCarera->bindParam(":nom", $carrera['nombre']);
            $consultaCarera->bindParam(":fech", $carrera['fecha']);
            
            $consultaCarera->execute(); // Ejecutamos las consulta.
            
            $cod_carrera = $conexion->lastInsertId(); // Recuperamos el código de la carrera.
            
            // Preparamos la consulta para insertar el punto de paso inicial.
            $sqlPuntoInicio = "INSERT INTO punto_paso (latitud, longitud, altura, cod_carrera) VALUES (:lat, :lon, :alt, :cod_c)";
            $consultaPuntoInicio = $conexion->prepare($sqlPuntoInicio);
            $consultaPuntoInicio->bindParam(":lat", $carrera['puntoiniciolat']);
            $consultaPuntoInicio->bindParam(":lon", $carrera['puntoiniciolon']);
            $consultaPuntoInicio->bindParam(":alt", $carrera['puntoinicioalt']);
            $consultaPuntoInicio->bindParam(":cod_c", $cod_carrera);
            
            $consultaPuntoInicio->execute(); // Ejecutamos la consulta.
            
            $punto_inicio = $conexion->lastInsertId(); // Recuperamos el código del punto inicio.
            
            // Preparamos la consulta para insertar el punto de paso final.
            $sqlPuntoFinal = "INSERT INTO punto_paso (latitud, longitud, altura, cod_carrera) VALUES (:lat, :lon, :alt, :cod_c)";
            $consultaPuntoFinal = $conexion->prepare($sqlPuntoFinal);
            $consultaPuntoFinal->bindParam(":lat", $carrera['puntofinallat']);
            $consultaPuntoFinal->bindParam(":lon", $carrera['puntofinallon']);
            $consultaPuntoFinal->bindParam(":alt", $carrera['puntofinalalt']);
            $consultaPuntoFinal->bindParam(":cod_c", $cod_carrera);
            
            $consultaPuntoFinal->execute(); // Ejecutamos la consulta.
            
            $punto_fin = $conexion->lastInsertId(); // Recuperamos el código del punto final.
            
            // Preparamos la consulta para actualoizar la carrera con los datos qque faltan.
            $sqlActualizarCarrera = "UPDATE carrera SET punto_inicio=:inicio, punto_fin=:fin WHERE cod_carrera=:cod_c";
            $consultaActualizarCarrera = $conexion->prepare($sqlActualizarCarrera);
            $consultaActualizarCarrera->bindParam(":inicio", $punto_inicio);
            $consultaActualizarCarrera->bindParam(":fin", $punto_fin);
            $consultaActualizarCarrera->bindParam(":cod_c", $cod_carrera);
            
            $consultaActualizarCarrera->execute(); // Ejecutamos la consulta.
            
        // En caso de que se produzca alguna excepción la capturamos.    
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función encargada de devolver un punto de paso.
     * 
     * @param type $cod_paso
     * @return type
     */
    public function mostrarPunto($cod_paso){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
            //
            // Preparamos la sentencia de la consulta.
            $sql = "SELECT latitud, longitud, altura, cod_carrera, cod_punto FROM punto_paso WHERE cod_punto=:cod";
            $consulta = $conexion->prepare($sql);
            $consulta->bindParam(":cod", $cod_paso);
            $consulta->execute(); // Ejecutamos la consulta.
            return $consulta->fetch();
            
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función encargada de mostrar los diferentes puntos de la carrera.
     * 
     * @param type $cod_carrera
     * @param type $punto_ini
     * @param type $punto_fin
     * @return type
     */
    public function mostrarPuntos($cod_carrera, $punto_ini, $punto_fin){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
            
            // En caso de querer mostrar todos los puntos excepto el de inicio y fin
            if($cod_carrera && $punto_ini && $punto_fin){
                
                // Preparamos la sentencia de la consulta.
                $sql = "SELECT * FROM punto_paso WHERE cod_carrera=:cod AND cod_punto NOT IN (:punto_ini,:punto_fin)";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(":cod", $cod_carrera); // Le pasamos el código de la carrera
                $consulta->bindParam(":punto_ini", $punto_ini); // Le pasamos el punto de inicio
                $consulta->bindParam(":punto_fin", $punto_fin); // Le pasamos el punto final
                
            // en caso contrario, mostrar todos los puntos    
            }else if($cod_carrera){
                
                // Preparamos la sentencia de la consulta.
                $sql = "SELECT * FROM punto_paso WHERE cod_carrera=:cod";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(":cod", $cod_carrera); // Le pasamos el código de la carrera
                
            }

            $consulta->execute(); // Ejecutamos la consulta.
            
            return $consulta->fetchAll(); // Devolvemos los resultados
            
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función encargada de eliminar la carrera completamente.
     * 
     * @param type $cod_carrera
     */
    public function eliminarCarrera($cod_carrera){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
            
            $sqlCarrera = "DELETE FROM carrera WHERE cod_carrera=:cod"; // Creamos la consulta
            $consultaCarrera = $conexion->prepare($sqlCarrera); // Preparamos la consulta
            $consultaCarrera->bindParam(":cod", $cod_carrera);
            $consultaCarrera->execute(); // Ejecutamos la consulta
            
            
            $sqlPunto = "DELETE FROM punto_paso WHERE cod_carrera=:cod"; // Creamos la consulta
            $consultaPunto = $conexion->prepare($sqlPunto); // Preparamos la consulta
            $consultaPunto->bindParam(":cod", $cod_carrera);
            $consultaPunto->execute(); // Ejecutamos la consulta
            
            
        // en caso de que se produzca una excepción la controlamos.    
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función utilizada para eliminar un punto de la base de datos.
     * 
     * @param type $cod_punto
     */
    function eliminarPunto($cod_punto){
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
            
            $sqlCarrera = "DELETE FROM punto_paso WHERE cod_punto=:cod"; // Creamos la consulta
            $consultaCarrera = $conexion->prepare($sqlCarrera); // Preparamos la consulta
            $consultaCarrera->bindParam(":cod", $cod_punto);
            $consultaCarrera->execute(); // Ejecutamos la consulta
           
        // en caso de que se produzca una excepción la controlamos.    
        } catch (PDOException $ex) {
            die($ex->getMessage());
        } 
    }
    
    /**
     * Función encargada de introducir un punto en la BD.
     * 
     * @param type $punto El punto a introducir.
     */
    function añadirPunto($punto){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
        
            // Preparamos la consulta para insertar el punto de paso final.
            $sqlPunto = "INSERT INTO punto_paso (latitud, longitud, altura, cod_carrera) VALUES (:lat, :lon, :alt, :cod_c)";
            $consultaPunto = $conexion->prepare($sqlPunto);
            $consultaPunto->bindParam(":lat", $punto['puntolata']);
            $consultaPunto->bindParam(":lon", $punto['puntolona']);
            $consultaPunto->bindParam(":alt", $punto['puntoalta']);
            $consultaPunto->bindParam(":cod_c", $punto['codigoa']);
            
            $consultaPunto->execute(); // Ejecutamos la consulta
            
            
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función encargada de actualizar la carrera.
     * 
     * @param type $carrera La carrera
     */
    function actualizarCarrera($carrera){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
        
            // Preparamos la consulta para actualoizar la carrera con los datos qque faltan.
            $sqlActualizarCarrera = "UPDATE carrera SET nombre=:nom, fecha=:fec WHERE cod_carrera=:cod_c";
            $consultaActualizarCarrera = $conexion->prepare($sqlActualizarCarrera);
            $consultaActualizarCarrera->bindParam(":nom", $carrera['nombree']);
            $consultaActualizarCarrera->bindParam(":fec", $carrera['fechae']);
            $consultaActualizarCarrera->bindParam(":cod_c", $carrera['codigoe']);
            
            $consultaActualizarCarrera->execute(); // Ejecutamos la consulta.
           
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Función encargada de actualizar los datos del putno.
     * 
     * @param type $punto Los datos de punto
     */
    function actualizarPunto($punto){
        
        try{
            
            $conexion = self::accesoBD(); // Conectamos con la base de datos.
        
            // Preparamos la consulta para actualoizar la carrera con los datos qque faltan.
            $sqlActualizarPunto = "UPDATE punto_paso SET latitud=:lat, longitud=:lon, altura=:alt WHERE cod_punto=:cod";
            
            $sqlActualizaPunto = $conexion->prepare($sqlActualizarPunto);
            $sqlActualizaPunto->bindParam(":lat", $punto['puntolatap']);
            $sqlActualizaPunto->bindParam(":lon", $punto['puntolonap']);
            $sqlActualizaPunto->bindParam(":alt", $punto['puntoaltap']);
            $sqlActualizaPunto->bindParam(":cod", $punto['codigop']);
            
            $sqlActualizaPunto->execute(); // Ejecutamos la consulta.
           
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

}
