<?php

function vite_url(string $path = ''): ?string
{
    $config = config('Vite');
    if (empty($config->entryPoints)) return null;

    $mainUrl = function($url) {
        $parts = parse_url($url);

        return "{$parts['scheme']}://{$parts['host']}";
    };

    $entryPointUrl = $mainUrl(base_url()) . ':5173/' . $config->entryPoints[$path];
    if (@file_get_contents($entryPointUrl)) {
        return sprintf('<script type="module" src="%s"></script>', $entryPointUrl);
    }

    $mainPath = explode('/', $path)[0];
    $manifestUrl = base_url("{$mainPath}/.vite/manifest.json");
    $manifest = json_decode(@file_get_contents($manifestUrl), false);
    if (empty($manifest)) return null;

    $key = $config->entryPoints[$path];
    if (! property_exists($manifest, $key)) return null;

    $entryPoint = $manifest->{$key};
    if (! property_exists($entryPoint, 'isEntry')) return null;
    if ($entryPoint->isEntry !== true) return null;

    $styles = !empty($entryPoint->css) ? $entryPoint->css : [];
    $script = $entryPoint->file;

    $result = '';
    foreach ($styles as $style) {
        $result .= sprintf('<link rel="stylesheet" href="%s">', base_url("{$mainPath}/{$style}"));
    }

    $result .= sprintf('<script type="module" src="%s"></script>', base_url("{$mainPath}/{$script}"));

    return $result;
}