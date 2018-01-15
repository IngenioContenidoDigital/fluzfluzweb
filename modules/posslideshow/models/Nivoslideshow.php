<?php
class Nivoslideshow extends ObjectModel
{
 	/** @var string Name */
	public $title;
        public $description;
        public $image;
        public $link;
        public $porder;
	public $active;
        public $type_route;
        public $type_view;
        public $link_app;

        /**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'pos_slideshow',
		'primary' => 'id_pos_slideshow',
		'multilang' => TRUE,
		'fields' => array(
		'porder' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
                'active' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
	        'image' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false, 'size' => 3999999999),
		//lang field
                'type_route' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                'type_view' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                'title' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 265),
                'link' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 265),
                'link_app' => array('type' => self::TYPE_STRING,'validate' => 'isGenericName'),
                'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'size' => 3999999999999),
                ),
	);
}