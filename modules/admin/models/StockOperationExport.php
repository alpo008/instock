<?php


namespace app\modules\admin\models;

use app\models\StockOperation;
use PHPExcel;
use PHPExcel_Cell_DataType;
use app\custom\FileStorage;

class StockOperationExport extends StockOperation
{
    const COLUMNS_TEMPLATE_STORAGE = 'stock_operation_export_template';

    /**
     * @return array|null
     */
    public function columns()
    {
        $fileStorage = new FileStorage();
        return $fileStorage->getContent(self::COLUMNS_TEMPLATE_STORAGE);
    }

    /**
     * @param StockOperation[] $operations
     * @return PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function makeExcel ($operations)
    {
        $attributes = $this->columns();
        $phpExcel = new PHPExcel();
        $phpExcel->setActiveSheetIndex(0);
        $column = 0;
        $row = 1;
        foreach ($attributes as $attr => $params) {
            $label = !empty($params['label']) ? $params['label'] : $this->getAttributeLabel($attr);
            $phpExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $label);
            $phpExcel->getActiveSheet()->getColumnDimensionByColumn($column)->setAutoSize();
            $column++;
        }

        $column = 0;
        $row = 2;
        foreach ($operations as $operation) {
            foreach ($attributes as $attr => $params) {
                if (!empty($operation->$attr) && is_scalar($operation->$attr)) {
                    $value = !empty($params['getter']) ? $operation->{$params['getter']}($attr) : $operation->{$attr};
                } else {
                    $value = 'not set';
                }
                $type = !empty($params['type']) ? $params['type'] : PHPExcel_Cell_DataType::TYPE_STRING;
                if (!empty($params['format'])) {
                    $phpExcel->getActiveSheet()->getStyleByColumnAndRow($column, $row)
                        ->getNumberFormat()->setFormatCode($params['format']);
                }
                $phpExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(
                    $column, $row, $value, $type
                );
                $column++;
            }
            $row++;
            $column = 0;
        }
        return $phpExcel;
    }
}