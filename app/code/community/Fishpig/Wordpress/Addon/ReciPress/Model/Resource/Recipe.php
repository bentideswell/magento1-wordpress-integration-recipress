<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Model_Resource_Recipe extends Fishpig_Wordpress_Model_Resource_Abstract
{
	/**
	 * Fields to be unserialized after load
	 *
	 * @var array
	 */
	protected $_serializableFields = array(
		'ingredients' => array(null, array()),
		'instructions' => array(null, array()),
	);
    
	/**
	 * Initialise the recipe resource model
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_recipress/recipe', 'post_id');
	}
	
	/**
	 * Custom load SQL to combine required tables
	 *
	 * @param string $field
	 * @param string|int $value
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		return parent::_getLoadSelect($field, $value, $object)
			->where('meta_key IN (?)', $this->getFields());
	}

	/**
	 * Load a recipe model
	 * After load, prepare the data
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @param string $value
	 * @param string $field = null
	 * @return $this
	 */
	public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
	{
		if (is_null($field)) {
			$field = $this->getIdFieldName();
		}
	
		$read = $this->_getReadAdapter();
		
		if ($read && !is_null($value)) {
			$select = $this->_getLoadSelect($field, $value, $object);
			$data = $read->fetchAll($select);
	
			if ($data) {
				$object->setData($this->_prepareRecipeData($data))->setId($value);
			}
		}
	
		$this->unserializeFields($object);
		$this->_afterLoad($object);
	
		return $this;
	}
	
	/**
	 * Prepare the data after loading a recipe
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		foreach($this->_serializableFields as $field => $null) {
			if ($object->getData($field)) {
				$items = array();
				
				foreach($object->getData($field) as $data) {
					$item = new Varien_Object($data);
					
					if ($item->getImage()) {
						$image = Mage::getModel('wordpress/image')->load($item->getImage());
						
						if ($image->getId()) {
							$item->setImage($image);
						}
						else {
							$item->setImage(false);
						}
					}
					
					if ($item->getIngredient()) {
						$term = Mage::getModel('wordpress/term')->setTaxonomy('ingredient')
							->load($item->getIngredient(), 'Name');

						if ($term->getId()) {
							$item->setIngredientUrl($term->getUrl());
						}
					}
					
					$items[] = $item;
				}
				
				if (count($items) > 0) {
					$object->setData($field, $items);
				}
			}
		}
		
		return parent::_afterLoad($object);
	}
	
	/**
	 * Prepare recipe data for _afterLoad method
	 *
	 * @param array $data
	 * @return array|false
	 */
	protected function _prepareRecipeData(array $data)
	{
		foreach($data as $it => $item) {
			if (($value = trim($item['meta_value'])) !== '') {
				$data[$item['meta_key']] = $value;
			}
			
			unset($data[$it]);
		}
		
		if (count($data) > 0) {
			foreach(array('ingredient', 'instruction') as $field) {
				if (isset($data[$field])) {
					$data[$field . 's'] = $data[$field];
					unset($data[$field]);
				}
			}

			return $data;
		}
		
		return false;
	}
	
	/**
	 * Retrieve the field names used for a recipe
	 *
	 * @return array
	 */
	public function getFields()
	{
		return array(
			'title',
			'summary',
			'yield',
			'servings',
			'prep_time',
			'cook_time',
			'other_time',
			'ingredient',
			'instruction',
		);
	}
	
	/**
	 * Retrieve the recipe's attached image
	 *
	 * @param Fishpig_Wordpress_Addon_ReciPress_Model_Recipe $recipe
	 * @return false|Fishpig_Wordpress_Model_Image
	 */
	public function getAttachedImage(Fishpig_Wordpress_Addon_ReciPress_Model_Recipe $recipe)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from($this->getTable('wordpress/post'))
			->where('post_parent=?', $recipe->getId())
			->where('post_type=?', 'attachment')
			->limit(1);
		
		if ($imageId = $this->_getReadAdapter()->fetchOne($select)) {
			$image = Mage::getModel('wordpress/image')->load($imageId);

			if ($image->getId()) {
				return $image;
			}
		}
		
		return false;
	}
}
