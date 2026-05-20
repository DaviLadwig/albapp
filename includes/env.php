<?php
function env_value(string $key, string $default = ''): string
{
    static $loaded = false;

    if (!$loaded) {
        $envPath = dirname(__DIR__) . '/.env';
        if (is_file($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$name, $value] = explode('=', $line, 2);
                $_ENV[trim($name)] = trim($value);
            }
        }
        $loaded = true;
    }

    return $_ENV[$key] ?? getenv($key) ?: $default;
}
?>
