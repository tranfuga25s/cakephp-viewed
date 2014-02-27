<?php
/*!
 * Viewed Behavior
 * 
 * @author Esteban Zeller
 */
class ViewedBehavior extends ModelBehavior {

    /*!
     * Saves the default preferences
     */
    private $defaults = array(
      'useModified' => true,
      'markUnviewedOnModified' => true
    );
    
    /*!
     * Saves the current preferences
     */
    public $settings = null;
    
    /*!
     * Inicializa el sistem
     */
    public function setup( Model $model, array $settings = array() ) {
        // combino las propiedades
        $this->settings = array_merge( $settings, $this->defaults );
    }
    
    /*!
     * Devuelve las preferencias
     * @param Model Modelo
     * @param value Valor de configuracion deseado, si no se especifica, devuelve todo el array
     * @return array or value, false if the value does not exists
     */
    public function getViewedSettings( Model $modelo, $value = null ) {
        if( is_null( $value ) ) {
            return $this->settings;
        }
        if( !array_key_exists( $value, $this->settings ) ) {
            return false;
        }
        return $this->settings[$value];
    }
    
    public function afterSave( Model $modelo ) {
        
    }
    
    public function afterDelete( Model $modelo ) {
        
    }
}