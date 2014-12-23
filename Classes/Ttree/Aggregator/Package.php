<?php
namespace Ttree\Aggregator;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Aggregator".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Package\Package as BasePackage;

/**
 * The Ttree Scheduler Package
 */
class Package extends BasePackage {

	/**
	 * @param Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(Bootstrap $bootstrap) {
		$dispatcher = $bootstrap->getSignalSlotDispatcher();

		$dispatcher->connect('TYPO3\Neos\Service\PublishingService', 'nodePublished', 'Ttree\Aggregator\Service\TaskRegistration', 'registerTask');
		$dispatcher->connect('TYPO3\Neos\Service\PublishingService', 'nodeRemoved', 'Ttree\Aggregator\Service\TaskRegistration', 'removeTask');
	}
}
