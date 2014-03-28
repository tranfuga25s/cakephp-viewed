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
      ),
      'userFunction' => 'getCurrentUser'
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
            $id_usuario = 0;
            if( method_exists( $modelo, $this->settings['userFunction'] ) ) {
                $function_name = $this->settings['userFunction'];
                $id_usuario = $modelo->$function_name();
            }
            $data = array(
                'Viewed' => array(
                    'model' => $modelo->alias,
                    'model_id' => $modelo->id,
                    'viewed' => false,
                    'modified' => false,
                    'user_id' => $id_usuario
                )
            );
            $this->Viewed = ClassRegistry::init('Viewed.Viewed');
            $this->Viewed->create();
            $this->Viewed->save( $data );
            ClassRegistry::removeObject( 'Viewed.Viewed' );
            return;
        } else {
            // Actualizo la información del registro

            // Busco los registros relacionados en Viewed.
            $this->Viewed = ClassRegistry::init( 'Viewed.Viewed' );
            $data = $this->Viewed->find( 'all', array(
                'conditions' => array( 'model' => $modelo->alias,
                                       'model_id' => $modelo->id
                                ),
                'fields' => array( 'id', 'viewed', 'user_id' ),
                'recursive' => -1
            ) );

            // Consulto el usuario actual ( si existe )
            $id_usuario = 0;
            if( method_exists( $modelo, $this->settings['userFunction'] ) ) {
                $function_name = $this->settings['userFunction'];
                $id_usuario = $modelo->$function_name();
            }

            // Si no existe ningun registro, genero el registro para el usuario actual
            if( count( $data ) <= 0 ) {
                $data = array(
                    'Viewed' => array(
                        'model' => $modelo->alias,
                        'model_id' => $modelo->id,
                        'user_id' => $id_usuario,
                        'modified' => true,
                        'viewed' => true
                    )
                );
                $this->Viewed->create();
                $this->Viewed->save( $data );
                ClassRegistry::removeObject( 'Viewed.Viewed' );
                return;
            } else if( $id_usuario == 0 ) {
                // No hay diferenciación de usuario - El sistema se comporta como comparador ( algun usuario vio el registro )
                $data['Viewed']['modified'] = true;
                $data['Viewed']['viewed'] = false;
                $this->Viewed->save( $data );
                ClassRegistry::removeObject( 'Viewed.Viewed' );
                return;
            }

            // Coloco como modificado los registros de los usuarios que no son $id_usuario
            $this->Viewed->updateAll(
                    array( 'modified' => true, 'viewed' => false ),
                    array( 'model' => $modelo->alias,
                           'model_id' => $modelo->id,
                           'NOT' => array( 'user_id' => $id_usuario ) )
            );
            
            

            // Busco la cantidad de registros que coinciden con el usuario actual
            if( Set::matches('/Viewed[id='.$id_usuario.']', $data ) ) {
                // El registro del usuario actual debe ser puesto como modificado = false y Viewed = true
                $this->Viewed->updateAll(
                        array( 'modified' => false, 'viewed' => true ),
                        array( 'model' => $modelo->alias,
                               'model_id' => $modelo->id,
                               'user_id' => $id_usuario )
                );
            } else {
                // Tengo que crear el registro para el usuario actual
                $data = array(
                    'Viewed' => array(
                        'model' => $modelo->alias,
                        'model_id' => $modelo->id,
                        'user_id' => $id_usuario,
                        'modified' => false,
                        'viewed' => true
                    )
                );
                $this->Viewed->create();
                $this->Viewed->save( $data );
            }
            ClassRegistry::removeObject( 'Viewed.Viewed' );
            return;
        }
    }

    /**
     *
     */
    public function afterDelete( Model $modelo ) {
        $this->Viewed = ClassRegistry::init('Viewed.Viewed');
        $this->Viewed->deleteAll( array( 'model' => $modelo->alias, 'model_id' => $modelo->id ) );
        ClassRegistry::removeObject( 'Viewed.Viewed' );
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
            ///@TODO Agregar mejora sacando todos los ID's a una sola consulta ?
            foreach( $results as &$result ) {
                if( is_null( $result ) ) { continue; }
                if( !array_key_exists( $modelo->alias, $result ) ) { continue; }
                if( !array_key_exists( $modelo->primaryKey, $result[$modelo->alias] ) ) { continue; }
                $data = $this->Viewed->find( 'first', array(
                    'conditions' => array(
                        'model' => $modelo->alias,
                        'model_id' => $result[$modelo->alias][$modelo->primaryKey]
                    ),
                    'recursive' => -1,
                    'fields' => array( 'viewed', 'modified' )
                ));
                if( count( $data ) > 0 ) {
                    $result[$modelo->alias][$this->settings['fields']['viewed']] = $data['Viewed']['viewed'];
                    $result[$modelo->alias][$this->settings['fields']['modified']] = $data['Viewed']['modified'];
                } else {
                    $result[$modelo->alias][$this->settings['fields']['viewed']] = false;
                    $result[$modelo->alias][$this->settings['fields']['modified']] = false;
                }
            }
            ClassRegistry::removeObject( 'Viewed.Viewed' );
        }
        return $results;
    }

    /**
     *
     */
    public function isViewed( Model $modelo ) {
        if( is_null( $modelo->id ) || !$modelo->id ) { return -1; }

        $this->Viewed = ClassRegistry::init( 'Viewed.Viewed' );
        $id_usuario = 0;
        if( method_exists( $modelo, $this->settings['userFunction'] ) ) {
            $function_name = $this->settings['userFunction'];
                $id_usuario = $modelo->$function_name();
        }
        $data = $this->Viewed->find( 'first', array(
            'conditions' => array( 'model' => $modelo->alias,
                                   'model_id' => $modelo->id,
                                   'user_id' => $id_usuario ),
            'fields' => array( 'viewed' )
        ));

        // Si count(data) == 0 => no existe registro para el usuario actual
        if( count( $data ) <= 0 || !array_key_exists( 'Viewed', $data ) ) {
            return false;
        }
        ClassRegistry::removeObject( 'Viewed.Viewed' );
        return $data['Viewed']['viewed'];
    }

    /**
     *
     */
    public function isModifiedAfterViewed( Model $modelo ) {
        if( is_null( $modelo->id ) || !$modelo->id ) { return -1; }

        $this->Viewed = ClassRegistry::init( 'Viewed.Viewed' );
        $data = $this->Viewed->find( 'first', array(
            'conditions' => array( 'model' => $modelo->alias,
                                   'model_id' => $modelo->id ),
            'fields' => array( 'modified' )
        ));
        if( count( $data ) <= 0 || !array_key_exists( 'Viewed', $data ) ) {
            return -1;
        }
        ClassRegistry::removeObject( 'Viewed.Viewed' );
        return $data['Viewed']['modified'];
    }

    /**
     * Setea el campo y modelo actual como visto para el usuario seleccionado
     */
    public function setViewed( Model $modelo ) {
        // Si el modelo no tiene seteado el ID devuelvo falso
        if( is_null( $modelo->id ) || !$modelo->id ) { return false; }
        
        $id_usuario = 0;
        if( method_exists( $modelo, $this->settings['userFunction'] ) ) {
            $function_name = $this->settings['userFunction'];
                $id_usuario = $modelo->$function_name();
        }

        $this->Viewed = ClassRegistry::init( 'Viewed.Viewed' );
        $data = $this->Viewed->find( 'first', array(
            'conditions' => array( 'model' => $modelo->alias,
                                   'model_id' => $modelo->id,
                                   'user_id' => $id_usuario ),
            'fields' => array( 'modified', 'viewed', 'id' )
        ));
        if( count( $data ) <= 0 || !array_key_exists( 'Viewed', $data ) ) {
            // Creo el registro ya que no existe
            $this->Viewed->create();
            $data['Viewed']['model'] = $modelo->alias;
            $data['Viewed']['model_id'] = $modelo->id;
            $data['Viewed']['user_id'] = $id_usuario;
        }
        $data['Viewed']['viewed'] = true;
        $data['Viewed']['modified'] = false;
        if( $this->Viewed->save( $data ) ) {
            ClassRegistry::removeObject( 'Viewed.Viewed' );
            return true;
        } else {
            ClassRegistry::removeObject( 'Viewed.Viewed' );
            return false;
        }
    }
}