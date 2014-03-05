<?php
App::uses('Model', 'Model');
App::uses('ModelBehavior', 'Model');
App::uses('ViewedBehavior', 'Viewed.Behavior' );

/**
 * ViewedTestCase
 * @author Esteban Zeller
 */
class ViewedTest extends CakeTestCase {

    public $fixtures = array(
        'core.article',
        'plugin.viewed.viewed'
    );

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {
            parent::setUp();
            $this->Article = ClassRegistry::init('Article');
            $this->Viewed = ClassRegistry::init('Viewed.Viewed');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown() {
            parent::tearDown();
            unset($this->Article);
    }


    public function testBasic() {
        $this->assertEqual( true, true );
    }

    /**
     * Testea la capacidad de generar y chequear las tablas necesarias en el construct
     * @author Esteban Zeller
     */
    public function testConstruct() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $this->assertContains( 'Viewed', $this->Article->Behaviors->loaded(), "El objeto no tiene propiedad Viewed" );
    }

    /**
     * Testea la capacidad de devolver las propiedades
     * @author Esteban Zeller
     */
    public function testSettings() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $this->assertInternalType( 'array', $this->Article->getViewedSettings(), "Lo devuelto no es un array" );

        $this->assertInternalType( 'boolean', $this->Article->getViewedSettings( 'markUnviewedOnModified' ), "La propiedad no es un booleano" );
        $this->assertEqual( true, $this->Article->getViewedSettings( 'markUnviewedOnModified' ), "La propiedad predeterminada markUnviewedOnModified no es true" );

        $this->assertInternalType( 'boolean', $this->Article->getViewedSettings( 'useModified' ), "La propiedad no es un booleano" );
        $this->assertEqual( true, $this->Article->getViewedSettings( 'useModified' ), "La propiedad predeterminada useModified no es true" );

        $this->assertInternalType( 'array', $this->Article->getViewedSettings( 'fields' ), "La propiedad no es un array" );
        $data = $this->Article->getViewedSettings( 'fields' );
        $this->assertArrayHasKey( 'viewed', $data, "La propiedad de campo visto no existe" );
        $this->assertArrayHasKey( 'modified', $data, "La propiedad de campo modificado no existe" );

