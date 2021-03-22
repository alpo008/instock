<?php


namespace app\modules\admin\models;


use app\models\Material;
use app\models\User;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style_NumberFormat;
use Yii;

class MaterialExport extends Material
{
    public function columns()
    {
        return [
            'ref' => [
                'type' => PHPExcel_Cell_DataType::TYPE_NUMERIC,
                'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER
            ],
            'name' => [
                'type' => PHPExcel_Cell_DataType::TYPE_STRING
            ],
            'qty' => [
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

    /**
     * @return PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function makeExcel ()
    {
        $attributes = $this->columns();
        $materials = $this::find()->all();
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
                    $column, $row, $value, $type);
                $column++;
            }
            $row++;
            $column = 0;
        }
        return $phpExcel;
    }

    /**
     * @return string
     */
    public function getEditorName()
    {
        return $this->editor instanceof User ? $this->editor->fullName : 'not set';
    }

    /**
     * @return string
     */
    public function getCreatorName()
    {
        return $this->creator instanceof User ? $this->creator->fullName : 'not set';
    }

    /**
     * @param string $attr
     * @return float|int
     */
    public function getExcelTimestamp($attr)
    {
        $timestamp = (int) Yii::$app->formatter->asTimestamp($this->{$attr});
        return 25569 + (($timestamp + Yii::$app->params['timeZoneShift']) / 86400);
    }
}