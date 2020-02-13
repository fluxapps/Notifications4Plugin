<?php

namespace srag\Notifications4Plugin\Parser;

use srag\DIC\DICTrait;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;

/**
 * Class AbstractParser
 *
 * @package srag\Notifications4Plugin\Parser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractParser implements Parser
{

    use DICTrait;
    use Notifications4PluginTrait;


    /**
     * AbstractParser constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getClass() : string
    {
        return static::class;
    }


    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return static::NAME;
    }


    /**
     * @inheritDoc
     */
    public function getDocLink() : string
    {
        return static::DOC_LINK;
    }
}
