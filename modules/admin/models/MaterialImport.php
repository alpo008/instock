<?php


namespace app\modules\admin\models;


use Yii;
use app\custom\FileStorage;
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
 *
 * @property array $defaultColumns
 * @property array $attributesList
 */

class MaterialImport extends Material
{
    const SKIP_ROW = 1;
    const DO_NOT_SKIP_ROW = 0;

    const COLUMNS_TEMPLATE_STORAGE = 'material_import_template';

    const PHP_EXCEL_CACHE_KEY = 'phpExcelMaterials';
    const PHP_EXCEL_CACHE_DURATION = 1800;

    public $file;
    public $skipFirstRow = self::SKIP_ROW;
    public $columns;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $fileStorage = new FileStorage();
        $this->columns = $fileStorage->getContent(self::COLUMNS_TEMPLATE_STORAGE);
        if (!$this->validateColumns()) {
            $this->columns = $this->defaultColumns;
        }
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
            [['skipFirstRow'], 'boolean']
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
            'file' => Yii::t('app', 'File')
        ];
    }

    /**
     * @param int $startRow
     * @param int $endRow
     * @return array количество импортированных записей
     * @throws \PHPExcel_Exception
     */
    public function import($startRow = 1, $endRow = 50)
    {
        $touched = 0;
        $processed = 0;
        $error = false;
        if (!$this->validateColumns()) {
            $error = Yii::t('app', 'Bad columns configuration');
            return compact('processed','touched', 'error');
        }
        if ($this->file instanceof UploadedFile) {
            try{
                $fileType = \PHPExcel_IOFactory::identify($this->file->tempName);
                $objReader = \PHPExcel_IOFactory::createReader($fileType);
                $phpExcel = $objReader->load($this->file->tempName);
                Yii::$app->cache->set(self::PHP_EXCEL_CACHE_KEY,
                    $phpExcel,
                    self::PHP_EXCEL_CACHE_DURATION
                );
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $phpExcel = Yii::$app->cache->get(self::PHP_EXCEL_CACHE_KEY);
        }

        if ($phpExcel instanceof \PHPExcel) {
            $sheet = $phpExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            if ($startRow === 1) {
                $startRow = (int)$this->skipFirstRow === $this::SKIP_ROW ? 2 : 1;
            }
            if ($highestRow >= $startRow) {
                $endRow = $endRow > $highestRow ? $highestRow : $endRow;
                for ($row = $startRow; $row <= $endRow; $row++) {
                    $processed = $row;
                    $materialAttributes = [];
                    foreach ($this->columns as $index => $column) {
                        $cell = $sheet->getCell($index . $row);
                        $value = $cell->getValue();
                        if (is_null($value) && $column['default'] === false) {
                            continue 2;
                        }
                        $value = is_null($value) ? $column['default'] : $value;
                        $value = !empty($column['getter']) ? $this->{$column['getter']}($value) : $value;
                        $materialAttributes[$column['attribute']] = $value;
                    }

                    if ($material = $this::findOne(['ref' => $materialAttributes['ref']])) {
                        unset ($materialAttributes['qty']);
                    } else {
                        $material = new Material();
                        $materialAttributes['qty'] = 0;
                    }
                    $material->setAttributes($materialAttributes, false);
                    if ($material->save()) {
                        $touched++;
                        if ($row === 2 && (int)$this->skipFirstRow === $this::SKIP_ROW) {
                            $touched++;
                        }
                    }
                }
            }
        }
        $total = isset($highestRow) ? $highestRow : 0;
        return compact('total', 'processed', 'touched', 'error');
    }

    /**
     * @param string $unit
     * @return int
     */
    public function getUnitCode ($unit)
    {
        $unitCodes = array_map('mb_strtolower', $this->unitsList);
        $unitCodes = array_flip($unitCodes);
        $unit = mb_strtolower(trim($unit, ' .'));
        return !empty($unitCodes[$unit]) ? $unitCodes[$unit] : 0;
    }

    /**
     * @return array[]
     */
    protected function getDefaultColumns()
    {
        return [
            'A' => [
                'attribute' => 'ref',
                'default' => false,
                'type' => 'text'
            ],
            'B' => [
                'attribute' => 'name',
                'default' => Yii::t('app', 'Not set'),
                'type' => 'text'
            ],
            'C' => [
                'attribute' => 'qty',
                'default' => 0,
                'type' => 'number'
            ],
            'D' => [
                'attribute' => 'min_qty',
                'default' => 0,
                'type' => 'number'
            ],
            'E' => [
                'attribute' => 'max_qty',
                'default' => 1,
                'type' => 'number'
            ],
            'F' => [
                'attribute' => 'unit',
                'getter' => 'getUnitCode',
                'default' => 0,
                'type' => 'number',
            ],
            'G' => [
                'attribute' => 'type',
                'default' => Yii::t('app', 'Not set'),
                'type' => 'text'
            ],
            'H' => [
                'attribute' => 'group',
                'default' => Yii::t('app', 'No group'),
                'type' => 'text'
            ]
        ];
    }
}