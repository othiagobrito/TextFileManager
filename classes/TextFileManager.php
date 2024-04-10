<?php

class TextFileManager
{
    public function __construct(protected $filepath)
    {
        # code...
    }

    protected function getFile(): SplFileObject
    {
        return $this->setFileFlags(new SplFileObject($this->filepath));
    }

    protected function setFileFlags(SplFileObject $file): SplFileObject
    {
        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        return $file;
    }

    public function getSpecs(): void
    {
        $file = $this->getFile();

        Printer::printLine("Path: {$file->getPath()}");
        Printer::printLine("Filename: {$file->getFilename()}");
        Printer::printLine("Filesize: {$file->getSize()} bytes");
        Printer::printLine("First line: {$file->current()}");
        Printer::printLine('First line number: ' . $file->key() + 1);
        $file->seek(PHP_INT_MAX);
        $file->seek($file->key());
        Printer::printLine("Last line: {$file->current()}");
        Printer::printLine('Last line number: ' . $file->key() + 1);
        Printer::printLine('');
    }

    public function readSpecificLines(array $lines): void
    {
        $file = $this->getFile();

        foreach ($lines as $key => $number) {
            $file->seek($number);
            $file->eof() ?: Printer::printLine($file->current());
        }
    }
}
