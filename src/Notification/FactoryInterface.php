<?php

namespace srag\Notifications4Plugin\Notification;

/**
 * Interface FactoryInterface
 *
 * @package srag\Notifications4Plugin\Notification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface FactoryInterface {

	/**
	 * @return Notification
	 */
	public function newInstance(): Notification;
}
