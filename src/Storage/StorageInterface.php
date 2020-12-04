<?php

namespace Davajlama\AntLog\Storage;

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
     * @return ArrayHelper
     */
    public function load();

    /**
     * @return void
     */
    public function clean();
}