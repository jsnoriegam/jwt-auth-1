<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) 2014-2021 Sean Tymon <tymon148@gmail.com>
 * (c) 2021 PHP Open Source Saver
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPOpenSourceSaver\JWTAuth\Console;

use Closure;
use Illuminate\Support\Str;

trait EnvHelperTrait
{
    /**
     * Checks if the env file exists.
     */
    public function envFileExists(): bool
    {
        return file_exists($this->envPath());
    }

    /**
     * Update an env-file entry.
     *
     * @param string|int $value
     */
    public function updateEnvEntry(string $key, $value, Closure $confirmOnExisting = null): bool
    {
        static $filepath = null;

        if (is_null($filepath)) {
            $filepath = $this->envPath();
        }

        if (false === Str::contains(file_get_contents($filepath), $key)) {
            // create new entry
            file_put_contents(
                $filepath,
                PHP_EOL."{$key}={$value}".PHP_EOL,
                FILE_APPEND
            );

            return true;
        } else {
            if (is_null($confirmOnExisting) || $confirmOnExisting()) {
                // update existing entry
                file_put_contents(
                    $filepath,
                    str_replace(
                        "/{$key}=.*/",
                        "{$key}={$value}",
                        file_get_contents($filepath)
                    )
                );

                return true;
            }
        }

        return false;
    }

    /**
     * Get the .env file path.
     */
    protected function envPath(): string
    {
        if (method_exists($this->laravel, 'environmentFilePath')) {
            return $this->laravel->environmentFilePath();
        }

        // check if laravel version Less than 5.4.17
        if (version_compare($this->laravel->version(), '5.4.17', '<')) {
            return $this->laravel->basePath().DIRECTORY_SEPARATOR.'.env';
        }

        return $this->laravel->basePath('.env');
    }
}
