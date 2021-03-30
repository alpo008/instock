<?php

use app\custom\FileStorage;
use app\modules\admin\models\MaterialImport;
use yii\db\Migration;

/**
 * Class m210330_110806_seed_material_import_template
 *
 * @property FileStorage $fileStorage
 */
class m210330_110806_seed_material_import_template extends Migration
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
        return $this->fileStorage->setContent(MaterialImport::COLUMNS_TEMPLATE_STORAGE, $template);
    }

    public function down()
    {
        $this->fileStorage->delete(MaterialImport::COLUMNS_TEMPLATE_STORAGE);
    }

    /**
     * @return array
     */
    protected function getTemplate ()
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
