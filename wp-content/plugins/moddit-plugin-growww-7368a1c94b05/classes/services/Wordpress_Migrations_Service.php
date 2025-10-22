<?php

namespace Growww\Services;

use Exception;

class Wordpress_Migrations_Service
{
    private string $version_key;
    private array $migrations;
    private ?string $version = null;

    public function __construct(array $migrations, string $version_key = 'growww_migrations_version')
    {
        $this->migrations = $migrations;
        $this->version_key = $version_key;
        if (!empty($migrations)) {
            $this->version = (string) max(array_keys($migrations));
        }
    }

    public function has_new_migrations(): bool
    {
        if (!isset($this->version)) {
            return false;
        }
        $db_version = get_option($this->version_key);
        return !$db_version || $db_version < $this->version;
    }

    public function run_new_migrations(): void
    {
        if ($this->has_new_migrations()) {
            $db_version = get_option($this->version_key);
            $this->log('Starting migration(s).');
            if ($updated_version = $this->run_above($db_version)) {
                update_option($this->version_key, $updated_version);
            }
        }
    }

    private function run_above($old_version): ?string
    {
        global $wpdb;
        $return_value = null;
        foreach ($this->migrations as $version => $migration) {
            $version = (string) $version;
            if ($this->version >= $version && $old_version < $version) {
                try {
                    if (is_callable($migration)) $result = $migration($wpdb);
                    else $result = $wpdb->query($migration);
                } catch (Exception $err) {
                    $this->log("Error migrating DB to version $version: " . $err->getMessage(), false);
                    trigger_error($err->getMessage());
                    $result = false;
                }
                if (!$result) break;
                $return_value = $version;
                $this->log("Succesfully migrated to version $version", true);
            }
        }
        return $return_value;
    }

    private function log(string $text, ?bool $success = null): void
    {
        if (function_exists('re_add_dev_message')) {
            re_add_dev_message($text, $success);
        }
        if (function_exists('growww_log')) {
            if ($success === null) $prio = 2;
            else $prio = $success ? 3 : 4;
            growww_log($prio, 'migrates', $text);
        }
    }
}
