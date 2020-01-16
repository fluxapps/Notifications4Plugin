<?php

namespace srag\Notifications4Plugin\Notification;

use ilDateTime;
use ilDBConstants;
use srag\CustomInputGUIs\TabsInputGUI\MultilangualTabsInputGUI;
use srag\DIC\DICTrait;
use srag\Notifications4Plugin\Notification\Language\NotificationLanguage;
use srag\Notifications4Plugin\Parser\twigParser;
use srag\Notifications4Plugin\Utils\Notifications4PluginTrait;
use stdClass;
use Throwable;

/**
 * Class Repository
 *
 * @package srag\Notifications4Plugin\Notification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository implements RepositoryInterface
{

    use DICTrait;
    use Notifications4PluginTrait;
    /**
     * @var RepositoryInterface|null
     */
    protected static $instance = null;


    /**
     * @return RepositoryInterface
     */
    public static function getInstance() : RepositoryInterface
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function deleteNotification(NotificationInterface $notification)/*: void*/
    {
        self::dic()->database()->manipulateF('DELETE FROM ' . self::dic()->database()->quoteIdentifier(Notification::getTableName())
            . ' WHERE id=%s', [ilDBConstants::T_INTEGER], [$notification->getId()]);
    }


    /**
     * @inheritDoc
     */
    public function dropTables()/*: void*/
    {
        self::dic()->database()->dropTable(Notification::getTableName(), false);

        self::dic()->database()->dropAutoIncrementTable(Notification::getTableName());

        $this->dropTablesLanguage();
    }


    /**
     * @deprecated
     */
    protected function dropTablesLanguage()/*: void*/
    {
        if (self::dic()->database()->sequenceExists(NotificationLanguage::getTableName() . "g")) {
            self::dic()->database()->dropSequence(NotificationLanguage::getTableName() . "g");
        }
        self::dic()->database()->dropTable(NotificationLanguage::getTableName() . "g", false);
        self::dic()->database()->dropAutoIncrementTable(NotificationLanguage::getTableName() . "g");

        if (self::dic()->database()->sequenceExists(NotificationLanguage::getTableName())) {
            self::dic()->database()->dropSequence(NotificationLanguage::getTableName());
        }
        self::dic()->database()->dropTable(NotificationLanguage::getTableName(), false);
        self::dic()->database()->dropAutoIncrementTable(NotificationLanguage::getTableName());
    }


    /**
     * @inheritDoc
     */
    public function duplicateNotification(NotificationInterface $notification) : NotificationInterface
    {
        $duplicated_notification = clone $notification;

        $duplicated_notification->setId(0);

        $duplicated_notification->setTitle($duplicated_notification->getTitle() . " ("
            . self::notifications4plugin()->getPlugin()->translate("duplicated", NotificationsCtrl::LANG_MODULE) . ")");

        return $duplicated_notification;
    }


    /**
     * @inheritDoc
     */
    public function factory() : FactoryInterface
    {
        return Factory::getInstance();
    }


    /**
     * @param int    $notification_id
     * @param string $language
     *
     * @return stdClass|null
     *
     * @deprecated
     */
    protected function getLanguageForNotification(int $notification_id, string $language) /* : ?stdClass*/
    {
        /**
         * @var stdClass|null $l
         */
        $l = self::dic()->database()->fetchObjectClass(self::dic()->database()->queryF('SELECT * FROM ' . self::dic()->database()
                ->quoteIdentifier(NotificationLanguage::getTableName()) . ' WHERE notification_id=%s AND language=%s', [
            ilDBConstants::T_INTEGER,
            ilDBConstants::T_TEXT
        ], [$notification_id, $language]), stdClass::class);

        return $l;
    }


    /**
     * @inheritDoc
     */
    public function getNotificationById(int $id)/*: ?NotificationInterface*/
    {
        /**
         * @var NotificationInterface|null $notification
         */
        $notification = self::dic()->database()->fetchObjectCallback(self::dic()->database()->queryF('SELECT * FROM ' . self::dic()->database()
                ->quoteIdentifier(Notification::getTableName()) . ' WHERE id=%s', [ilDBConstants::T_INTEGER], [$id]), [
            $this->factory(),
            "fromDB"
        ]);

        return $notification;
    }


    /**
     * @inheritDoc
     */
    public function getNotificationByName(string $name)/*: ?NotificationInterface*/
    {
        /**
         * @var NotificationInterface|null $notification
         */
        $notification = self::dic()->database()->fetchObjectCallback(self::dic()->database()->queryF('SELECT * FROM ' . self::dic()->database()
                ->quoteIdentifier(Notification::getTableName())
            . ' WHERE name=%s', [ilDBConstants::T_TEXT], [$name]), [$this->factory(), "fromDB"]);

        return $notification;
    }


    /**
     * @inheritDoc
     */
    public function getNotifications(string $sort_by = null, string $sort_by_direction = null, int $limit_start = null, int $limit_end = null) : array
    {

        $sql = 'SELECT *';

        $sql .= $this->getNotificationsQuery($sort_by, $sort_by_direction, $limit_start, $limit_end);

        /**
         * @var NotificationInterface[] $notifications
         */
        $notifications = self::dic()->database()->fetchAllCallback(self::dic()->database()->query($sql), [$this->factory(), "fromDB"]);

        return $notifications;
    }


    /**
     * @inheritDoc
     */
    public function getNotificationsCount() : int
    {

        $sql = 'SELECT COUNT(id) AS count';

        $sql .= $this->getNotificationsQuery(null, null, null, null);

        $result = self::dic()->database()->query($sql);

        if (($row = $result->fetchAssoc()) !== false) {
            return intval($row["count"]);
        }

        return 0;
    }


    /**
     * @param string|null $sort_by
     * @param string|null $sort_by_direction
     * @param int|null    $limit_start
     * @param int|null    $limit_end
     *
     * @return string
     */
    private function getNotificationsQuery(string $sort_by = null, string $sort_by_direction = null, int $limit_start = null, int $limit_end = null) : string
    {

        $sql = ' FROM ' . self::dic()->database()->quoteIdentifier(Notification::getTableName());

        if ($sort_by !== null && $sort_by_direction !== null) {
            $sql .= ' ORDER BY ' . self::dic()->database()->quoteIdentifier($sort_by) . ' ' . $sort_by_direction;
        }

        if ($limit_start !== null && $limit_end !== null) {
            $sql .= ' LIMIT ' . self::dic()->database()->quote($limit_start, ilDBConstants::T_INTEGER) . ',' . self::dic()->database()
                    ->quote($limit_end, ilDBConstants::T_INTEGER);
        }

        return $sql;
    }


    /**
     * @inheritDoc
     */
    public function installTables()/*:void*/
    {
        try {
            Notification::updateDB();
        } catch (Throwable $ex) {
            // Fix Call to a member function getName() on null (Because not use ILIAS sequence)
        }

        if (self::dic()->database()->sequenceExists(Notification::getTableName())) {
            self::dic()->database()->dropSequence(Notification::getTableName());
        }

        self::dic()->database()->createAutoIncrement(Notification::getTableName(), "id");

        $this->migrateLanguages();

        if (self::dic()->database()->tableColumnExists(Notification::getTableName(), "default_language")) {
            self::dic()->database()->dropTableColumn(Notification::getTableName(), "default_language");
        }
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function migrateFromOldGlobalPlugin(string $name = null)/*: ?NotificationInterface*/
    {
        $global_plugin_notification_table_name = "sr_notification";
        $global_plugin_notification_language_table_name = "sr_notification_lang";
        $global_plugin_twig_parser_class = implode("\\", [
            "srag",
            "Notifications4Plugin",
            "Notifications4Plugins",
            "Parser",
            "twigParser"
        ]); // (Prevents LibraryNamespaceChanger)

        if (!empty($name)) {
            if (self::dic()->database()->tableExists($global_plugin_notification_table_name)
                && self::dic()->database()->tableExists($global_plugin_notification_language_table_name)
            ) {
                $result = self::dic()->database()->queryF('SELECT * FROM ' . self::dic()->database()
                        ->quoteIdentifier($global_plugin_notification_table_name) . ' WHERE name=%s', [ilDBConstants::T_TEXT], [$name]);

                if (($row = $result->fetchAssoc()) !== false) {

                    $notification = $this->getNotificationByName($name);
                    if ($notification !== null) {
                        return $notification;
                    }

                    $notification = $this->factory()->newInstance();

                    $notification->setName($row["name"]);
                    $notification->setTitle($row["title"]);
                    $notification->setDescription($row["description"]);

                    if (empty($row["parser"]) || $row["parser"] === $global_plugin_twig_parser_class) {
                        $notification->setParser(twigParser::class);
                    } else {
                        $notification->setParser($row["parser"]);
                    }

                    $result2 = self::dic()->database()->queryF('SELECT * FROM ' . self::dic()->database()
                            ->quoteIdentifier($global_plugin_notification_language_table_name)
                        . ' WHERE notification_id=%s', [ilDBConstants::T_INTEGER], [$row["id"]]);

                    while (($row2 = $result2->fetchAssoc()) !== false) {
                        $notification->setSubject($row2["subject"], $row2["language"]);
                        $notification->setText($row2["text"], $row2["language"]);
                    }

                    $this->storeNotification($notification);

                    return $notification;
                }
            }
        }

        return null;
    }


    /**
     * @inheritDoc
     */
    protected function migrateLanguages()/*:void*/
    {
        if (self::dic()->database()->tableExists(NotificationLanguage::getTableName() . "g")) {
            self::dic()->database()->renameTable(NotificationLanguage::getTableName() . "g", NotificationLanguage::getTableName());
        }

        if (self::dic()->database()->tableExists(NotificationLanguage::getTableName())) {

            foreach (self::notifications4plugin()->notifications()->getNotifications() as $notification) {

                foreach (array_keys(MultilangualTabsInputGUI::getLanguages()) as $lang_key) {

                    $language = $this->getLanguageForNotification($notification->getId(), $lang_key);

                    if ($language !== null) {
                        $notification->setSubject($language->subject, $lang_key);
                        $notification->setText($language->text, $lang_key);
                    }
                }

                if (!empty($notification->default_language)) {
                    $notification->setSubject($notification->getSubject($notification->default_language, false), "default");
                    $notification->setText($notification->getText($notification->default_language, false), "default");
                }

                self::notifications4plugin()->notifications()->storeNotification($notification);
            }
        }

        $this->dropTablesLanguage();
    }


    /**
     * @inheritDoc
     */
    public function storeNotification(NotificationInterface $notification)/*: void*/
    {
        $date = new ilDateTime(time(), IL_CAL_UNIX);

        if (empty($notification->getId())) {
            $notification->setCreatedAt($date);
        }

        $notification->setUpdatedAt($date);

        $notification->setId(self::dic()->database()->store(Notification::getTableName(), [
            "name"        => [ilDBConstants::T_TEXT, $notification->getName()],
            "title"       => [ilDBConstants::T_TEXT, $notification->getTitle()],
            "description" => [ilDBConstants::T_TEXT, $notification->getDescription()],
            "parser"      => [ilDBConstants::T_TEXT, $notification->getParser()],
            "subject"     => [ilDBConstants::T_TEXT, json_encode($notification->getSubjects())],
            "text"        => [ilDBConstants::T_TEXT, json_encode($notification->getTexts())],
            "created_at"  => [ilDBConstants::T_TEXT, $notification->getCreatedAt()->get(IL_CAL_DATETIME)],
            "updated_at"  => [ilDBConstants::T_TEXT, $notification->getUpdatedAt()->get(IL_CAL_DATETIME)]
        ], "id", $notification->getId()));
    }
}
