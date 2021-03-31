<?php

use app\custom\FileStorage;
use app\modules\admin\models\MaterialExport;
use yii\db\Migration;

/**
 * Class m210327_080712_seed_material_export_template
 *
 * @property FileStorage $fileStorage
 */
class m210327_080712_seed_material_export_template extends Migration
{
    public $fileStorage;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->fileStorage = new FileStorage();
    }

    /**
     * @return bool|void|null
     */
    public function up()
    {
        $template = $this->getTemplate();
        return $this->fileStorage->setContent(MaterialExport::COLUMNS_TEMPLATE_STORAGE, $template);
    }

    public function down()
    {
        $this->fileStorage->delete(MaterialExport::COLUMNS_TEMPLATE_STORAGE);
    }

    /**
     * @return array
     */
    protected function getTemplate ()
    {
        return [
            'ref' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER
            ],
            'name' => [
                'type' => PHPExcel_Cell_DataType::TYPE_STRING
            ],
            'quantity' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00
            ],
            'min_qty' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00
            ],
            'max_qty' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00
            ],
            'unit' => [
                'type' => PHPExcel_Cell_DataType::TYPE_STRING,
                'getter' => 'getUnitName'
            ],
            'type' => [
                'type' => PHPExcel_Cell_DataType::TYPE_STRING,
            ],
            'group' => [
                'type' => PHPExcel_Cell_DataType::TYPE_STRING,
            ],
            'created_at' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'getter' => 'getExcelTimestamp',
                'format' => 'DD.MM.YYYY HH:MM:SS'
            ],
            'created_by' => [
                'getter' => 'getCreatorName'
            ],
            'updated_at' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'getter' => 'getExcelTimestamp',
                'format' => 'DD.MM.YYYY HH:MM:SS'
            ],
            'updated_by' => [
                'getter' => 'getEditorName'
            ]
        ];
    }
}
