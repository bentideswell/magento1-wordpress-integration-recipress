<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReciPress_Block_Recipe extends Mage_Core_Block_Template
{
	/**
	 * Determine wheter the recipe block can be used
	 *
	 * @return bool
	 */
	public function canDisplay()
	{
		$helper = $this->helper('wp_addon_recipress');

		if ($helper->isEnabled()) {
			$handles = $this->getLayout()->getUpdate()->getHandles();

			if (in_array('wordpress_homepage', $handles)) {
				return $helper->canDisplayOnHomepage();
			}
			else if (in_array('wordpress_post_view', $handles)) {
				return $helper->canDisplayOnPostPage();
			}
			else if (in_array('wordpress_search_index', $handles) || in_array('catalogsearch_result_index', $handles)) {
				return $helper->canDisplayOnSearchPage();
			}
			else if (in_array('wordpress_term', $handles)) {
				return $helper->canDisplayOnTermPage();
			}
		}

		return false;	
	}

	/**
	 * Convert the number of mintues to a relative time
	 *
	 * @param int $minutes
	 * @return string
	 */	
	public function getRelativeTime($minutes)
	{
		if ($minutes < 1) {
			return $this->__('%d seconds', ceil(60 / 1));
		}
		
		if ($minutes < 60) {
			return $this->__('%s minutes', $minutes);
		}
		
		$hLabel = $minutes < 120 ? 'hr' : 'hrs';
		
		return $this->__('%s %s %s minutes', floor($minutes / 60), $hLabel, $minutes % 60);
	}
	
	/**
	 * Convert the numberof minutes to a relative time
	 * This is used for the span title (rich snippets)
	 *
	 * @param int $minutes
	 * @return string
	 */
	public function getRelativeTimeTitle($minutes)
	{
		$relative = strtolower($this->getRelativeTime($minutes));
		
		if (strpos($relative, 'seconds') !== false) {
			return '0H1M';
		}
		
		return str_replace(array('hrs', 'hr', 'minutes', ' '), array('H', 'H', 'M', ''), $relative);
	}
}
