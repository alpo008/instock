<?php


namespace app\modules\admin\models;

use app\models\Material;
use PHPExcel;
use PHPExcel_Cell_DataType;
use app\custom\FileStorage;

class MaterialExport extends Material
{
    const COLUMNS_TEMPLATE_STORAGE = 'material_export_template';

    /**
     * @return array|null
     */
    public function columns()
    {
        $fileStorage = new FileStorage();
        return $fileStorage->getContent(self::COLUMNS_TEMPLATE_STORAGE);
    }

    /**
     * @param Material[] $materials
     * @return PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function makeExcel ($materials)
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
        foreach ($materials as $material) {
            foreach ($attributes as $attr => $params) {
                if (!empty($material->$attr) && is_scalar($material->$attr)) {
                    $value = !empty($params['getter']) ? $material->{$params['getter']}($attr) : $material->{$attr};
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