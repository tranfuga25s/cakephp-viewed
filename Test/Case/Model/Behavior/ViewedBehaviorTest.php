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

}
