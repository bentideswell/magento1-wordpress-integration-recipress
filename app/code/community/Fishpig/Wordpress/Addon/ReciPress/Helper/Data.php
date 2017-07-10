<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Helper_Data extends Fishpig_Wordpress_Helper_Plugin_Abstract
{
	/**
	 * Options prefix. Used to quickly load the plugin options
	 *
	 * @var string
	 */
	protected $_optionsFieldPrefix = 'recipress';
	
	protected $_recipeObjectCache = array();
	
	/**
	 * Determine whether the extension and plugin are enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('recipress/recipress.php');
	}
	
	/**
	 * Determine whether to auto append the recipe to the bottom of the post
	 * If false, recipe included on shortcode
	 *
	 * @return bool
	 */
	public function canAutoAdd()
	{
		return $this->getAutoadd() === 'yes';
	}
	
	/**
	 * Determine whether the recipe can be displayed on the homepage
	 *
	 * @return bool
	 */
	public function canDisplayOnHomepage()
	{
		return $this->isEnabled() && in_array('home', $this->getOutputScope());
	}

	/**
	 * Determine whether the recipe can be displayed on the post page
	 *
	 * @return bool
	 */	
	public function canDisplayOnPostPage()
	{
		return $this->isEnabled() && in_array('single', $this->getOutputScope());
	}
	
	/**
	 * Determine whether the recipe can be displayed on the term page
	 * This includes, terms, categories, tags and archives
	 *
	 * @return bool
	 */
	public function canDisplayOnTermPage()
	{
		return $this->isEnabled() && in_array('archive', $this->getOutputScope());
	}
	
	/**
	 * Determine whether the recipe can be displayed on the search page
	 *
	 * @return bool
	 */
	public function canDisplayOnSearchPage()
	{
		return $this->isEnabled() && in_array('search', $this->getOutputScope());
	}
	
	/**
	 * Retrieve an array of area's to display the recipe
	 *
	 * @return array
	 */
	public function getOutputScope()
	{
		if (!is_array($this->getOutput())) {
			$this->setOutput(array());
		}
		
		return $this->_getData('output');
	}
	
	/**
	 * Determine whether to use a custom image
	 *
	 * @return bool
	 */
	public function useCustomImage()
	{
		return $this->getUsePhoto() === 'no';
	}
	
	/**
	 * Load a recipe by a post object
	 *
	 * @param Varien_Object $post
	 * @return Fishpig_Wordpress_Addon_ReciPress_Model_Recipe
	 */
	public function loadRecipeByPost(Varien_Object $post)
	{
		if (isset($this->_recipeObjectCache[$post->getId()])) {
			return $this->_recipeObjectCache[$post->getId()];
		}
		
		$recipe = Mage::getModel('wp_addon_recipress/recipe')->loadByPost($post);

		$this->_recipeObjectCache[$post->getId()] = $recipe->getId()
			? $recipe
			: false;
		
		return $this->_recipeObjectCache[$post->getId()];
	}
}
