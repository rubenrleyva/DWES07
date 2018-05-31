<?php

/* 
 * DWES7 - Aplicaciones web hibridas
 * Tarea 7: Ciclismo
 * Autor: Rubén Ángel Rodriguez Leyva
 */

/**
 * Description of Punto
 *
 * @author RubenRL
 */
class Punto {
   
    private $latitud;
    private $longitud;
    private $altura;
    private $cod_carrera;
    private $cod_punto;
    
    /**
     * Constructor de la clase carrera.
     * @param type $row
     */
    public function __construct($row) {
        
        $this->latitud = $row['latitud'];
        $this->longitud = $row['longitud'];
        $this->altura = $row['altura'];
        $this->cod_carrera = $row['cod_carrera'];
        $this->cod_punto = $row['cod_punto'];
    }
    
    /**
     * Método mágico que confirma datos pendientes o realizar tareas similares de limpieza.
     * @return type
     */
    public function __sleep() {
        return array('latitud', 'longitud', 'altura', 'cod_carrera', 'cod_punto');
    }
    
    // Métodos GETTER
    
    public function getLatitud(){
        return $this->latitud;
    }
    
    public function getLongitud(){
        return $this->longitud;
    }
    
    public function getAltitud(){
        return $this->altura;
    }
    
    public function getCod_punto(){
        return $this->cod_punto;
    }
    
    public function getCod_carrera(){
        return $this->cod_carrera;
    }
    
    // Métodos SETTER

    
    public function setLatitud($latitud){
        $this->latitud = $latitud;
    }
    
    public function setLongitud($longitud){
        $this->longitud = $longitud;
    }
    
    public function setAltura($altura){
        $this->altura = $altura;
    }
      
    public function setCod_carrera($cod_carrera){
        $this->cod_carrera = $cod_carrera;
    }
    
    public function setCod_punto($cod_punto){
        $this->cod_punto = $cod_punto;
    }
    
}
