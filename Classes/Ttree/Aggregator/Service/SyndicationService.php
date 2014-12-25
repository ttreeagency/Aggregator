<?php
namespace Ttree\Aggregator\Service;
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
use Cocur\Slugify\Slugify;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Service\NodeTypeManager;

/**
 * Service class to parse external Feed (RSS or Atom)
 *
 * @Flow\Scope("singleton")
 */
class SyndicationService implements SyndicationServiceInterface {

	/**
	 * @Flow\Inject
	 * @var NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * @param NodeInterface $node
	 */
	public function process(NodeInterface $node) {
		$nodeData = $node->getNodeData();
		if (!$nodeData->getNodeType()->isOfType('Ttree.Aggregator:Feed')) {
			throw new \InvalidArgumentException('Node must be of type "Ttree.Aggregator:Feed"', 1419338872);
		}
		$feedUri = $nodeData->getProperty('feedUri');
		if (trim($feedUri) === '') {
			return;
		}
		Assertion::url($feedUri);
		$slug = new Slugify();
		$feed = $this->parseFeed($feedUri);
		if (!isset($feed['item']) || !is_array($feed['item'])) {
			return;
		}
		foreach ($feed['item'] as $item) {
			$name = $slug->slugify($item['title']);
			$document = $node->getNode($name) ?: $this->createDocument($node, $name, $item);
			$document->setProperty('title', Arrays::getValueByPath($item, 'title'));
			$document->setProperty('description', Arrays::getValueByPath($item, 'description'));
			$document->setProperty('link', Arrays::getValueByPath($item, 'link'));
			$document->setProperty('uriPathSegment', $name);
			$timestamp = Arrays::getValueByPath($item, 'timestamp');
			if ($timestamp) {
				$publicationDate = new \DateTime('@' . $timestamp);
				$publicationDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
				$document->setProperty('publicationDate', $publicationDate);
			}
		}
	}

	/**
	 * @param NodeInterface $parentNode
	 * @param string $name
	 * @param array $item
	 * @return NodeInterface
	 */
	protected function createDocument(NodeInterface $parentNode, $name, array $item) {
		$nodeType = $this->nodeTypeManager->getNodeType('Ttree.Aggregator:SyndicatedDocument');
		return $parentNode->createNode($name, $nodeType);
	}

	/**
	 * @param string $uri
	 * @return array
	 */
	protected function parseFeed($uri) {
		$dom = new \DOMDocument();
		$dom->load($uri);
		$feed = $dom->getElementsByTagName("feed");
		if($feed->length != 0) {
			$feed = \Feed::loadAtom($uri);
		} else {
			$feed = \Feed::loadRss($uri);
		}

		return $feed->toArray();
	}

}