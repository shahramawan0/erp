<?php
class EnvironmentConfig {
    private static $loaded = false;
    private static $env = [];

    public static function load() {
        if (self::$loaded) return;
        $envFile = __DIR__ . '/../../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim(trim($value), '"\'');
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                    self::$env[$key] = $value;
                }
            }
        }
        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        self::load();
        return $_ENV[$key] ?? getenv($key) ?: (self::$env[$key] ?? $default);
    }
}
?>
