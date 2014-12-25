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

use TYPO3\Flow\Annotations as Flow;

/**
 * Service class to parse external Feed (RSS or Atom)
 *
 * @Flow\Scope("singleton")
 */
abstract class AbstractDataProvider implements DataProviderInterface, \Iterator {

	/**
	 * @var array
	 */
	protected $metadata = array();

	/**
	 * @var array
	 */
	protected $items = array();

	/**
	 * @var integer
	 */
	protected $position = 0;

	/**
	 * {@inheritdoc}
	 */
	public function getMetadata() {
		return $this->getMetadata();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getItems() {
		return $this->getItems();
	}

	/**
	 * Rewind
	 */
	public function rewind() {
		$this->position = 0;
	}

	/**
	 * Get current item
	 *
	 * @return array
	 */
	public function current() {
		return $this->items[$this->position];
	}

	/**
	 * Get current position
	 *
	 * @return integer
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * Move to next position
	 */
	public function next() {
		++$this->position;
	}

	/**
	 * Check if position exist
	 *
	 * @return boolean
	 */
	public function valid() {
		return isset($this->items[$this->position]);
	}

}