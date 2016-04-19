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
use Ttree\Aggregator\DataProvider\FeedDataProvider;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
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
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $logger;

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
		$dataProvider = new FeedDataProvider();
		$dataProvider->fetch($feedUri);
		foreach ($dataProvider as $item) {
			$title = strip_tags(trim(Arrays::getValueByPath($item, 'title')));
			if ($title === '') {
				continue;
			}
			$name = $slug->slugify($title);
			$document = $node->getNode($name) ?: $this->createDocument($node, $name, $item);
			$this->logger->log(sprintf('Import external article "%s"', $title));
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
	 * @return NodeInterface
	 */
	protected function createDocument(NodeInterface $parentNode, $name) {
		$nodeType = $this->nodeTypeManager->getNodeType('Ttree.Aggregator:SyndicatedDocument');
		return $parentNode->createNode($name, $nodeType);
	}

}
