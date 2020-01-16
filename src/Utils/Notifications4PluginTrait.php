<?php

namespace srag\Notifications4Plugin\Utils;

use srag\Notifications4Plugin\Repository as Notifications4PluginRepository;
use srag\Notifications4Plugin\RepositoryInterface as Notifications4PluginRepositoryInterface;

/**
 * Trait Notifications4PluginTrait
 *
 * @package srag\Notifications4Plugin\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait Notifications4PluginTrait
{

    /**
     * @return Notifications4PluginRepositoryInterface
     */
    protected static function notifications4plugin() : Notifications4PluginRepositoryInterface
    {
        return Notifications4PluginRepository::getInstance();
    }
}
