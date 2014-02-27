<?php
App::uses('Viewed', 'Viewed.Model');

/**
 * Viewed Test Case
 *
 */
class ViewedTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.viewed.viewed'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Viewed = ClassRegistry::init('Viewed.Viewed');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Viewed);

		parent::tearDown();
	}

}
