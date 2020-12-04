<?php


namespace Davajlama\AntLog\Storage;

class FileStorage implements StorageInterface
{
    /** @var string */
    const SEPARATOR = '<!==ant-log-separator==!>';

    /** @var string */
    private $tempDir;

    /**
     * FileStorage constructor.
     * @param $tempDir
     * @throws \Exception
     */
    public function __construct($tempDir)
    {
        if(!is_dir($tempDir)) {
            throw new \Exception("Directory [$tempDir] not exists");
        }

        $this->tempDir = $tempDir;
    }

    /**
     * @param string $type
     * @param string $runner
     * @param array $data
     */
    public function store($type, $runner, array $data)
    {
        $filename = $this->createFileName($type, $runner);
        file_put_contents($filename, json_encode($data) . self::SEPARATOR, FILE_APPEND);
    }

    /**
     * @return ArrayHelper
     */
    public function load()
    {
        $list = new ArrayHelper();
        foreach($this->findFiles() as $file) {
            $list->append($this->loadFile($file));
        }

        return $list;
    }

    /**
     * @return void
     */
    public function clean()
    {
        foreach($this->findFiles() as $file) {
            unlink($file);
        }
    }

    /**
     * @return array|false
     */
    protected function findFiles()
    {
        return glob($this->tempDir . '/*.antlog');
    }

    protected function loadFile($file)
    {
        $content = file_get_contents($file);

        $list = new ArrayHelper();
        foreach(explode(self::SEPARATOR, $content) as $json) {
            if($json) {
                $list[] = json_decode($json);
            }
        }

        return $list;
    }

    /**
     * @param string $type
     * @param string $runner
     * @return string
     */
    public function createFileName($type, $runner)
    {
        $date = date("Ymd");
        return sprintf("%s.%s.%s.antlog", $type, $runner, $date);
    }
}