<?php
/**
 * ViewedFixture
 *
 */
class ViewedFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'viewed';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'model' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_spanish_ci', 'charset' => 'utf8'),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'viewed' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'modified_after' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_spanish_ci', 'engine' => 'InnoDB')
	);

}
