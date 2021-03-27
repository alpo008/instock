<?php

namespace app\custom;

interface StorageInterface
{
    /**
     * @param string $storageKey
     * @return mixed
     */
    public function getContent($storageKey);

    /**
     * @param string $storageKey
     * @param mixed $content
     * @return boolean
     */
    public function setContent($storageKey, $content);

    /**
     * @param string $storageKey
     * @return boolean
     */
    public function delete($storageKey);

    /**
     * @param string $storageKey
     * @return boolean
     */
    public function exists($storageKey);
}