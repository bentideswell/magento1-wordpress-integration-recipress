<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Block_Sidebar_Widget_Terms extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Set the posts collection
	 *
	 */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();

		$this->setRecipeTerms($this->_getRecipeTermsCollection());

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
	protected function _getRecipeTermsCollection()
	{
		$collection = Mage::getResourceModel('wordpress/term_collection')
			->addTaxonomyFilter($this->getTaxonomy());

		$collection->getSelect()->reset('order')
			->order('main_table.name ASC');

		return $collection;
	}
	
	/**
	 * Retrieve the default title
	 *
	 * @return string
	 */
	public function getDefaultTitle()
	{
		return $this->__('Recent Terms');
	}
}
