<?php
namespace Ttree\Aggregator\Task;
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
use Ttree\Aggregator\Service\SyndicationService;
use Ttree\Scheduler\Task\TaskInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Service\ContextFactory;

/**
 * Start Aggregator task
 */
class AggregatorTask implements TaskInterface {

	/**
	 * @Flow\Inject
	 * @var ContextFactory
	 */
	protected $contextFactory;

	/**
	 * @Flow\Inject
	 * @var SyndicationService
	 */
	protected $syndicationService;

	/**
	 * @param array
	 * @return void
	 * @todo add error message if the target node does not exist
	 */
	public function execute(array $arguments = array()) {
		Assertion::uuid($arguments['node']);

		$liveContext = $this->contextFactory->create(array(
			'workspaceName' => 'live'
		));
		$node = $liveContext->getNodeByIdentifier($arguments['node']);
		if ($node instanceof NodeInterface) {
			$this->syndicationService->process($node);
		}
	}

}