<?php
namespace App\Console\Commands;
use App\Role;
use App\Permission;
use Illuminate\Console\Command;
class AuthRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create-role {name} {--R|remove}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create auth role for a model';
    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$roleName = $this->getNameArgument();
        // check if its remove
        if( $is_remove = $this->option('remove') ) {
            // remove permission
            if( Role::where('name', 'LIKE', '%'. $roleName)->delete() ) {
                $this->warn('Role ' . $roleName . ' deleted.');
            }  else {
                $this->warn('No role ' . $roleName .' found!');
            }
        } else {
            // create roles
            $name = $this->getNameArgument();
            Role::firstOrCreate(['name' => $name ]);
            
            $this->info('Role ' . $name . ' created.');
        }
    }

    /**
     * Get pluralized name argument
     *
     * @return string
     */
    private function getNameArgument()
    {
        return strtolower($this->argument('name'));
    }
}