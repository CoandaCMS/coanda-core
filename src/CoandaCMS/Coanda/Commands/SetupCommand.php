<?php namespace CoandaCMS\Coanda\Commands;

use Illuminate\Console\Command;
use Coanda;

/**
 * Class SetupCommand
 * @package CoandaCMS\Coanda\Commands
 */
class SetupCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'coanda:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the coanda CMS system';

    /**
     * @var
     */
    private $userRepository;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->userRepository = $app->make('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface');

        parent::__construct();
    }

    /**
     * Run the command
     */
    public function fire()
    {
        $this->info('--------------------------');
        $this->info('Welcome to CoandaCMS setup');
        $this->info('--------------------------');

        $admin_group_name = $this->ask('First, we need to set up an admin user group, what shall we call it? (default: Administrators)');

        if ($admin_group_name == '')
        {
            $admin_group_name = 'Administrators';
        }

        // create the main admin group....
        $group = $this->userRepository->createGroup(['name' => $admin_group_name, 'permissions' => ['*']]);

        $this->info('Now, lets setup the first user account for you');

        $first_name = $this->ask('First name? (default: Admin)');

        if ($first_name == '')
        {
            $first_name = 'Admin';
        }

        $last_name = $this->ask('Last name? (default: User)');

        if ($last_name == '')
        {
            $last_name = 'User';
        }

        $email = $this->ask('Email address? (default: demo@somewhere.com)');

        if ($email == '')
        {
            $email = 'demo@somewhere.com';
        }

        $password = $this->ask('Password? (default: password)');

        if ($password == '')
        {
            $password = 'password';
        }

        $user_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        try
        {
            $user = $this->userRepository->createNew($user_data, $group->id);

            $this->info('All done, you can now log in with the details specified.');
        }
        catch (\CoandaCMS\Coanda\Exceptions\ValidationException $exception)
        {
            foreach ($exception->getInvalidFields() as $invalid)
            {
                $this->error($invalid);
            }
        }
    }
}