<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use app\custom\FileStorage;

/**
 * This is the model class for table "materials".
 *
 * @property int $id ИД
 * @property int $ref Номер САП
 * @property string $name Наименование
 * @property float|null $min_qty Крит. низкое кол-во
 * @property float|null $max_qty Макс. кол-во
 * @property int $unit Ед. измерения
 * @property string|null $type Тип
 * @property string|null $group Группа
 * @property string|null $created_at Дата добавления
 * @property string|null $updated_at Дата изменения
 * @property int|null $created_by Кто добавил
 * @property int|null $updated_by Кто отредактировал
 * @property UploadedFile $photo Фото
 *
 * @property MaterialStock[] $materialsStocks
 * @property Stock[] $stocks
 * @property float $quantity Текущее кол-во
 * @property array $unitsList
 * @property string $unitName
 * @property User $creator
 * @property User $editor
 * @property string $photoPath
 * @property array $stockAliases
 * @property string $shortName
 * @property array $groupsList
 */
class Material extends \yii\db\ActiveRecord
{
    const PHOTOS_PATH = '@app/web/images/materials/';
    const PHOTOS_EXTENSIONS = ['jpg', 'png', 'jpeg'];

    const GROUPS_LIST_STORAGE = 'material_groups_list';

    const SHORT_NAME_LENGTH = 37;

    const UNIT_UNDEFINED = 0;
    const UNIT_PCS = 1;
    const UNIT_METERS = 2;
    const UNIT_PAIRS = 3;
    const UNIT_KG = 4;

    public $photo;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%materials}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ref', 'name'], 'required'],
            [['ref', 'unit', 'created_by', 'updated_by'], 'integer'],
            [['min_qty', 'max_qty'], 'number', 'min' => 0],
            ['max_qty', 'compare', 'compareAttribute' => 'min_qty', 'operator' => '>=', 'type' => 'number'],
            ['min_qty', 'compare', 'compareAttribute' => 'max_qty', 'operator' => '<=', 'type' => 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['type', 'group'], 'string', 'max' => 16],
            [['ref'], 'unique'],
            [['photo'], 'file', 'extensions' => implode(',', self::PHOTOS_EXTENSIONS), 'maxSize' => 1024*1024]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ref' => Yii::t('app', 'Ref'),
            'name' => Yii::t('app', 'Material name'),
            'quantity' => Yii::t('app', 'Qty'),
            'min_qty' => Yii::t('app', 'Min qty'),
            'max_qty' => Yii::t('app', 'Max qty'),
            'unit' => Yii::t('app', 'Unit'),
            'type' => Yii::t('app', 'Type'),
            'group' => Yii::t('app', 'Group'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'created_by' => Yii::t('app', 'Created by'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'photo' => Yii::t('app', 'Photo'),
            'stockAliases' => Yii::t('app', 'Stock places')
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => Yii::$app->formatter->asDatetime(time()),
            ],
            BlameableBehavior::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->photo = UploadedFile::getInstance($this, 'photo')) {
            $this->deletePhoto();
            $this->photo->saveAs($this::PHOTOS_PATH . $this->ref . '.' . $this->photo->extension);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialsStocks()
    {
        return $this->hasMany(MaterialStock::class, ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::class, ['id' => 'stock_id'])
            ->via('materialsStocks');
    }

    /**
     * @param integer $stockId
     * @return int|float
     */
    public function getQuantity($stockId = 0)
    {
        $query = $this->getMaterialsStocks()->select('SUM(qty)');
        if ($stockId !== 0) {
            $query->where(['stock_id' => $stockId]);
        }
        return !is_null($query->scalar()) ? $query->scalar() : 0;
    }

    /**
     * @inheritDoc
     */
    public function afterDelete()
    {
        $this->deletePhoto();
    }

    /**
     * Администратор, добавивший данный материал
     * @return \yii\db\ActiveQuery
     */
    public function getCreator ()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Администратор, редактировавший данный материал последним
     * @return \yii\db\ActiveQuery
     */
    public function getEditor ()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * List of units
     * @return array
     */
    public function getUnitsList ()
    {
        return [
            self::UNIT_UNDEFINED => Yii::t('app', '?'),
            self::UNIT_PCS => Yii::t('app', 'Pcs'),
            self::UNIT_METERS => Yii::t('app', 'Meters'),
            self::UNIT_PAIRS => Yii::t('app', 'Pairs'),
            self::UNIT_KG => Yii::t('app', 'Kg'),
        ];
    }

    /**
     * Measurement unit name
     * @return string
     */
    public function getUnitName ()
    {
        return !empty($this->unitsList[$this->unit]) ?
            $this->unitsList[$this->unit] :
            $this->unitsList[0];
    }

    /**
     * @return array|null
     */
    public function getGroupsList()
    {
        $fileStorage = new FileStorage();
        return $fileStorage->getContent(self::GROUPS_LIST_STORAGE);
    }

    /**
     * Путь для отображения фото
     * @return string
     */
    public function getPhotoPath ()
    {
        foreach (self::PHOTOS_EXTENSIONS as $extension) {
            $path = Yii::getAlias(self::PHOTOS_PATH) . $this->ref . '.' . $extension;
            if(is_file($path)) {
                return '@web/images/materials/' . $this->ref . '.' . $extension;
            }
        }
        return  '';
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

    /**
     * @return array
     */
    public function getStockAliases ()
    {
        $aliases = [];
        if (!empty($this->stocks) && is_array($this->stocks)) {
            $aliases = array_column($this->stocks, 'alias', 'id');
        }
        return $aliases;
    }

    /**
     * Cut too long material names
     * @return string|null
     */
    public function getShortName ()
    {
        $re = '/<[^>]*>/';
        $shortText = preg_replace($re, ' ', $this->name);
        if (strlen($shortText) < self::SHORT_NAME_LENGTH) {
            return $shortText;
        }
        $shortText = substr($shortText, 0, self::SHORT_NAME_LENGTH);
        $end = strlen(strrchr($shortText, ' '));
        return substr($shortText, 0, -$end);
    }

    /**
     * Удаление фото материала
     */
    private function deletePhoto ()
    {
        foreach (self::PHOTOS_EXTENSIONS as $extension) {
            $path = Yii::getAlias(self::PHOTOS_PATH) . $this->ref . '.' . $extension;
            if(is_file($path)) {
                unlink($path);
            }
        }
    }
}
