<?php

namespace srag\Notifications4Plugin\UI;

use ilNonEditableValueGUI;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\CustomInputGUIs\TabsInputGUI\MultilangualTabsInputGUI;
use srag\CustomInputGUIs\TabsInputGUI\TabsInputGUI;
use srag\CustomInputGUIs\TextAreaInputGUI\TextAreaInputGUI;
use srag\DIC\Plugin\PluginInterface;
use srag\Notifications4Plugin\Ctrl\CtrlInterface;
use srag\Notifications4Plugin\Notification\Notification;
use srag\Notifications4Plugin\Parser\twigParser;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;

/**
 * Class NotificationFormGUI
 *
 * @package srag\Notifications4Plugin\UI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class NotificationFormGUI extends ObjectPropertyFormGUI
{

    use Notifications4PluginTrait;
    const LANG_MODULE = CtrlInterface::LANG_MODULE_NOTIFICATIONS4PLUGIN;
    /**
     * @var PluginInterface
     */
    protected $plugin;
    /**
     * @var Notification
     */
    protected $object;


    /**
     * NotificationFormGUI constructor
     *
     * @param PluginInterface $plugin
     * @param CtrlInterface   $parent
     * @param Notification    $object
     */
    public function __construct(PluginInterface $plugin, CtrlInterface $parent, Notification $object)
    {
        $this->plugin = $plugin;

        parent::__construct($parent, $object, false);
    }


    /**
     * @inheritdoc
     */
    protected function getValue(/*string*/ $key)/*: void*/
    {
        switch (true) {
            case ($key === "subject"):
            case ($key === "text"):
                return null;

            case (strpos($key, "subject_") === 0):
                $language = substr($key, strlen("subject_"));

                return $this->object->getSubject($language);

            case (strpos($key, "text_") === 0):
                $language = substr($key, strlen("text_"));

                return $this->object->getText($language);

            default:
                return parent::getValue($key);
        }
    }


    /**
     * @inheritdoc
     */
    protected function initAction()/*: void*/
    {
        if (!empty($this->object->getId())) {
            self::dic()->ctrl()->setParameter($this->parent, CtrlInterface::GET_PARAM, $this->object->getId());
        }

        parent::initAction();

        self::dic()->ctrl()->setParameter($this->parent, CtrlInterface::GET_PARAM, null);
    }


    /**
     * @inheritdoc
     */
    protected function initCommands()/*: void*/
    {
        if (!empty($this->object->getId())) {
            $this->addCommandButton(CtrlInterface::CMD_UPDATE_NOTIFICATION, $this->txt("save"));
        } else {
            $this->addCommandButton(CtrlInterface::CMD_CREATE_NOTIFICATION, $this->txt("add"));
        }

        $this->addCommandButton(CtrlInterface::CMD_LIST_NOTIFICATIONS, $this->txt("cancel"));
    }


    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = (!empty($this->object->getId()) ? [
                "id" => [
                    self::PROPERTY_CLASS    => ilNonEditableValueGUI::class,
                    self::PROPERTY_REQUIRED => true
                ]
            ] : []) + [
                "name"            => [
                    self::PROPERTY_CLASS    => (empty($this->object->getId()) ? ilTextInputGUI::class : ilNonEditableValueGUI::class),
                    self::PROPERTY_REQUIRED => true
                ],
                "title"           => [
                    self::PROPERTY_CLASS    => ilTextInputGUI::class,
                    self::PROPERTY_REQUIRED => true
                ],
                "description"     => [
                    self::PROPERTY_CLASS    => ilTextAreaInputGUI::class,
                    self::PROPERTY_REQUIRED => false
                ],
                "defaultlanguage" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => false,
                    self::PROPERTY_OPTIONS  => MultilangualTabsInputGUI::getLanguages(),
                    "setTitle"              => $this->txt("default_language"),
                    "setInfo"               => $this->txt("default_language_info")
                ],
                "parser"          => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => self::parser()->getPossibleParsers(),
                    "setInfo"               => twigParser::NAME . ": " . self::output()->getHTML(self::dic()->ui()->factory()->link()
                            ->standard(twigParser::DOC_LINK, twigParser::DOC_LINK)->withOpenInNewViewport(true))
                ],
                "subject"         => [
                    self::PROPERTY_CLASS    => TabsInputGUI::class,
                    self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                        "subject" => [
                            self::PROPERTY_CLASS => ilTextInputGUI::class,
                            "setTitle"           => ""
                        ]
                    ])
                ],
                "text"            => [
                    self::PROPERTY_CLASS    => TabsInputGUI::class,
                    self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                        "text" => [
                            self::PROPERTY_CLASS => TextAreaInputGUI::class,
                            "setRows"            => 10,
                            "setTitle"           => ""
                        ]
                    ])
                ]
            ];
    }


    /**
     * @inheritdoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt(!empty($this->object->getId()) ? "edit_notification" : "add_notification"));
    }


    /**
     * @inheritdoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch (true) {
            case ($key === "id"):
            case ($key === "subject"):
            case ($key === "text"):
                break;

            case ($key === "name"):
                if (empty($this->object->getId())) {
                    parent::storeValue($key, $value);
                }
                break;

            case (strpos($key, "subject_") === 0):
                $language = substr($key, strlen("subject_"));

                $this->object->setSubject(strval($value), strval($language));
                break;

            case (strpos($key, "text_") === 0):
                $language = substr($key, strlen("text_"));

                $this->object->setText(strval($value), strval($language));

                break;

            default:
                parent::storeValue($key, $value);
        }
    }


    /**
     * @inheritdoc
     */
    public function txt(/*string*/ $key,/*?string*/ $default = null) : string
    {
        if ($default !== null) {
            return $this->plugin->translate($key, self::LANG_MODULE, [], true, "", $default);
        } else {
            return $this->plugin->translate($key, self::LANG_MODULE);
        }
    }
}
