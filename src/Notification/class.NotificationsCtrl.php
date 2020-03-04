<?php

namespace srag\Notifications4Plugin\Notification;

use srag\DIC\DICTrait;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;

/**
 * Class NotificationsCtrl
 *
 * @package srag\Notifications4Plugin\Notification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NotificationsCtrl
{

    use DICTrait;
    use Notifications4PluginTrait;
    const CMD_LIST_NOTIFICATIONS = "listNotifications";
    const LANG_MODULE = "notifications4plugin";
    const TAB_NOTIFICATIONS = "notifications";


    /**
     * NotificationsCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(NotificationCtrl::class):
                self::dic()->ctrl()->forwardCommand(new NotificationCtrl($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_LIST_NOTIFICATIONS:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }


    /**
     *
     */
    protected function listNotifications()/*: void*/
    {
        $table = self::notifications4plugin()->notifications()->factory()->newTableInstance($this);

        self::output()->output($table);
    }
}
