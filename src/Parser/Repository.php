<?php

namespace srag\Notifications4Plugin\Parser;

use srag\DIC\DICTrait;
use srag\Notifications4Plugin\Exception\Notifications4PluginsException;
use srag\Notifications4Plugin\Notification\AbstractNotification;
use srag\Notifications4Plugin\Utils\Notifications4PluginsTrait;

/**
 * Class Repository
 *
 * @package srag\Notifications4Plugin\Parser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository {

	use DICTrait;
	use Notifications4PluginsTrait;
	/**
	 * @var self
	 */
	protected static $instance = null;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Repository constructor
	 */
	private function __construct() {

	}


	/**
	 * @return Factory
	 */
	public function factory(): Factory {
		return Factory::getInstance();
	}


	/**
	 * @param AbstractNotification $notification
	 *
	 * @return Parser
	 */
	public function getParserForNotification(AbstractNotification $notification): Parser {
		// Currently only one parser type
		return $this->factory()->twig();
	}


	/**
	 * @param Parser               $parser
	 * @param AbstractNotification $notification
	 * @param array                $placeholders
	 * @param string               $language
	 *
	 * @return string
	 *
	 * @throws Notifications4PluginsException
	 */
	public function parseSubject(Parser $parser, AbstractNotification $notification, array $placeholders = array(), string $language = ""): string {
		return $parser->parse($notification->getSubject($language), $placeholders);
	}


	/**
	 * @param Parser               $parser
	 * @param AbstractNotification $notification
	 * @param array                $placeholders
	 * @param string               $language
	 *
	 * @return string
	 *
	 * @throws Notifications4PluginsException
	 */
	public function parseText(Parser $parser, AbstractNotification $notification, array $placeholders = array(), string $language = ""): string {
		return $parser->parse($notification->getText($language), $placeholders);
	}
}
