<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Helper_Shortcode_Recipe extends Fishpig_Wordpress_Helper_Shortcode_Abstract
{
	/**
	 * Inject the form html 
	 *
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function injectRecipeObserver(Varien_Event_Observer $observer)
	{
		$helper = Mage::helper('wp_addon_recipress');

		if (!$helper->isEnabled()) {
			return $this;
		}

		/*
			if ($observer->getEvent()->getContext() === 'excerpt') {
				return $this;
			}
		*/

		$html = $observer->getEvent()->getContent();
		$content = $html->getContent();

		if ($helper->canAutoAdd()) {
			if (strpos($content, '[recipe') === false) {
				$content .= "\n[recipe]\n\n";
			}
		}
		
		$this->apply($content, $observer->getEvent()->getObject(), $observer->getEvent()->getContext());

		$html->setContent($content);
	}
	
	/**
	 * Retrieve the shortcode tag
	 *
	 * @return string
	 */
	public function getTag()
	{
		return 'recipe';
	}
	
	/**
	 * Apply the Vimeo short code
	 *
	 * @param string &$content
	 * @param Fishpig_Wordpress_Model_Post_Abstract $object
	 * @return void
	 */	
	protected function _apply(&$content, Fishpig_Wordpress_Model_Post $post)
	{
		if (($shortcodes = $this->_getShortcodes($content)) !== false) {
			foreach($shortcodes as $shortcode) {
				$content = str_replace($shortcode->getHtml(), $this->getRecipeHtml($post), $content);
			}
		}
	}
	
	/**
	 * Retrieve the HTML for the recipe associated with the post
	 * Returns empty string if recipe not set
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return string
	 */
	public function getRecipeHtml(Fishpig_Wordpress_Model_Post $post)
	{
		if ($recipe = Mage::helper('wp_addon_recipress')->loadRecipeByPost($post)) {
			return $recipe->getHtml();
		}
		
		return '';
	}
}
