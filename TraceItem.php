<?php

namespace demi\outlog;

class TraceItem
{
    public $file;
    public $line;
    public $function;
    public $class;
    public $args;
    public $contextLines = 3;

    public function __construct($file, $line, $function, $class = null, $args = array())
    {
        $this->file = $file;
        $this->line = $line;
        $this->function = $function;
        $this->class = $class;
        $this->args = $args;
    }

    /**
     * Get file context lines
     *
     * @return null|string
     */
    public function getFileContext()
    {
        if (empty($this->file) || $this->line === null) {
            return null;
        }

        $f = @fopen($this->file, 'r');
        if (!$f) {
            return null;
        }

        $startLine = $this->line - $this->contextLines;
        if ($startLine < 1) {
            $startLine = 1;
        }
        $endLine = $this->line + $this->contextLines;

        $lines = array();
        $currentLine = 0;
        while (($line = fgets($f)) !== false) {
            $currentLine++;

            if ($currentLine < $startLine) {
                continue;
            } elseif ($currentLine > $endLine) {
                break;
            }

            $lines[] = $line;
        }

        return implode(PHP_EOL, $lines);
    }
}