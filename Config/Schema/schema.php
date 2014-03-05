<?php
/***
 * Schema for cakephp-viewed
 * 
 * Schema para cakephp-viewed
 * 
 * @author Esteban Zeller 
 */
class ViewedSchema extends CakeSchema {

    public $viewed = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
        'model' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_spanish_ci', 'charset' => 'utf8'),
        'model_id' => array('type' => 'integer', 'null' => false, 'default' => null),
        'user_id' => array( 'type' => 'integer', 'null' => false, 'default' => null),
        'viewed' => array('type' => 'boolean', 'null' => false, 'default' => null),
        'modified' => array('type' => 'boolean', 'null' => false, 'default' => null),        
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1 )
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_spanish_ci', 'engine' => 'InnoDB')
    );

}

