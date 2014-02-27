<?php
App::uses('Model', 'Model');
App::uses('ModelBehavior', 'Model');
App::uses('ViewedBehavior', 'Viewed.Behavior' );

class Viewed extends CakeTestModel {
    public $name = 'Viewed';
    public $useTable = 'viewed';
}


/**
 * ViewedTestCase
 * @author Esteban Zeller
 */
class ViewedTest extends CakeTestCase {
  
    public $fixture = array(
        'core.Article'
    );

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {
            parent::setUp();
            $this->Article = ClassRegistry::init('Article');
            $this->Viewed = ClassRegistry::init('Viewed');
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
        $data = array(
            'Article' => array(
                'title' => 'test'
            )            
        );
        $this->assertNotEqual( false, $this->Article->save( $data ), "Falla el guardar" );
        $id = $this->Article->id;
        $this->assertGreaterThan( 0, $id, "El id es incorrecto?" );
        
        $data = $this->Viewed->find( 'first', array( 'conditions' => array( 'model' => 'Article', 'model_id' => $id ) ) );
        $this->assertNotEqual( count( $data ), 0, "NO se creo ningun registro!" );
        
        $this->assertArrayHasKey( 'model', $data, "No se encuentra el modelo relacionado" );
        $this->assertEqual( $data['model'], 'Article', 'No coincide el nombre del modelo' );
        
        $this->assertArrayHasKey( 'model_id', $data, "No se encuentra el id del modelo relacionado" );
        $this->assertEqual( $data['model_id'],  $id, 'No coincide el nombre del modelo' );
        
        $this->assertArrayHasKey( 'viewed', $data, "No se encuentra el campo viewed" );
        $this->assertEqual( $data['viewed'], false, 'No coincide el campo viewed' );
        
        $this->assertArrayHasKey( 'modified', $data, "No se encuentra el campo modified" );
        $this->assertEqual( $data['modified'], false, 'No coincide el campo modified' );
        
    }

}
