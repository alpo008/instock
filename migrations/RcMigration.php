<?php
namespace app\migrations;


use Yii;

/**
 * @link http://www.efko.ru/
 * @copyright Copyright (c) АО "Управляющая компания ЭФКО" (18.03.16 14:27)
 * @author веб - программист УИТ, Кулинич Александр a.kulinich@uk.efko.ru
 */

abstract class RcMigration extends \yii\db\Migration {

    const TABLE_USERS = '{{%users}}';
    const TABLE_MATERIALS = '{{%materials}}';
    const TABLE_STOCKS = '{{%stocks}}';
    const TABLE_MATERIALS_STOCKS = '{{%materials_stocks}}';
    const TABLE_STOCK_OPERATIONS = '{{%stock_operations}}';


    /**
     * Получие значения dsn-атрибута
     * @param string $name
     * @param string $dsn
     * @return mixed|null
     */
    public static function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    /**
     * Резервное копирование базы данных
     * с сохранением дампа в runtime
     * @param string $message сообщение
     */
    public static function BackupDB($message = "Выполняем резервное копирование БД INSTOCK\n"){
        echo $message;
        $backupDir = date("Ymd_His");
        $backupsPath = Yii::getAlias('@app/runtime/backups/');
        exec('find ' . $backupsPath . ' -maxdepth 1 -type d -mtime +10 -exec rm -rf {} \;');
        if (mkdir($backupsPath . $backupDir, 0777, true)) {
            $command = 'mysqldump -u'. Yii::$app->db->username.' -p'. Yii::$app->db->password.
                ' '.self::getDsnAttribute('dbname', Yii::$app->db->dsn).' > '.
                Yii::getAlias('@app/runtime/backups/' . $backupDir . '/') .
                'instock_db_backup_' . date("Ymd_His") .".sql\n";
            exec($command);
        }
    }

    /**
     * Проверка существования таблицы
     * @param string $tableName
     * @return bool
     */
    protected function tableExists($tableName)
    {
        return !!Yii::$app->db->schema->getTableSchema($tableName);
    }

    /**
     * Проверка существования столбца в таблице
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function columnExists($tableName, $columnName)
    {
        $existingColumns = Yii::$app->db->schema
            ->getTableSchema($tableName)
            ->columnNames;
        if (is_array($existingColumns)) {
            return in_array($columnName, $existingColumns);
        } else {
            return false;
        }
    }

    /**
     * Проверка существования внешнего ключа в таблице
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function fkExists($tableName, $fkName)
    {
        $existingForeignKeys = Yii::$app->db->schema
            ->getTableSchema($tableName)
            ->foreignKeys;
        if (is_array($existingForeignKeys)) {
            return array_key_exists($fkName, $existingForeignKeys);
        } else {
            return false;
        }
    }
}
