<?php

/* 
 * DWES7 - Aplicaciones web hibridas
 * Tarea 7: Ciclismo
 * Autor: Rubén Ángel Rodriguez Leyva
 */

/**
 * Description of Carrera
 *
 * @author RubenRL
 */
class Carrera {
    
    private $cod_carrera;
    private $nombre;
    private $fecha;
    private $punto_inicio;
    private $punto_fin;
    
    
    /**
     * Constructor de la clase carrera.
     * @param type $row
     */
    public function __construct($row) {
        $this->cod_carrera = $row['cod_carrera'];
        $this->nombre = $row['nombre'];
        $this->fecha = $row['fecha'];
        $this->punto_inicio = $row['punto_inicio'];
        $this->punto_fin = $row['punto_fin'];
    }
    
    /**
     * Método mágico que confirma datos pendientes o realizar tareas similares de limpieza.
     * @return type
     */
    public function __sleep() {
        return array('cod_carrera', 'nombre', 'fecha', 'punto_inicio', 'punto_fin');
    }
    
    // Métodos GETTER
    
    public function getCod_carrera(){
        return $this->cod_carrera;
    }
    
    public function getNombre(){
        return $this->nombre;
    }
    
    public function getFecha(){
        return $this->fecha;
    }
    
    public function getPunto_inicio(){
        return $this->punto_inicio;
    }
    
    public function getPunto_fin(){
        return $this->punto_fin;
    }
    
    // Métodos SETTER
    
    public function setCod_carrera($cod_carrera){
        $this->cod_carrera = $cod_carrera;
    }
    
    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
    
    public function setFecha($fecha){
        $this->fecha = $fecha;
    }
    
    public function setPunto_inicio($punto_inicio){
        $this->punto_inicio = $punto_inicio;
    }
    
    public function setPunto_fin($punto_fin){
        $this->punto_fin = $punto_fin;
    }
}
