<?php
namespace Ttree\Aggregator\DataProvider;
/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Aggregator".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Assert\Assertion;
use TYPO3\Flow\Annotations as Flow;

/**
 * Service class to parse external Feed (RSS or Atom)
 */
class FeedDataProvider extends AbstractDataProvider {

	/**
	 * Fetch and parse the give URI
	 *
	 * @param string $uri
	 * @param array $options
	 * @return $this
	 */
	public function fetch($uri, array $options = array()) {
		Assertion::url($uri);

		$dom = new \DOMDocument();
		$dom->load($uri);
		
		$feed = $dom->getElementsByTagName("feed");

		if($feed->length != 0) {
			$feed = \Feed::loadAtom($uri);
			$feed = $feed->toArray();
			$this->items = isset($feed['entry']) && is_array($feed['entry']) ? $feed['entry']: [];
			unset($feed['entry']);
		} else {
			$feed = \Feed::loadRss($uri);
			$feed = $feed->toArray();
			$this->items = isset($feed['item']) && is_array($feed['item']) ? $feed['item']: [];
			unset($feed['item']);
		}

		$this->metadata = $feed;

		return $this;
	}


}
