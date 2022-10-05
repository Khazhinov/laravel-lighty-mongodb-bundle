<?php

declare(strict_types=1);

namespace Khazhinov\LaravelLightyMongoDBBundle\Console\Commands\Generator;

use Khazhinov\LaravelLighty\Console\Commands\Generator\BaseGenerator;

final class ModelMongoDBGenerator extends BaseGenerator
{
    public function initGeneratorParams(): void
    {
        $this->default_generator_namespace = 'App\Models';
        $this->default_generator_dir = 'Models';
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "lighty:generate-model-mongodb 
                            {model_name : Название модели. Используйте слэш (/) для вложенности.}
                            {--type=loggingable : Тип модели - l|loggingable or a|authenticatable}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Метод для генерации моделей MongoDB с использованием предлагаемой пакетом архитектуры.';

    /**
     * @var array<string, string>
     */
    private array $available_stubs = [
        'authenticatable' => 'model.mongodb.authenticatable.stub',
        'loggingable' => 'model.mongodb.loggingable.stub',
    ];

    private string $current_stub;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->initGeneratorParams();

        if (! $this->checkType()) {
            return 1;
        }

        /** @var string $model_name */
        $model_name = $this->argument('model_name');
        $this->initGenerator($model_name);

        if ($this->checkClassExist($this->class_path)) {
            $this->output->warning("Модель [{$this->class_name}] уже существует.");

            return 1;
        }

        if (! $this->createClass()) {
            $this->output->warning("Не получилось создать модель ({$this->class_path}).");

            return 1;
        }

        $this->output->info("Модель [{$this->class_name}] создана успешно.");

        return 0;
    }

    private function checkType(): bool
    {
        switch ($type = $this->option('type')) {
            case 'l':
            case 'loggingable':
                $this->current_stub = __DIR__ . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR .  $this->available_stubs['loggingable'];
                if (! file_exists($this->current_stub)) {
                    $this->output->warning('Заготовка модели вида Loggingable не найдена.');

                    return false;
                }

                return true;
            case 'a':
            case 'authenticatable':
                $this->current_stub = __DIR__ . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . $this->available_stubs['authenticatable'];
                if (! file_exists($this->current_stub)) {
                    $this->output->warning('Заготовка модели вида Authenticatable не найдена.');

                    return false;
                }

                return true;
            default:
                /** @var string $type */
                $this->output->warning('Неизвестный тип модели: ' . $type);
        }

        return false;
    }

    public function makeClassData(): string
    {
        /** @var string $data */
        $data = @file_get_contents($this->current_stub);

        $data = str_ireplace("{{ model_namespace }}", $this->class_namespace, $data);
        $data = str_ireplace("{{ model_name }}", $this->class_name, $data);

        return $data;
    }
}
