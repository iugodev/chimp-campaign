<?php namespace ChimpCampaigns\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class MigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'chimpCampaigns:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the ChimpCampaigns specifications.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->laravel->view->addNamespace('ChimpCampaigns', substr(__DIR__, 0, -8).'views');


        $this->line('');

        if ($this->confirm("Proceed with the migration creation? [Yes|no]", "Yes")) {

            $this->line('');

            $this->info("Creating migration...");
            if ($this->createMigration()) {

                $this->info("Migration successfully created!");
            } else {
                $this->error(
                    "Couldn't create migration.\n Check the write permissions".
                    " within the database/migrations directory."
                );
            }

            $this->line('');

        }
    }

    /**
     * Create the migration.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function createMigration()
    {
        $migrationFile = base_path("/database/migrations")."/".date('Y_m_d_His')."_chimp_campaign_setup_tables.php";


        $output = $this->laravel->view->make('ChimpCampaigns::generators.migration')->render();

        if (!file_exists($migrationFile) && $fs = fopen($migrationFile, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }

        return false;
    }
}
