<?php


namespace app\custom;


use yii\base\BaseObject;

/**
 * Class FileStorage
 * @package app\custom
 *
 * @property string $pathAlias
 * @property string $path
 * @property string $prefix
 */
class FileStorage extends BaseObject implements StorageInterface
{
    const EXTENSION = 'dat';
    const DEFAULT_PATH_ALIAS = '@app/storage';

    protected $pathAlias;
    protected $path;
    protected $prefix;

    /**
     * @inheritDoc
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        if (empty($config['pathAlias'])) {
            $this->pathAlias = $this::DEFAULT_PATH_ALIAS;
        }
        $this->path = \Yii::getAlias($this->pathAlias);
    }

    /**
     * Prefix setter
     * @param string $prefix
     */
    public function setPrefix ($prefix)
    {
        if (is_string($prefix) && ctype_alnum($prefix) && strlen($prefix) > 0) {
            $this->prefix = $prefix;
        }
    }

    /**
     * @inheritDoc
     */
    public function getContent ($storageKey)
    {
        $fp = @fopen($this->getFileName($storageKey), 'r');
        if ($fp !== false) {
            flock($fp, LOCK_SH);
            $content = @stream_get_contents($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            if (!empty($content)) {
                return unserialize($content);
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setContent($storageKey, $content)
    {
        $serialized = serialize($content);
        return !!file_put_contents($this->getFileName($storageKey), $serialized, LOCK_EX);
    }

    /**
     * @inheritDoc
     */
    public function delete ($storageKey)
    {
        return unlink($this->getFileName($storageKey));
    }

    /**
     * @inheritDoc
     */
    public function exists ($storageKey)
    {
        return is_file($this->getFileName($storageKey));
    }

    /**
     * @param string $storageKey
     * @return string
     */
    protected function getFileName($storageKey)
    {
        if (is_string($storageKey)) {
            if (!empty($this->prefix)) {
                $storageKey = $this->prefix . '_' . $storageKey;
            }
            $storageKey = ctype_alnum($storageKey) && mb_strlen($storageKey) <= 32 ? $storageKey : md5($storageKey);
        } else {
            $serializedKey = serialize($storageKey);

            $storageKey = md5($serializedKey);
        }
        return $this->path . DIRECTORY_SEPARATOR . $storageKey . '.' . self::EXTENSION;
    }
}
