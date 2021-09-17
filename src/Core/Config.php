<?php

namespace Zheltikov\Cms\Core;

use function Zheltikov\Cms\array_is_list;

/**
 *
 */
class Config
{
    use SingletonTrait;

    public const CONFIG_FILENAME = '.cms_config.json5';

    /**
     * @var array
     */
    private $constants = [];

    /**
     * @throws \Zheltikov\Cms\Core\Exception|\JsonException
     * @throws \Exception
     */
    public function load(): void
    {
        // Check the config file
        $filename = __DIR__ . '/../../' . static::CONFIG_FILENAME;
        if (!file_exists($filename)) {
            throw new Exception('Config file ' . static::CONFIG_FILENAME . ' does not exist!');
        }

        // Read the config file
        $contents = file_get_contents($filename);
        if ($contents === false) {
            throw new Exception('Could not read config file ' . static::CONFIG_FILENAME . '!');
        }

        // Parse and load the config
        $this->parseConfig($contents);

        // Load essential initial configuration
        require_once(__DIR__ . '/../init.php');
    }

    /**
     * @param string $contents
     * @throws \Exception
     */
    private function parseConfig(string $contents): void
    {
        // decode the data
        $data = json5_decode(
            $contents,
            true,
            512,
            JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR
        );

        // process elements, define constants
        foreach ($data as $constant => $value) {
            $this->set($constant, $this->resolveValue($value), false);
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function resolveValue($value)
    {
        if (is_string($value)) {
            $count = null;
            return preg_replace_callback(
                '/(\{\$([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)\})/imU',
                function (array $matches): string {
                    if ($this->isset($matches[2])) {
                        return (string) $this->get($matches[2], '');
                    }

                    if (defined($matches[2])) {
                        return (string) constant($matches[2]);
                    }

                    return '';
                },
                $value,
                -1,
                $count
            );
        } elseif (is_array($value)) {
            $result = [];
            if (array_is_list($value)) {
                foreach ($value as $v) {
                    $result[] = $this->resolveValue($v);
                }
            } else {
                foreach ($value as $k => $v) {
                    $result[$this->resolveValue($k)] = $this->resolveValue($v);
                }
            }
            return $result;
        } else {
            return $value;
        }
    }

    /**
     * @param string $constant
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $constant, $default = null)
    {
        return array_key_exists($constant, $this->constants)
            ? $this->constants[$constant]
            : $default;
    }

    /**
     * @param string $constant
     * @param mixed|null $value
     * @param bool $throw
     * @throws \Exception
     */
    public function set(string $constant, $value = null, bool $throw = true): void
    {
        if ($this->isset($constant)) {
            if ($throw) {
                throw new Exception('Cannot redefine constant: ' . $constant);
            }
        } else {
            $this->constants[$constant] = $value;
        }
    }

    /**
     * @param string $constant
     * @return bool
     */
    public function isset(string $constant): bool
    {
        return array_key_exists($constant, $this->constants);
    }
}
