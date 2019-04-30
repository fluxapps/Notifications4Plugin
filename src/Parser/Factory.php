<?php

namespace srag\Notifications4Plugin\Parser;

use srag\DIC\DICTrait;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;

/**
 * Class Factory
 *
 * @package srag\Notifications4Plugin\Parser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory implements FactoryInterface {

	use DICTrait;
	use Notifications4PluginTrait;
	/**
	 * @var FactoryInterface
	 */
	protected static $instance = null;


	/**
	 * @return FactoryInterface
	 */
	public static function getInstance(): FactoryInterface {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Factory constructor
	 */
	private function __construct() {

	}


	/**
	 * @inheritdoc
	 */
	public function twig(): twigParser {
		return new twigParser();
	}
}
