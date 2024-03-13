<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: "make:controller")]
class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';

    protected function configure()
    {
        $this->setDescription('Define a new Controller for a model')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the controller it must be the name of a model');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $controllerName = $input->getArgument('name');
        if (empty($controllerName)){
            $controllerName = $io->ask('Enter the name of the Controller preferable the name of the model');
        }
        $className = ucfirst($controllerName) . 'Controller';
        $fileName = $className . '.php';
        $filePath = base_path('/app/Http/Controllers/') . $fileName;

        if (file_exists($filePath)) {
            $io->error("Controller '$controllerName' already exists.");
            return Command::FAILURE;
        }
        $modelName = ucfirst($controllerName);

        $controllerContent = $this->controllerContent($modelName, $className);

        file_put_contents($filePath, $controllerContent);

        $io->success("Controller '$controllerName' created successfully at: $filePath");

        return Command::SUCCESS;
    }

    private function controllerContent(string $modelName, $controllerName): string
    {
        return <<<PHP
        <?php

        namespace App\Http\Controllers;

        use App\Models\\$modelName;
        use App\Core\Request;
        use App\Core\Session;

        class $controllerName extends Controller
        {
            public function index(): void
            {
                /*
                 * Implement index method
                 *
                 * This method is called when the user visits the route /users
                 * it acts as the home page if using the resource controller
                 * remember to use the render method to render the view
                 * and pass the data to the view
                 *
                 * Example:
                 *
                 * \$users = User::all();
                 * \$this->render('users', [
                 *  'users' => \$users
                 * ]);
                 *
                 * TODO: register the router for the controller in the routes directory
                 * all web route can be found in the `routes/web.php` file
                 *
                 * Example:
                 *
                 * \`Router::resource('/users', '$controllerName::class')`;
                 *
                 * or to register a single route use
                 * \`Router::get('/users', [$controllerName, 'index'])`;
                 *
                */

            }

            public function create(): void
            {
                /*
                 * Implement create method
                 * it uses `GET` method to render the create form
                 * this method is used to render the create form and render errors from the
                 * session if the is any
                 * Example:
                 *
                 * \$this->render('users/create',[
                 *  'errors' => Session::get('errors')
                 * ]);
                */
            }

            public function store(Request \$request): void
            {
                /*
                * Implement store method
                * it uses `POST` method to store the data from the create form
                * this method is used to store the data from the create form
                * this method is responsible for creating or storing the user into the database
                * Example:
                *   \$this->validate((array) \$request->all(), [
                *     'name' => 'required|string|max:255',
                *     'email' => 'required|string|email|max:255|unique:users,email',
                *     'password' => 'required|string|min:6|confirmed',
                *   ]);
                *
                *   \$user = new User();
                *   \$user->name = \$request-input('name');
                *   \$user->email = \$request-input('email');
                *   \$user->password = \$request-input('password');
                *   \$user = \$user->save()
                *
                *   \$this->redirect('/users');
                *
                */
            }

            public function show(Request \$request): void
            {
                /* Implement show method
                * it uses `GET` method to render the show page
                * this method is used to render the show page and render errors from the
                * session if the is any
                * in other words it is used to view a record
                *
                * Example:
                * \$id = \$request->params()->id;
                * \$user = User::getById(\$id); // if we use id
                * \$this->render("users/show", [
                *    "user" => \$user,
                *     "errors" => Session::get('errors')
                *  ]);
                *
                */
            }

            public function edit(Request \$request): void
            {
                /* Implement edit method
                * it uses `GET` method to render the edit form
                * this method is used to render the edit form and render errors from the
                * session if the is any
                * in other words it is used to edit a record
                *
                * Example:
                *   \$id = \$request->params()->id;
                *   \$user = User::getById(\$id);
                *
                * \$this->render('users/edit', [
                *    'user' => \$user,
                *    'errors' => Session::get('errors')
                ]);
                */
            }

            public function update(Request \$request): void
            {
                /* Implement update method
                * it uses `PUT` or `PATCH` method to update the data from the edit form
                * this method is used to update the data from the edit form
                * this method is responsible for updating or storing the user into the database
                * Example:
                * \$this->validate((array)\$request->all(), [
                *     'name' => 'required|string|max:255',
                *     'email' => 'required|string|email|max:255',
                *     'password' => 'required|string|min:6|confirmed',
                * ])
                *
                * \$user = User::getById(\$request->params()->id);
                * \$user = new User();
                * \$user->name = \$request->input('name');
                * \$user->email = \$request->input('email');
                * \$user->password = \$request->input('password');
                * \$user = \$user->save()
                *   if (\$user){
                *    redirect("/users/{\$user[0]->id}/show");
                *  }
                */
            }

            public function destroy(Request \$request): void
            {
                /*\ Implement destroy method
                *   This method is used to delete a record from the database
                *   we begin by fetching the record from the database using the query params
                *
                *   \$user_id = \$request->params->id;
                *   \$User::delete(\$user_id);
                *
                *
                */
            }
        }
        PHP;
    }
}
