<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Model_Recipe extends Fishpig_Wordpress_Model_Abstract
{
	/**
	 * Initialise the model
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_recipress/recipe');
	}
	
	/**
	 * Load a recipe using a post model
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return $this
	 */
	public function loadByPost(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		$this->load($post->getId());
		
		if (count($this->getData()) > 0) {
			$this->setPost($post);
		}
		
		return $this;
	}
	
	/**
	 * Retrieve the total time the recipe takes to make
	 *
	 * @return int
	 */
	public function getTotalTime()
	{
		if (!$this->hasTotalTime()) {
			$this->setTotalTime((int)$this->getPrepTime() + (int)$this->getCookTime() + (int)$this->getOtherTime());
		}
		
		return $this->_getData('total_time');
	}
	
	/**
	 * Determine whether the recipe has meta information
	 *
	 * @return bool
	 */
	public function hasMeta()
	{
		return $this->getCuisine() || $this->getCourse() || $this->getSkillLevel();
	}
	
	/**
	 * Retrieve the cuisine term model
	 *
	 * @return Fishpig_Wordpress_Model_Term|false
	 */
	public function getCuisine()
	{
		if (!$this->hasCuisine()) {
			$this->setCuisine($this->_loadTerm('cuisine'));
		}
		
		return $this->_getData('cuisine');
	}
	
	/**
	 * Retrieve the skill level term model
	 *
	 * @return Fishpig_Wordpress_Model_Term|false
	 */
	public function getSkillLevel()
	{
		if (!$this->hasSkillLevel()) {
			$this->setSkillLevel($this->_loadTerm('skill_level'));
		}
		
		return $this->_getData('skill_level');
	}
	
	/**
	 * Retrieve the course term model
	 *
	 * @return Fishpig_Wordpress_Model_Term|false
	 */
	public function getCourse()
	{
		if (!$this->hasCourse()) {
			$this->setCourse($this->_loadTerm('course'));
		}
		
		return $this->_getData('course');
	}
	
	/**
	 * Load a term based on the taxonomy
	 *
	 * @return Fishpig_Wordpress_Model_Term|false
	 */
	protected function _loadTerm($taxonomy)
	{
		$terms = Mage::getResourceModel('wordpress/term_collection')
			->addTaxonomyFilter($taxonomy)
			->addPostIdFilter($this->getPost()->getId())
			->setCurPage(1)
			->setPageSize(1)
			->load();
			
		if (count($terms) > 0) {
			return $terms->getFirstItem();
		}
		
		return false;	
	}	
	
	/**
	 * Retrieve the recipe image URL
	 *
	 * @return string|false
	 */
	public function getImageUrl()
	{
		if ($this->getImage()) {
			return $this->getImage()->getThumbnailImage();
		}
		
		return false;
	}
	
	/**
	 * Retrieve the recipe image object
	 *
	 * @return Fishpig_Wordpress_Model_Image|false
	 */
	public function getImage()
	{
		if (!$this->hasImage()) {
			if (Mage::helper('wp_addon_recipress')->useCustomImage()) {
				$this->setImage($this->getResource()->getAttachedImage($this));
			}
			else {
				$this->setImage($this->getPost()->getFeaturedImage());
			}
		}
		
		return $this->_getData('image');
	}
	
	public function getHtml()
	{
		if (!$this->hasHtml()) {
			$html = Mage::getSingleton('core/layout')->createBlock('wp_addon_recipress/recipe')
				->setTemplate('wordpress-addons/recipress/recipe.phtml')
				->setRecipe($this)
				->toHtml();

			$this->setHtml($html);
		}
		
		return $this->_getData('html');
	}
}
