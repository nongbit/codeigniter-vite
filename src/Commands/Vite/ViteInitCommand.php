<?php

namespace Nongbit\Vite\Commands\Vite;

use CodeIgniter\CLI\BaseCommand;

class ViteInitCommand extends BaseCommand
{
	protected $group = 'Vite';
    protected $name = 'vite:init';
    protected $description = 'Initial vite integration.';
    protected $usage = 'vite:init';

    public function run(array $params): void
    {
        array_unshift($params, '');
        $this->call('vite:entry', $params);
    }
}