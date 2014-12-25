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

/**
 * Data Provider Interface
 */
interface DataProviderInterface {

	/**
	 * Fetch and parse the give URI
	 *
	 * @param string $uri
	 * @param array $options
	 * @return $this
	 */
	public function fetch($uri, array $options = array());

	/**
	 * Return the metadata of the parsed URI
	 *
	 * @return array
	 */
	public function getMetadata();

	/**
	 * Return all items attached to the parsed URI
	 *
	 * @return array
	 */
	public function getItems();

}