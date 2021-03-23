<?php


namespace app\modules\admin\models;


use Yii;
use app\models\Material;
use yii\web\UploadedFile;

/**
 * Class MaterialImport
 * @package app\modules\admin\models
 *
 * @property UploadedFile $file
 *
 * @property array $columns
 * @property integer $skipFirstRow
 * @property integer $duplicatedKeyAction;
 *
 * @property array $defaultColumns
 * @property array $attributesList
 * @property array $duplicatedKeyActionsList
 */

class MaterialImport extends Material
{
    const SKIP_ROW = 1;
    const DO_NOT_SKIP_ROW = 0;

    const SKIP_DUPLICATED = 1;
    const REPLACE_DUPLICATED = 2;
    const MERGE_DUPLICATED = 3;

    public $file;
    public $skipFirstRow = self::SKIP_ROW;
    public $duplicatedKeyAction = self::MERGE_DUPLICATED;
    public $columns;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->columns = $this->defaultColumns;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['file'], 'file', //'extensions' => 'xlsx,xls',
                'mimeTypes'  => [
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ],
                'wrongMimeType'=> \Yii::t('app','Only excel files are allowed') . '!',
                'checkExtensionByMimeType' => false,
                'skipOnEmpty' => true],
            [['skipFirstRow', 'duplicatedKeyAction'], 'integer', 'min' => 0],
        ];
    }

    /**
     * Validates columns array structure
     * @return bool
     */
    public function validateColumns()
    {
        if (!is_array($this->columns)) {
            return false;
        }
        $required = ['attribute', 'default', 'type'];
        foreach ($this->columns as $key => $column) {
            if (!preg_match('/^[A-Z]{1,3}$/', $key)) {
                return false;
            }
            if (!is_array($column)) {
                return false;
            }
            foreach ($required as $item) {
                if (!array_key_exists($item, $column)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'skipFirstRow' => Yii::t('app', 'Ignore first row'),
            'duplicatedKeyAction' => Yii::t('app', 'Duplicated ref action'),
            'file' => Yii::t('app', 'File')
        ];
    }

    /**
     * @return array[]
     */
    public function getDefaultColumns()
    {
        return [
            'A' => [
                'attribute' => 'ref',
                'required' => true,
                'default' => false,
                'type' => 'text'
            ],
            'B' => [
                'attribute' => 'name',
                'required' => true,
                'default' => Yii::t('app', 'Not set'),
                'type' => 'text'
            ],
            'C' => [
                'attribute' => 'qty',
                'required' => false,
                'default' => 0,
                'type' => 'number'
            ],
            'D' => [
                'attribute' => 'min_qty',
                'required' => false,
                'default' => 0,
                'type' => 'number'
            ],
            'E' => [
                'attribute' => 'max_qty',
                'required' => false,
                'default' => 1,
                'type' => 'number'
            ],
            'F' => [
                'attribute' => 'unit',
                'required' => true,
                'default' => 0,
                'type' => 'number',
            ],
            'G' => [
                'attribute' => 'type',
                'required' => true,
                'default' => Yii::t('app', 'Not set'),
                'type' => 'text'
            ],
            'H' => [
                'attribute' => 'group',
                'required' => true,
                'default' => Yii::t('app', 'No group'),
                'type' => 'text'
            ]
        ];
    }

    /**
     * @return int количество импортированных записей
     * @throws \PHPExcel_Exception
     */
    public function import()
    {
        $result = 0;
        if (!$this->validateColumns()) {
            return $result;
        }
        if ($this->file instanceof UploadedFile) {
            try{
                $fileType = \PHPExcel_IOFactory::identify($this->file->tempName);
                $objReader = \PHPExcel_IOFactory::createReader($fileType);
                $phpExcel = $objReader->load($this->file->tempName);
            } catch (\Exception $e) {
            }
            $sheet = $phpExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $startRow = (int) $this->skipFirstRow === $this::SKIP_ROW ? 2 : 1;
            if ($highestRow >= $startRow) {
                for($row = $startRow; $row <= $highestRow; $row++) {
                    $materialAttributes = [];
                    foreach ($this->columns as $index => $column) {
                        $cell = $sheet->getCell($index . $row);
                        $value = $cell->getValue();
                        if (is_null($value) && $column['default'] === 'false') {
                            continue;
                        }
                        $value = is_null($value) ? $column['default'] : $value;
                        $materialAttributes[$column['attribute']] = $value;
                    }
                    if ($material = $this::findOne(['ref' => $materialAttributes['ref']])) {
                        switch ((int) $this->duplicatedKeyAction) {
                            case $this::MERGE_DUPLICATED :
                                $material->qty += $materialAttributes['qty'];
                            break;
                            case $this::REPLACE_DUPLICATED :
                                $material->qty = $materialAttributes['qty'];
                            break;
                        }
                        if ($material->save()) {
                            $result++;
                        }
                    } else {
                        $material = new Material($materialAttributes);
                        if ($material->save()) {
                            $result++;
                        }
                    }
                }
            }
        }
        return $result;
    }

/*    public function getAttributesList()
    {
        $list = $this->attributeLabels();
        $attributes = array_column($this->columns, 'attribute');
        $res = array_filter($list, function ($key) use ($attributes) {
            return in_array($key, $attributes);
        }, ARRAY_FILTER_USE_KEY);
        return $res;
    }*/

    /**
     * @return array
     */
    public function getDuplicatedKeyActionsList()
    {
        return [
            $this::SKIP_DUPLICATED => Yii::t('app', 'Save database value'),
            $this::REPLACE_DUPLICATED => Yii::t('app', 'Save file value'),
            $this::MERGE_DUPLICATED => Yii::t('app', 'Sum database and file values')
        ];
    }
}