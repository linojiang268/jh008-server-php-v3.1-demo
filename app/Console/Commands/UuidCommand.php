<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Console\Input\InputOption;

class UuidCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate UUID';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $level = $this->option('level');

        $this->line(sprintf('<info>%s</info>', $this->generate($level)));
    }

    private function generate($level)
    {
        $method = 'uuid' . substr($level, 1);
        if (!method_exists(Uuid::class, $method)) {
            $this->error('Invalid uuid level');
            return;
        }

        // only uuid3 and uuid5 accepts namespace and name though
        if (in_array($method, ['uuid3', 'uuid5'])) {
            return Uuid::$method($this->option('namespace'), $this->option('name'));
        }

        return Uuid::$method();
    }

    protected function getOptions()
    {
        return [
            ['level', 'l', InputOption::VALUE_OPTIONAL, 'Version of uuid(should be one of "v1", "v3", "v4" and "v5")', 'v4'],
            ['namespace', '', InputOption::VALUE_OPTIONAL, 'Namespace for v3 and v5', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'],
            ['name', '', InputOption::VALUE_OPTIONAL, 'Name for v3 and v5', 'zero2all'],
        ];
    }
}