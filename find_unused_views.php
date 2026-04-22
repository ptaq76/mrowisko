<?php

$viewsDir = __DIR__ . '/resources/views';
$appDir = __DIR__ . '/app/Http/Controllers';
$routesDir = __DIR__ . '/routes';

$views = [];

// Recursive function to find all views
function getViews($dir, $prefix = '') {
    global $views;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            if ($file === 'layouts') continue; // skip layouts
            getViews($path, $prefix . $file . '.');
        } else {
            if (strpos($file, '_') === 0) continue; // skip partials
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && strpos($file, '.blade.php') !== false) {
                $viewName = $prefix . str_replace('.blade.php', '', $file);
                $views[$viewName] = [
                    'path' => $path,
                    'used' => false
                ];
            }
        }
    }
}

getViews($viewsDir);

// Search inside multiple directories
function checkUsage($dir) {
    global $views;
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            foreach ($views as $viewName => $data) {
                if ($data['used']) continue;

                // Check for view('view.name') or view("view.name") or view::make('view.name') 
                // Or @include('view.name') inside Blade
                $patterns = [
                    "['\"]" . preg_quote($viewName) . "['\"]",
                ];
                foreach ($patterns as $pattern) {
                    if (preg_match('/' . $pattern . '/', $content)) {
                        $views[$viewName]['used'] = true;
                        break;
                    }
                }
            }
        }
    }
}

checkUsage($appDir);
checkUsage($routesDir);
checkUsage($viewsDir); // check for @include etc.

$unused = [];
foreach ($views as $viewName => $data) {
    if (!$data['used']) {
        $unused[] = $viewName;
    }
}

echo "Unused views (no route/controller/view seems to load them):\n";
foreach ($unused as $u) {
    echo "- " . $u . "\n";
}
echo "Total unused: " . count($unused) . "\n";
