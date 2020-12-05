<?php


namespace Davajlama\AntLog\Utils;


class Output
{
    /** @var Colorizer */
    private $colorizer;

    /**
     * @param $text
     * @return $this
     */
    public function writeHeadline($text)
    {
        $this->writeBreak()
                ->writeLine($this->getColorizer()->magenta(sprintf('==================  %s  ==================', $text)))
                ->writeBreak();

        return $this;
    }

    /**
     * @return $this
     */
    public function writeBreak()
    {
        $this->write(PHP_EOL);
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function writeLine($text)
    {
        $this->write($text)->writeBreak();
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function write($text)
    {
        echo $text;
        return $this;
    }

    /**
     * @return Colorizer
     */
    protected function getColorizer()
    {
        if($this->colorizer === null) {
            $this->colorizer = new Colorizer();
        }

        return $this->colorizer;
    }
}