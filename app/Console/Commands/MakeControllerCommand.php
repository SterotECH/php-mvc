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
        $view = strtolower($modelName);
        return <<<PHP
        <?php

        namespace App\Http\Controllers;

        use App\Models\\$modelName;
        use App\Core\Request;
        use App\Core\Response;
        use App\Core\Router;

        class $controllerName extends Controller
        {
            public function index(): void
            {
                Response::view('$view/index', [
                    '$view\\s'=> $modelName::all()
                ]);
            }

            public function create(): void
            {
                Response::view('$view/create',[
                    'errors' => Session::get('errors')
                ]);
            }

            public function store(Request \$request): void
            {
                \$$view = new $modelName();

                \$$view\\->save();

                Response::redirect(Router::previousUrl());

            }

            public function show(Request \$request): void
            {
                \$id = \$request->params()->id;

                \$$view = $modelName::findById(\$id);
                Response::view('$view/show', [
                    '$view' => \$$view
                ]);
            }

            public function edit(Request \$request): void
            {
                \$id = \$request->params()->id;
                \$$view = $modelName::findById(\$id);

                Response::view('$view/edit', [
                    '$view' => \$$view,
                ]);
            }

            public function update(Request \$request): void
            {
                \$request->validate([]);
                \$$view = new $modelName();

                \$id = \$request->params()->id;
                \$$view\\->id = \$id;

                \$$view\\->save();

                Response::redirect(Router::previousUrl());
            }

            public function destroy(Request \$request): void
            {
                $modelName::delete(\$request->params()->id);

                Response::redirect(Router::previousUrl());
            }
        }
        PHP;
    }
}
