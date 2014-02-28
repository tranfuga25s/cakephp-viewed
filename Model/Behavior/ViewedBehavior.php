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
      'markUnviewedOnModified' => true,
      'fields' => array(
          'viewed' => 'viewed',
          'modified' => 'modifiedAfterViewed'
      )
    );

    /*!
     * Saves the current preferences
     */
    public $settings = null;

    /*!
     * Inicializa el sistem
     */
    public function setup( Model $model, $settings = array() ) {
        // combino las propiedades
        $this->settings = array_replace_recursive( $this->defaults, $settings );
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

    /**
     * Funcion que actualiza los datos del modelo relacionado
     * @param Model $modelo
     * @param boolean $created
     * @param array $options
     */
    public function afterSave( Model $modelo, $created, $options = array() ) {
        if( $created ) {
            // Genero una nueva entrada en el sistema para este elemento recién creado
            $data = array(
                'Viewed' => array(
                    'model' => $modelo->alias,
                    'model_id' => $modelo->id,
                    'viewed' => false,
                    'modified' => false
                )
            );
            $this->Viewed = ClassRegistry::init('Viewed.Viewed');
            $this->Viewed->save( $data );
            return;
        } else {
            // Actualizo la información del registro
            $this->Viewed = ClassRegistry::init( 'Viewed.Viewed' );
            $data = $this->Viewed->find( 'first', array(
                'conditions' => array( 'model' => $modelo->alias,
                                       'model_id' => $modelo->id
                                ),
                'fields' => array( $modelo->primaryKey ),
                'recursive' => -1
            ) );
            if( count( $data ) <= 0 ) {
                $data = array(
                    'Viewed' => array(
                        'model' => $modelo->alias,
                        'model_id' => $modelo->id
                    )
                );
            }
            $data['Viewed']['modified'] = true;
            $data['Viewed']['viewed'] = false;
            $this->Viewed->save( $data );
            return;
        }
    }

    /**
     *
     */
    public function afterDelete( Model $modelo ) {
        $this->Viewed = ClassRegistry::init('Viewed.Viewed');
        $this->Viewed->deleteAll( array( 'model' => $modelo->alias, 'model_id' => $modelo->id ) );
    }

    /**
     * Funcion para ingresar los campos relacionados
     * @param modelo Modelo
     * @param resultados Resultados devueltos
     * @param primary boolean ?
     * @return
     */
    public function afterFind( Model $modelo, $results, $primary = false ) {
        if( count( $results ) > 0 ) {
            $this->Viewed = ClassRegistry::init('Viewed.Viewed');
            foreach( $results as &$result ) {
                $data = $this->Viewed->find( 'first', array(
                    'conditions' => array(
                        'model' => $modelo->alias,
                        'model_id' => $result[$modelo->alias][$modelo->primaryKey]
                    ),
                    'recursive' => -1,
                    'fields' => array( 'viewed' )
                ));
                if( count( $data ) > 0 ) {
                    $result[$modelo->alias][$this->settings['fields']['viewed']] = $data['Viewed']['viewed'];
                }
            }
        }
        return $results;
    }
}