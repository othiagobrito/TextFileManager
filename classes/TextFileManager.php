<?php

class TextFileManager
{
    public function __construct(protected string $filepath)
    {
        # code...
    }

    public function getFilename(): string
    {
        return basename($this->filepath);
    }

    public function getFilePath(): string
    {
        return $this->filepath;
    }

    public function getFileDirectory(): string
    {
        return dirname($this->getFilePath());
    }

    public function fileExists(): string
    {
        return is_file($this->filepath);
    }

    protected function getFile(): SplFileObject
    {
        return $this->setFileFlags(new SplFileObject($this->filepath));
    }

    public function getTotalLinesNumber(): int
    {
        $file = $this->getFile();
        $file->seek(PHP_INT_MAX);
        $file->seek($file->key());
        
        return $file->key() + 1;
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

        foreach ($lines as $number) {
            $file->seek($number);
            $file->eof() ?: Printer::printLine($file->current());
        }
    }

    public function chunk(?string $output = '', ?bool $map = false): array
    {
        if (! $this->fileExists()) {
            return ['status' => false, 'level' => 'fail', 'message' => 'Error: File not found.'];
        }

        $output ?: $output = $this->getFileDirectory();
        $totalLines = $this->getTotalLinesNumber() - 1;

        $file = $this->getFile();
        $extension = TextFileService::findFileExtension($this->getFilePath());
        $filename = TextFileService::findFilename($this->getFilePath(), $extension);

        $chunks = array_chunk(range(0, $totalLines), (int) sqrt($totalLines));
        ! $map ?: file_put_contents(filename: "{$output}/chunk-map.json", data: json_encode($chunks, JSON_PRETTY_PRINT));

        foreach ($chunks as $key => $chunk) {
            $chunkFilename = TextFileService::makeChunkFilename($filename, $extension, $key);
            
            $lastChunkKey = array_key_last($chunk);
            foreach ($chunk as $key => $line) {
                $file->seek($line);
                $content = $file->current();
                $lastChunkKey === $key ?: $content = $content . PHP_EOL;
                file_put_contents(filename: "{$output}/{$chunkFilename}", data: $content, flags: FILE_APPEND | LOCK_EX);
            }
        }

        return ['status' => true, 'level' => 'success', 'message' => 'File chunked successfully!'];
    }
}
