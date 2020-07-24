<?php namespace Conso\Commands;

/**
 *
 * @author    <contact@lotfio.net>
 * @package   Conso PHP Console Creator
 * @version   1.6.0
 * @license   MIT
 * @category  CLI
 * @copyright 2019 Lotfio Lakehal
 */

use function Conso\commandHelp;
use Conso\{Conso, Command};
use Conso\Exceptions\InputException;
use Conso\Contracts\{CommandInterface,InputInterface,OutputInterface};

class Compile extends Command implements CommandInterface
{
    /**
     * sub commands
     *
     * @var array
     */
    protected $sub = [

    ];

    /**
     * flags
     *
     * @var array
     */
    protected $flags = [

    ];

    /**
     * command help
     *
     * @var string
     */
    protected $help  = [

    ];

    /**
     * command description
     *
     * @var string
     */
    protected $description = 'This is Compile command description.';

    /**
     * execute method
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output) : void
    {
        if(ini_get('phar.readonly') !== "")
            throw new InputException("phar.readonly must be turned off in php.ini");

        if(!$input->option(0))
            throw new InputException("build location is required");

        if(!is_dir($input->option(0)) || !is_writable($input->option(0)))
            throw new InputException("build location must be a valid directory");

        if(!is_dir('bin') || !is_writable('bin'))
            throw new Exception("bin directory is not writable");

        if(is_dir('bin/pkg'))
            $this->cleanBuildFiles('bin/pkg');

        $this->copyBuildFiles('src',    'bin/pkg/src');
        $this->copyBuildFiles('vendor', 'bin/pkg/vendor');
        copy('commands.php', 'bin/pkg/commands.php');
        copy('conso', 'bin/pkg/conso');

        // create stb
        $contents = file_get_contents('bin/pkg/conso');
        $contents = str_replace("#!/usr/bin/env php", NULL, $contents);
        file_put_contents('bin/pkg/conso', trim($contents));

        // remove built in commands
        unlink('bin/pkg/src/Conso/Commands/Command.php');
        unlink('bin/pkg/src/Conso/Commands/Compile.php');

        // remove built in commands definition
        $contents = file_get_contents('bin/pkg/commands.php');
        $contents = str_replace('$conso->command(\'command\', Conso\Commands\Command::class);', NULL, $contents);
        $contents = str_replace('$conso->command(\'compile\', Conso\Commands\Compile::class);', NULL, $contents);
        file_put_contents('bin/pkg/commands.php', trim($contents));

        // compile package
        $phar = new \Phar($input->option(0) . '/conso.phar');
        // add all files in the project, only include php files
        $phar->buildFromDirectory('bin/pkg', '/^(?!.*build\\.php)(?:.*)$/');
        $phar->setStub('#!/usr/bin/env php' . PHP_EOL . $phar->createDefaultStub('conso'));

        $this->cleanBuildFiles('bin/pkg');
    }


    /**
     * copy build files
     *
     * @param string $source
     * @param string $dest
     * @return void
     */
    private function copyBuildFiles(string $source, string $dest) : void
    {
        mkdir($dest, 0755, true);

        foreach ( $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST) as $item)
        {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * clean build files
     *
     * @param string $dir
     * @return void
     */
    private function cleanBuildFiles(string $dir) : bool
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->cleanBuildFiles("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}