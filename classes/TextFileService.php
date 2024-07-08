<?php

class TextFileService
{
    public static function findFileExtension(string $filename): string
    {
        $match = preg_match('/\.(.+)$/i', $filename, $matches);

        return $match ? $matches[1] : '';
    }

    public static function findFilename(string $filename, string $extension): string
    {
        return basename($filename, ".{$extension}");
    }

    public static function makeChunkFilename(string $filename, string $extension, int $chunkNumber): string
    {
        return "{$filename}-{$chunkNumber}.{$extension}";
    }
}
