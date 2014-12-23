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

use Ttree\Scheduler\Domain\Model\Task;
use Ttree\Scheduler\Domain\Repository\TaskRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeData;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Listen to Node publication and deletion to register task in the scheduler
 */
class TaskRegistration {

	/**
	 * @Flow\Inject
	 * @var TaskRepository
	 */
	protected $taskRepository;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\InjectSettings(path="defaultExpression")
	 * @var string
	 */
	protected $defaultExpression;

	/**
	 * @param NodeInterface $node
	 */
	public function registerTask(NodeInterface $node) {
		$nodeData = $node->getNodeData();
		if (!$nodeData->getNodeType()->isOfType('Ttree.Aggregator:Feed')) {
			return;
		}
		$task = $this->taskRepository->findOneByImplementationAndArguments('Ttree\Aggregator\Task\AggregatorTask', array(
			'node' => $nodeData->getIdentifier()
		));

		if (!$task instanceof Task) {
			$task = $this->addTask($nodeData);
		}

		$task->setExpression($nodeData->getProperty('aggregationFrequency') ?: $this->defaultExpression);
		if ($nodeData->getProperty('aggregationStatus')) {
			$task->enable();
		} else {
			$task->disable();
		}

		if (!$this->persistenceManager->isNewObject($task)) {
			$this->taskRepository->update($task);
		}
	}

	/**
	 * @param NodeData $nodeData
	 * @throws \TYPO3\TYPO3CR\Exception\NodeException
	 * @return Task
	 */
	protected function addTask(NodeData $nodeData) {
		$task = new Task($nodeData->getProperty('aggregationFrequency') ?: $this->defaultExpression, 'Ttree\Aggregator\Task\AggregatorTask', array(
			'node' => $nodeData->getIdentifier()
		));
		$this->taskRepository->add($task);
		return $task;
	}

	/**
	 * @param NodeInterface $node
	 */
	public function removeTask(NodeInterface $node) {
		$nodeData = $node->getNodeData();
		if (!$nodeData->getNodeType()->isOfType('Ttree.Aggregator:Feed')) {
			return;
		}
		$task = $this->taskRepository->findOneByImplementationAndArguments('Ttree\Aggregator\Task\AggregatorTask', array(
			'node' => $nodeData->getIdentifier()
		));
		if ($task !== NULL) {
			$this->taskRepository->remove($task);
		}
	}

}