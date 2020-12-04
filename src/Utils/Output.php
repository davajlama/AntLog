<?php


namespace Davajlama\AntLog\Utils;


class Output
{
    /**
     * @param $text
     * @return $this
     */
    public function writeHeadline($text)
    {
        $this->writeLine(sprintf('====================%s====================', $text))
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
}