<?php

class Printer
{
    public static function printLine(mixed $content): void
    {
        print_r($content);
        print_r(PHP_EOL);
    }
}
