<?php

use app\models\Material;
use yii\db\Migration;
use app\custom\FileStorage;

/**
 * Class m210430_132736_seed_material_groups_list
 */
class m210430_132736_seed_material_groups_list extends Migration
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
        $groupsList = $this->getGroupsList();
        return $this->fileStorage->setContent(Material::GROUPS_LIST_STORAGE, $groupsList);
    }

    /**
     * @return bool|void|null
     */
    public function down()
    {
        $this->fileStorage->delete(Material::GROUPS_LIST_STORAGE);
    }

    /**
     * @return array
     */
    protected function getGroupsList ()
    {
        return [
            'CHECK+' => 'CHECK+',
            'CHECK+ V5' => 'CHECK+ V5',
            'CHECK+ V6' => 'CHECK+ V6',
            'CHECK+ V6 PILZ' => 'CHECK+ V6 PILZ',
            'CHECK+ V6 SCHNEIDER' => 'CHECK+ V6 SCHNEIDER',
            'LNMP' => 'LNMP',
            'MCAL3' => 'MCAL3',
            'MCAL4' => 'MCAL4',
            'MULTI3' => 'MULTI3',
            'MULTI3,4' => 'MULTI3,4',
            'MULTI4' => 'MULTI4',
            'Общего назначения' => 'Общего назначения'
        ];
    }
}