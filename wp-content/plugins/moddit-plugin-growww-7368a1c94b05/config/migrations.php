<?php
if (!defined('GROWWW_ALGEMEEN_DIR')) die;

return [
    // Translations custom table
    '2022-04-25' => "
        CREATE TABLE `growww_i18n_strings` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `lang` varchar(6) NOT NULL,
            `domain` varchar(64) NOT NULL,
            `msgid` text NOT NULL,
            `msgstr` text,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];
