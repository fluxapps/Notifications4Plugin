<?php

namespace srag\Notifications4Plugin\Parser;

use srag\Notifications4Plugin\Exception\Notifications4PluginException;
use srag\Notifications4Plugin\Notification\Notification;

/**
 * Interface RepositoryInterface
 *
 * @package srag\Notifications4Plugin\Parser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface RepositoryInterface {

	/**
	 * @param Parser $parser
	 */
	public function addParser(Parser $parser);


	/**
	 * @return FactoryInterface
	 */
	public function factory(): FactoryInterface;


	/**
	 * @return Parser[]
	 */
	public function getPossibleParsers(): array;


	/**
	 * @param string $parser_class
	 *
	 * @return Parser
	 *
	 * @throws Notifications4PluginException
	 */
	public function getParserByClass(string $parser_class): Parser;


	/**
	 * @param Notification $notification
	 *
	 * @return Parser
	 *
	 * @throws Notifications4PluginException
	 */
	public function getParserForNotification(Notification $notification): Parser;


	/**
	 * @param Parser       $parser
	 * @param Notification $notification
	 * @param array        $placeholders
	 * @param string       $language
	 *
	 * @return string
	 *
	 * @throws Notifications4PluginException
	 */
	public function parseSubject(Parser $parser, Notification $notification, array $placeholders = array(), string $language = ""): string;


	/**
	 * @param Parser       $parser
	 * @param Notification $notification
	 * @param array        $placeholders
	 * @param string       $language
	 *
	 * @return string
	 *
	 * @throws Notifications4PluginException
	 */
	public function parseText(Parser $parser, Notification $notification, array $placeholders = array(), string $language = ""): string;
}