        $this->assertEqual( false, $this->Article->getViewedSettings( 'unknown'), "Debería de devolver falso cuando la propeidad no existe" );
    }

    public function testSettingsChanged() {
        $this->Article->Behaviors->load( 'Viewed.Viewed', array(
                  'fields' => array(
                      'viewed' => 'visto'
                  )
              )
        );
        $this->assertInternalType( 'array', $this->Article->getViewedSettings(), "Lo devuelto no es un array" );

        $this->assertInternalType( 'boolean', $this->Article->getViewedSettings( 'markUnviewedOnModified' ), "La propiedad no es un booleano" );
        $this->assertEqual( true, $this->Article->getViewedSettings( 'markUnviewedOnModified' ), "La propiedad predeterminada markUnviewedOnModified no es true" );

        $this->assertInternalType( 'boolean', $this->Article->getViewedSettings( 'useModified' ), "La propiedad no es un booleano" );
        $this->assertEqual( true, $this->Article->getViewedSettings( 'useModified' ), "La propiedad predeterminada useModified no es true" );

        $this->assertInternalType( 'array', $this->Article->getViewedSettings( 'fields' ), "La propiedad no es un array" );
        $data = $this->Article->getViewedSettings( 'fields' );
        $this->assertArrayHasKey( 'viewed', $data, "La propiedad de campo visto no existe" );
        $this->assertEqual( 'visto', $data['viewed'], "No coincide la propiedad nueva" );

        $this->assertArrayHasKey( 'modified', $data, "La propiedad de campo modificado no existe" );

        $this->assertEqual( false, $this->Article->getViewedSettings( 'unknown'), "Debería de devolver falso cuando la propeidad no existe" );

    }

    /**
     * Testea la capacidad de crear el registro asociado cuando se crea un elemento
     * @author
     */
    public function testCreation() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $save_data = array(
            'Article' => array(
                'title' => 'test'
            )
        );
        $this->assertNotEqual( false, $this->Article->save( $save_data ), "Falla el guardar" );
        $id = $this->Article->id;
        $this->assertGreaterThan( 0, $id, "El id es incorrecto!" );

        $data = $this->Viewed->find( 'first', array( 'conditions' => array( 'model' => 'Article', 'model_id' => $id ) ) );
        $this->assertNotEqual( count( $data ), 0, "No se creo ningun registro!" );
        $this->assertArrayHasKey( 'Viewed', $data, "No se encuentran los datos!" );

        $this->assertArrayHasKey( 'model', $data['Viewed'], "No se encuentra el modelo relacionado" );
        $this->assertEqual( $data['Viewed']['model'], 'Article', 'No coincide el nombre del modelo' );

        $this->assertArrayHasKey( 'model_id', $data['Viewed'], "No se encuentra el id del modelo relacionado" );
        $this->assertEqual( $data['Viewed']['model_id'],  $id, 'No coincide el nombre del modelo' );

        $this->assertArrayHasKey( 'viewed', $data['Viewed'], "No se encuentra el campo viewed" );
        $this->assertEqual( $data['Viewed']['viewed'], false, 'No coincide el campo viewed' );

        $this->assertArrayHasKey( 'modified', $data['Viewed'], "No se encuentra el campo modified" );
        $this->assertEqual( $data['Viewed']['modified'], false, 'No coincide el campo modified' );

    }

    /**
     * Testea la capacidad de crear el registro asociado cuando se crea un elemento
     * @author
     */
    public function testEdicionBasica() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $save_data = array(
            'Article' => array(
                'title' => 'test'
            )
        );
        $this->assertNotEqual( false, $this->Article->save( $save_data ), "Falla el guardar" );
        $id = $this->Article->id;
        $this->assertGreaterThan( 0, $id, "El id es incorrecto!" );
        $save_data = $this->Article->read();
        $this->assertNotEqual( count( $save_data ), 0, "No se trajo ningun dato" );

        $save_data['title'] = 'New title';
        $this->assertNotEqual( false, $this->Article->save( $save_data ), "Falla el guardar" );

        $data = $this->Viewed->find( 'first', array( 'conditions' => array( 'model' => $this->Article->alias, 'model_id' => $save_data[$this->Article->alias][$this->Article->primaryKey] ) ) );
        $this->assertNotEqual( count( $data ), 0, "No se creo ningun registro!" );
        $this->assertArrayHasKey( 'Viewed', $data, "No se encuentran los datos!" );

        $this->assertArrayHasKey( 'model', $data['Viewed'], "No se encuentra el modelo relacionado" );
        $this->assertEqual( $data['Viewed']['model'], 'Article', 'No coincide el nombre del modelo' );

        $this->assertArrayHasKey( 'model_id', $data['Viewed'], "No se encuentra el id del modelo relacionado" );
        $this->assertEqual( $data['Viewed']['model_id'],  $id, 'No coincide el nombre del modelo' );

        $this->assertArrayHasKey( 'viewed', $data['Viewed'], "No se encuentra el campo viewed" );
        $this->assertEqual( $data['Viewed']['viewed'], false, 'No coincide el campo viewed' );

        $this->assertArrayHasKey( 'modified', $data['Viewed'], "No se encuentra el campo modified" );
        $this->assertEqual( $data['Viewed']['modified'], true, 'No coincide el campo modified' );
    }

    /**
     * Testea que los datos sean eliminados correctamente
     */
    public function testEliminacion() {
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias]), 0, "No se trajo ningun campo" );

        $this->Article->Behaviors->load('Viewed.Viewed');
        $save_data[$this->Article->alias][$this->Article->primaryKey] = $data[$this->Article->alias][$this->Article->primaryKey];
        $save_data[$this->Article->alias][$this->Article->displayField] = 'New title';
        $this->assertNotEqual( false, $this->Article->save( $save_data ), "Falla el guardar" );


        $this->assertNotEqual( $this->Article->delete( $data[$this->Article->alias][$this->Article->primaryKey] ), false, "No se pudo eliminar el registro" );

        $datos = $this->Viewed->find( 'first', array( 'conditions' => array(
            'model' => $this->Article->alias,
            'model_id' => $data[$this->Article->alias][$this->Article->primaryKey] ) )
        );
        $this->assertEqual( count( $datos ), 0, "No deberian de existir mas datos acerca del modelo eliminado" );

    }

    /**
     * Testea el uso de la funcion directamente
     */
    public function testGetStatus() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $data = $this->Article->find( 'first', array( 'conditions' => array( $this->Article->primaryKey => $this->Article->id ) ) );

        $this->assertArrayHasKey( $this->Article->alias, $data, "Los datos devueltos no tienen el formato recomendado" );
        $this->assertArrayHasKey( 'viewed', $data[$this->Article->alias], "Falta el campo de viewed" );
        $this->assertArrayHasKey( 'modifiedAfterViewed', $data[$this->Article->alias], "Falta el campo de modifiedAfterViewed" );
    }

    /**
     * Funcion que verifica que el mappeo de nombres funcione correctamente
     */
    public function testFieldNameViewed() {
        $this->Article->Behaviors->load( 'Viewed.Viewed', array(
                  'fields' => array(
                      'viewed' => 'visto'
                  )
              )
        );
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $data2 = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertArrayHasKey( 'Article', $data2, "No se encontró el elemento Article" );
        $this->assertArrayHasKey( 'visto', $data2['Article'], "No se encontró el campo 'visto'" );
    }

    /**
     * Funcion que verifica que el mappeo de nombres funcione correctamente
     */
    public function testFieldNameModified() {
        $this->Article->Behaviors->load( 'Viewed.Viewed', array(
                  'fields' => array(
                      'modified' => 'modificado'
                  )
              )
        );
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $data2 = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertArrayHasKey( 'Article', $data2, "No se encontró el elemento Article" );
        $this->assertArrayHasKey( 'modificado', $data2['Article'], "No se encontró el campo 'modificado'" );
    }

    /**
     * Funcion que prueba la funcion isViewed()
     */
    public function testViewedFunction() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $this->assertEqual( $this->Article->isViewed(), false, "La funcion de visto o no visto es incorrecta" );
    }

    /**
     * Funcion para testear el funcionamiento de fue modificado luego de creado
     */
    public function testModifiedAfterViewed() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $this->assertEqual( $this->Article->isModifiedAfterViewed(), true, "La funcion de modificado despues de visto es incorrecta" );
    }


    /**
     * Funcion para setear la funcion como vista
     */
    public function testSetViewed() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $this->assertEqual( $this->Article->setViewed(), true, "La funcion de setear como visto devolvió falta" );

        $this->assertEqual( $this->Article->isViewed(), true, "El valor de visto es incorrecto" );
        $this->assertEqual( $this->Article->isModifiedAfterViewed(), true, "El valor de modificado luego de visto es incorrecto" );


    }

    /**
     * Funcion para setear la funcion como vista
     */
    public function testSetModifiedAfterViewed() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data = $this->Article->find( 'first', array( 'fields' => $this->Article->primaryKey ) );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );

        $this->Article->id = $data[$this->Article->alias][$this->Article->primaryKey];
        $data[$this->Article->alias][$this->Article->displayField] = 'test';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $this->assertEqual( $this->Article->setViewed(), true, "La funcion de setear como visto devolvió falta" );

        $this->assertEqual( $this->Article->isViewed(), true, "El valor de visto es incorrecto" );
        $this->assertEqual( $this->Article->isModifiedAfterViewed(), true, "El valor de modificado luego de visto es incorrecto" );

        $data[$this->Article->alias][$this->Article->displayField] = 'test2';
        $this->assertNotEqual( false, $this->Article->save( $data ), "No se pudo guardar los datos" );

        $this->assertEqual( $this->Article->isViewed(), false, "El valor de visto es incorrecto" );
        $this->assertEqual( $this->Article->isModifiedAfterViewed(), true, "El valor de modificado luego de visto es incorrecto" );
    }

    /**
     * Verifica que los valores predeterminados sean los correctos cuando no se creó el registro.
     */
    public function testFindWhitoutCreate() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data = $this->Article->find( 'first' );
        $this->assertNotEqual( count( $data ), 0, "No existen datos!" );
        $this->assertNotEqual( count( $data[$this->Article->alias] ), 0, "No se trajo ningun campo" );
        $this->assertEqual( $data[$this->Article->alias]['viewed'], false, "No se trajo el campo predeterminado" );
        $this->assertEqual( $data[$this->Article->alias]['modifiedAfterViewed'], false, "No se trajo el campo predeterminado para modificado luego de visto" );
    }

    /**
     * Testea la posibilidad de que se llame a un setViewed sin tener el registro original de la creación del registro
     */
    public function testSetWithoutCreate() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $data_article = $this->Article->find( 'first' );
        $data = $this->Viewed->find( 'first', array( 'conditions' => array( 'model' => $this->Article->alias, 'model_id' => $data_article[$this->Article->alias][$this->Article->primaryKey] ) ) );
        $this->assertEqual( count( $data ), 0, "No se creo ningun registro!" );

        $this->Article->id = $data_article[$this->Article->alias][$this->Article->primaryKey];
        $this->assertEqual( $this->Article->setViewed(), true, "No se pudo setear el registro como visto" );

        $data = $this->Viewed->find( 'first', array( 'conditions' => array( 'model' => $this->Article->alias, 'model_id' => $data_article[$this->Article->alias][$this->Article->primaryKey] ) ) );
        $this->assertNotEqual( count( $data ), 0, "No se creo ningun registro!" );
        $this->assertArrayHasKey( 'Viewed', $data, "No se pudo encontrar el registro!" );

        $this->assertArrayHasKey( 'model', $data['Viewed'], "No se encuentra el modelo relacionado" );
        $this->assertEqual( $data['Viewed']['model'], 'Article', 'No coincide el nombre del modelo' );

        $this->assertArrayHasKey( 'model_id', $data['Viewed'], "No se encuentra el id del modelo relacionado" );
        $this->assertEqual( $data['Viewed']['model_id'],  $data_article[$this->Article->alias][$this->Article->primaryKey], 'No coincide el nombre del modelo' );

        $this->assertArrayHasKey( 'viewed', $data['Viewed'], "No se encuentra el campo viewed" );
        $this->assertEqual( $data['Viewed']['viewed'], true, 'No coincide el campo viewed' );

        $this->assertArrayHasKey( 'modified', $data['Viewed'], "No se encuentra el campo modified" );
        $this->assertEqual( $data['Viewed']['modified'], false, 'No coincide el campo modified' );
    }

    /**
     * Testea la posibilidad de que el sistema no tenga el ID seteado en el modelo cuando se llama
     */
    public function testSetWithoutIdSetted() {
        $this->Article->Behaviors->load('Viewed.Viewed');
        $this->assertNotEqual( $this->Article->setViewed(), true, "No debe devolver verdadero si no hay id seteado" );
    }

}
