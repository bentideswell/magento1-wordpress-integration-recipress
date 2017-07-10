<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Block_Sidebar_Widget_Recent extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Set the posts collection
	 *
	 */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();

		$this->setRecipes($this->_getRecipeCollection());

		return $this;
	}
	
	/**
	 * Control the number of recipes displayed
	 *
	 * @param int $count
	 * @return $this
	 */
	public function setRecipeCount($count)
	{
		return $this->setNumber($count);
	}
	
	/**
	 * Adds on cateogry/author ID filters
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getRecipeCollection()
	{
		$collection = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter()
			->setOrderByPostDate()
			->setPageSize($this->getNumber() ? $this->getNumber() : 5)
			->setCurPage(1);
		
		$collection->addMetaFieldToFilter('hasRecipe', 'Yes');

		return $collection;
	}
	
	/**
	 * Retrieve the default title
	 *
	 * @return string
	 */
	public function getDefaultTitle()
	{
		return $this->__('Recent Recipes');
	}
}
