<?php

/**
 * @author    <contact@lotfio.net>
 *
 * @version   1.6.0
 *
 * @license   MIT
 *
 * @category  CLI
 *
 * @copyright 2019 Lotfio Lakehal
 */
$conso->command('command', Conso\Commands\Command::class);
$conso->command('compile', Conso\Commands\Compile::class);
$conso->command('make', Conso\Commands\Make::class);