<?php

namespace Nongbit\Vite\Commands\Vite;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;

class VitePublishCommand extends BaseCommand
{
    protected $group = 'Vite';
    protected $name = 'vite:entry';
    protected $description = 'Create new vite entry point.';
    protected $usage = 'vite:entry name';

    protected $entries;

    public function run(array $entries): void
    {
        $this->entries = $entries;

        $this->publish();
    }

    protected function publish(): void
    {
        foreach ($this->entries as $entry) {
            $this->publishPackageInformation($entry);
            $this->publishViteConfiguration($entry);
            $this->publishAsset($entry);
        }
    }

    protected function publishPackageInformation(string $entry): void
    {
        $source = json_decode(file_get_contents(__DIR__ . '/../../files/package.json'), true);
        $target = [];

        if (is_readable(ROOTPATH . 'package.json')) {
            $target = json_decode(file_get_contents(ROOTPATH . 'package.json'), true);
            $target = is_array($target) ? $target : [];
        }

        $content = array_merge($this->arrayDiff($source, $target), $target);
        if ($entry !== '') $content['scripts']["build-{$entry}"] = "vite build -c {$entry}.vite.config.js";

        $content = json_encode($content, JSON_PRETTY_PRINT);

        try {
            file_put_contents(ROOTPATH . 'package.json', $content, LOCK_EX);
        } catch(Throwable $e) {
            $this->quit("Cant write to file: package.json");
        }
    }

    protected function publishViteConfiguration(string $entry): void
    {
        $content = file_get_contents(__DIR__ . '/../../files/vite.config.js');
        $content = str_replace('<OUTDIR>', $entry !== '' ? "/{$entry}" : '', $content);
        $content = str_replace('<ENTRYPOINT>', $entry !== '' ? "/{$entry}" : '', $content);

        $targetFilename = $entry !== '' ? "{$entry}.vite.config.js" : 'vite.config.js';
        try {
            file_put_contents(ROOTPATH . $targetFilename,  $content, LOCK_EX);
        } catch (Throwable $e) {
            $this->quit("Cant write to file: {$targetFilename}");
        }
    }

    protected function publishAsset(string $entry): void
    {
        helper('file');
        $targetPath = APPPATH . 'Views/' . ($entry !== '' ? "{$entry}/" : '') . 'assets';

        try {
            if (file_exists($targetPath) === false) mkdir($targetPath, fileperms(APPPATH), true);
            directory_mirror(__DIR__ . '/../../files/assets', $targetPath);
        } catch (Throwable $e) {
            $this->quit("Cant publish asset.");
        }
    }

    protected function arrayDiff(array $a, array $b): array
    {
        $differences = [];

        foreach ($a as $key => $value) {
            if (array_key_exists($key, $b)) {
                if (is_array($value)) {
                    $aRecursiveDiff = $this->arrayDiff($value, $b[$key]);
                    if (count($aRecursiveDiff)) $differences[$key] = $aRecursiveDiff;
                } else {
                    if ($value != $b[$key]) $differences[$key] = $value;
                }
            } else $differences[$key] = $value;
        }

        return $differences;
    }

    protected function quit(string $message = ''): void
    {
        if ($message !== '') CLI::error($message);
        CLI::newLine();
        exit;
    }
}