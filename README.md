# Codeigniter Vite
Integrate Vite to CodeIgniter 4.

## Setup

Download package using composer.

```shell
composer require nongbit/codeigniter-vite
```

Run command to initiate vite.

```shell
php spark vite:init
```

Create and open `APPPATH/Config/Vite.php`.

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public array $entryPoints = [
        '' => 'Views/assets/js/app.js',
    ];
}
```

## Usage

Inside your view, call `vite_url()`.

If the vite server is active then `vite_url()` will produce code like the following.

```html
<script type="module" src="http://localhost:5173/app/Views/assets/js/app.js"></script>
```

If `manifest.json` is found then `vite_url()` will read the file and generate the appropriate html code, something like this:

```html
<link rel="stylesheet" href="http://localhost/assets/app.css">
<script type="module" src="http://localhost/assets/app.js">
```
