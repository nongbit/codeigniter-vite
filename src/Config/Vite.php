<?php

namespace Nongbit\Vite\Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public array $entryPoints = [
        '' => 'Views/assets/js/app.js',
    ];
}