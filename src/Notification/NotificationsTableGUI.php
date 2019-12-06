<?php

namespace srag\Notifications4Plugin\Notification;

use srag\CustomInputGUIs\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\TableGUI\TableGUI;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;

/**
 * Class NotificationsTableGUI
 *
 * @package srag\Notifications4Plugin\Notification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class NotificationsTableGUI extends TableGUI
{

    use Notifications4PluginTrait;
    const LANG_MODULE = AbstractNotificationsCtrl::LANG_MODULE;


    /**
     * NotificationsTableGUI constructor
     *
     * @param AbstractNotificationsCtrl $parent
     * @param string                    $parent_cmd
     */
    public function __construct(AbstractNotificationsCtrl $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritdoc
     *
     * @param NotificationInterface $notification
     */
    protected function getColumnValue(/*string*/ $column, /*NotificationInterface*/ $notification, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            default:
                $value = Items::getter($notification, $column);
                break;
        }

        return strval($value);
    }


    /**
     * @inheritdoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "title"       => [
                "id"      => "title",
                "default" => true,
                "sort"    => true
            ],
            "description" => [
                "id"      => "description",
                "default" => true,
                "sort"    => true
            ],
            "name"        => [
                "id"      => "name",
                "default" => true,
                "sort"    => true
            ]
        ];

        return $columns;
    }


    /**
     * @inheritdoc
     */
    protected function initColumns()/*: void*/
    {
        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritdoc
     */
    protected function initCommands()/*: void*/
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_notification"), self::dic()->ctrl()
            ->getLinkTargetByClass($this->parent_obj->getNotificationCtrlClass(), AbstractNotificationCtrl::CMD_ADD_NOTIFICATION)));
    }


    /**
     * @inheritdoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");

        // Fix stupid ilTable2GUI !!! ...
        $this->determineLimit();
        $this->determineOffsetAndOrder();

        $this->setData(self::notifications4plugin()->notifications()->getNotifications($this->getOrderField(), $this->getOrderDirection(), intval($this->getOffset()), intval($this->getLimit())));

        $this->setMaxCount(self::notifications4plugin()->notifications()->getNotificationsCount());
    }


    /**
     * @inheritdoc
     */
    protected function initFilterFields()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {
        $this->setId("notifications4plugin_" . self::notifications4plugin()->getPlugin()->getPluginObject()->getId());
    }


    /**
     * @inheritdoc
     */
    protected function initTitle()/*: void*/
    {

    }


    /**
     * @inheritdoc
     *
     * @param NotificationInterface $notification
     */
    protected function fillRow(/*NotificationInterface*/ $notification)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass($this->parent_obj->getNotificationCtrlClass(), AbstractNotificationCtrl::GET_PARAM_NOTIFICATION_ID, $notification->getId());

        parent::fillRow($notification);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->button()->shy($this->txt("edit"), self::dic()->ctrl()
                ->getLinkTargetByClass($this->parent_obj->getNotificationCtrlClass(), AbstractNotificationCtrl::CMD_EDIT_NOTIFICATION)),
            self::dic()->ui()->factory()->button()->shy($this->txt("duplicate"), self::dic()->ctrl()
                ->getLinkTargetByClass($this->parent_obj->getNotificationCtrlClass(), AbstractNotificationCtrl::CMD_DUPLICATE_NOTIFICATION)),
            self::dic()->ui()->factory()->button()->shy($this->txt("delete"), self::dic()->ctrl()
                ->getLinkTargetByClass($this->parent_obj->getNotificationCtrlClass(), AbstractNotificationCtrl::CMD_DELETE_NOTIFICATION_CONFIRM))
        ])->withLabel($this->txt("actions"))));
    }


    /**
     * @inheritdoc
     */
    public function txt(/*string*/ $key,/*?string*/ $default = null) : string
    {
        if ($default !== null) {
            return self::notifications4plugin()->getPlugin()->translate($key, self::LANG_MODULE, [], true, "", $default);
        } else {
            return self::notifications4plugin()->getPlugin()->translate($key, self::LANG_MODULE);
        }
    }
}
