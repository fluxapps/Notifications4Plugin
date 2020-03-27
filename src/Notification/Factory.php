<?php

namespace srag\Notifications4Plugin\Notification;

use ilDateTime;
use srag\DIC\DICTrait;
use srag\Notifications4Plugin\Notification\Table\TableBuilder;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;
use stdClass;

/**
 * Class Factory
 *
 * @package srag\Notifications4Plugin\Notification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory implements FactoryInterface
{

    use DICTrait;
    use Notifications4PluginTrait;
    /**
     * @var FactoryInterface|null
     */
    protected static $instance = null;


    /**
     * @return FactoryInterface
     */
    public static function getInstance() : FactoryInterface
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function fromDB(stdClass $data) : NotificationInterface
    {
        $notification = $this->newInstance();

        $notification->setId($data->id);
        $notification->setName($data->name);
        $notification->setTitle($data->title);
        $notification->setDescription($data->description);
        $notification->setParser($data->parser);
        $notification->setParserOptions(json_decode($data->parser_options, true) ?? []);
        $notification->setSubjects(json_decode($data->subject, true) ?? []);
        $notification->setTexts(json_decode($data->text, true) ?? []);
        $notification->setCreatedAt(new ilDateTime($data->created_at, IL_CAL_DATETIME));
        $notification->setUpdatedAt(new ilDateTime($data->updated_at, IL_CAL_DATETIME));

        if (isset($data->default_language)) {
            $notification->default_language = $data->default_language;
        }

        return $notification;
    }


    /**
     * @inheritDoc
     */
    public function newInstance() : NotificationInterface
    {
        $notification = new Notification();

        return $notification;
    }


    /**
     * @inheritDoc
     */
    public function newTableBuilderInstance(NotificationsCtrl $parent) : TableBuilder
    {
        $table = new TableBuilder($parent);

        return $table;
    }


    /**
     * @inheritDoc
     */
    public function newFormInstance(NotificationCtrl $parent, NotificationInterface $notification) : NotificationFormGUI
    {
        $form = new NotificationFormGUI($parent, $notification);

        return $form;
    }
}
