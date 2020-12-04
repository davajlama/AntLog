<?php

namespace Davajlama\AntLog\Storage;

use Davajlama\AntLog\Utils\ArrayHelper;

interface StorageInterface
{

    /**
     * @param string $type
     * @param string $runner
     * @param array $data
     * @return void
     */
    public function store($type, $runner, array $data);

    /**
     * @param string $type
     * @return ArrayHelper
     */
    public function load($type);

    /**
     * @return void
     */
    public function clean();
}