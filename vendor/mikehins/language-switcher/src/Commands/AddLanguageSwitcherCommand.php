<?php

namespace mikehins\languageswitcher\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLanguageSwitcherCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'switch:add';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add a language switcher in the main navbar';
	
	protected $user;
	
	protected $table;
	
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->user = config('auth.providers.users.model');
		
		if (!$this->user) {
			$this->info('You must run php artisan make:auth before to use this package');
			die;
		}
		
		$this->table = (new $this->user)->getTable();
	}
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		if (!Schema::hasTable($this->table)) {
			$this->info('You must migrate the users table first php artisan migrate');
			die;
		}
		
		$this->addDefaultLanguageField()
			->addMenuItemToNavigation()
			->addRoute()
			->addToMiddleware()
			->addController()
			->addConfig()
			->addToLoginController()
			->addMiddleware();
	}
	
	protected function addDefaultLanguageField()
	{
		if (!Schema::hasColumn($this->table, 'default_language')) {
			Schema::table($this->table, function (Blueprint $table) {
				$table->char('default_language', 2)->nullable();
			});
			$this->info('The default_language field has been added to the "' . $this->table . '" table');
			
			return $this;
		}
		
		$this->info('The default_language field was already present in the "' . $this->table . '" table');
		
		return $this;
	}
	
	protected function addMenuItemToNavigation()
	{
		$view = array_first(config('view.paths')) . '/layouts/app.blade.php';
		$stub = file_get_contents(__DIR__ . '/../stubs/switcher.stub');
		$file = file_get_contents($view);
		
		if (strpos("config('languages')", $file) === false) {
			$file = str_replace('<ul class="nav navbar-nav navbar-right">', '<ul class="nav navbar-nav navbar-right">' . $stub, $file);
			$this->info('The switcher has been added to the navbar');
			file_put_contents($view, $file);
			
			return $this;
		}
		
		$this->info('The switcher was already in the navbar');
		
		return $this;
	}
	
	protected function addRoute()
	{
		$route = base_path('routes/web.php');
		$file = file_get_contents($route);
		$stub = file_get_contents(__DIR__ . '/../stubs/routes.stub');
		
		if (strpos($file, $stub) === false) {
			file_put_contents($route, $stub, FILE_APPEND);
			$this->info('The route has been added');
			
			return $this;
		}
		
		$this->info('The route already exists');
		return $this;
	}
	
	protected function addToLoginController()
	{
		$controller = app_path('Http/Controllers/Auth/LoginController.php');
		$file = file_get_contents($controller);
		$stub = file_get_contents(__DIR__ . '/../stubs/LoginController.stub');
		
		if (strpos($file, $stub) === false) {
			file_put_contents($controller, rtrim(trim($file), '}') . $stub . '}');
			$this->info('The LoginController has been modified');
			
			return $this;
		}
		
		$this->info('The LoginController already exists');
		return $this;
	}
	
	protected function addToMiddleware()
	{
		$kernel = app_path('Http/Kernel.php');
		$stub = file_get_contents(__DIR__ . '/../stubs/Kernel.stub');
		$file = file_get_contents($kernel);
		
		if (strpos($stub, $file) === false) {
			$file = str_replace('\Illuminate\Session\Middleware\StartSession::class,', '\Illuminate\Session\Middleware\StartSession::class,' . $stub, $file);
			$this->info('The switcher has been added to the middleware');
			file_put_contents($kernel, $file);
			
			return $this;
		}
		
		$this->info('The Kernel has already been modified');
		return $this;
	}
	
	protected function addController()
	{
		if (file_exists(base_path('config/languages.php'))) {
			$this->info('The LanguageController already exists');
			return $this;
		}
		
		file_put_contents(
			app_path('Http/Controllers/LanguageController.php'),
			file_get_contents(__DIR__ . '/../stubs/LanguageController.stub')
		);
		
		$this->info('The LanguageController has been created');
		
		return $this;
	}
	
	protected function addConfig()
	{
		if (file_exists(base_path('config/languages.php'))) {
			$this->info('The config file already exists');
			return $this;
		}
		
		file_put_contents(
			base_path('config/languages.php'),
			file_get_contents(__DIR__ . '/../stubs/languages.stub')
		);
		
		$this->info('The config file has been created');
		
		return $this;
	}
	
	protected function addMiddleware()
	{
		if (file_exists(app_path('Http/Middleware/Language.php'))) {
			$this->info('The language middleware file already exists');
			return $this;
		}
		
		file_put_contents(
			app_path('Http/Middleware/Language.php'),
			file_get_contents(__DIR__ . '/../stubs/LanguageMiddleware.stub')
		);
		
		$this->info('The language middleware has been created');
		
		return $this;
	}
}
