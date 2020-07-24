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
use Conso\Contracts\{CommandInterface,InputInterface,OutputInterface};

class Make extends Command implements CommandInterface
{
    /**
     * sub commands
     *
     * @var array
     */
    protected $sub = [
        'file'
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
    protected $description = 'This is Make command description.';

    /**
     * execute method
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output) : void
    {
        commandHelp($this->app->invokedCommand, $output);
    }

    public function file(InputInterface $input, OutputInterface $output)
    {
        mkdir('hello');
        touch('hello/conso.txt');
        $output->writeLn("File created ! \n", 'red');
    }
}