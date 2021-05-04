<?php

use yii\db\Migration;
use app\custom\FileStorage;
use app\modules\admin\models\StockOperationExport;

/**
 * Class m210504_143628_seed_stock_operation_export_template
 */
class m210504_143628_seed_stock_operation_export_template extends Migration
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
        return $this->fileStorage->setContent(StockOperationExport::COLUMNS_TEMPLATE_STORAGE, $template);
    }

    public function down()
    {
        $this->fileStorage->delete(StockOperationExport::COLUMNS_TEMPLATE_STORAGE);
    }

    /**
     * @return array
     */
    protected function getTemplate () :array
    {
        return [
            'created_at' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'getter' => 'getExcelTimestamp',
                'format' => 'DD.MM.YYYY HH:MM:SS',
                'skip' => false
            ],
            'materialRef' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,
                'getter' => 'getMaterialRef',
                'skip' => false
            ],
            'materialName' => [
                'getter' => 'getMaterialName',
                'skip' => false
            ],
            'stockAlias' => [
                'getter' => 'getStockAlias',
                'skip' => false
            ],
            'operationType' => [
                'getter' => 'getOperationType',
                'skip' => false
            ],
            'qty' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,
                'skip' => false
            ],
            'created_by' => [
                'getter' => 'getCreatorName',
                'skip' => false
            ]
        ];
    }
}
