<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFullModel extends Command
{
    protected $signature = 'make:full-model
                            {name : The PascalCase name of the model (e.g. BlogPost)}
                            {--namespace= : Observer sub-namespace under App\\Observers (default: same as model name)}
                            {--table= : Override the auto-derived table name}
                            {--no-migration : Skip creating a migration}
                            {--no-observer : Skip creating an observer}
                            {--no-factory : Skip creating a factory}
                            {--no-soft-deletes : Exclude SoftDeletes trait from the model}
                            {--force : Overwrite existing files without prompting}';

    protected $description = 'Create a model with trait files for relations, accessors, mutators, and scopes.';

    /**
     * Trait types to scaffold.
     */
    private const TRAIT_TYPES = ['Relations', 'Accessors', 'Mutators', 'Scopes'];

    /**
     * Base path where stubs are stored.
     */
    private function stubPath(string $stub): string
    {
        $custom = base_path("stubs/full-model/{$stub}");

        return File::exists($custom) ? $custom : __DIR__ . "/stubs/{$stub}";
    }

    public function handle(): int
    {
        $name = $this->argument('name');

        // ── 1. Validate the model name ─────────────────────────────────────────
        if (! preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name)) {
            $this->error("Invalid model name \"{$name}\". Must be PascalCase and contain only alphanumeric characters (e.g. BlogPost).");
            return self::FAILURE;
        }

        // ── 2. Resolve paths and names ─────────────────────────────────────────
        $modelDir       = app_path("Models/{$name}");
        $tableName      = $this->option('table') ?? Str::plural(Str::snake($name));
        $observerNs     = $this->resolveObserverNamespace($name);
        $withSoftDeletes = ! $this->option('no-soft-deletes');
        $withObserver    = ! $this->option('no-observer');
        $withFactory     = ! $this->option('no-factory');
        $withMigration   = ! $this->option('no-migration');

        $this->info("Scaffolding model <comment>{$name}</comment>...");
        $this->newLine();

        // ── 3. Check for existing files and handle conflicts ───────────────────
        if (File::exists($modelDir) && ! $this->option('force')) {
            if (! $this->confirm("Directory <comment>{$modelDir}</comment> already exists. Files may be overwritten. Continue?")) {
                $this->warn('Aborted.');
                return self::FAILURE;
            }
        }

        // ── 4. Create model directory structure ────────────────────────────────
        File::ensureDirectoryExists($modelDir);
        foreach (self::TRAIT_TYPES as $type) {
            File::ensureDirectoryExists("{$modelDir}/{$type}");
        }

        // ── 5. Write model ─────────────────────────────────────────────────────
        $this->writeModel($name, $modelDir, $tableName, $observerNs, $withSoftDeletes, $withObserver);

        // ── 6. Write traits ────────────────────────────────────────────────────
        foreach (self::TRAIT_TYPES as $type) {
            $this->writeTrait($name, $type, $modelDir);
        }

        // ── 7. Optionally write factory ────────────────────────────────────────
        if ($withFactory) {
            $this->writeFactory($name);
        }

        // ── 8. Optionally write observer ───────────────────────────────────────
        if ($withObserver) {
            $this->writeObserver($name, $observerNs);
        }

        // ── 9. Optionally create migration ─────────────────────────────────────
        if ($withMigration) {
            $this->call('make:migration', ['name' => "create_{$tableName}_table"]);
        }

        // ── 10. Summary ────────────────────────────────────────────────────────
        $this->newLine();
        $this->info("✔ Full model structure for <comment>{$name}</comment> created successfully!");
        $this->newLine();
        $this->printSummary($name, $tableName, $observerNs, $withSoftDeletes, $withObserver, $withFactory, $withMigration);

        return self::SUCCESS;
    }

    // ── Private: resolve observer namespace ────────────────────────────────────

    private function resolveObserverNamespace(string $name): string
    {
        $custom = $this->option('namespace');

        return $custom ? trim($custom, '\\') : $name;
    }

    // ── Private: writers ───────────────────────────────────────────────────────

    private function writeModel(
        string $name,
        string $dir,
        string $tableName,
        string $observerNs,
        bool $withSoftDeletes,
        bool $withObserver,
    ): void {
        $observerFqn     = "App\\Observers\\{$observerNs}\\{$name}Observer";
        $observerClass   = "{$name}Observer";

        $observerImport    = $withObserver ? "use {$observerFqn};" : '';
        $observedByAttr    = $withObserver ? "#[ObservedBy([{$observerClass}::class])]\n" : '';
        $softDeletesTrait  = $withSoftDeletes ? ', SoftDeletes' : '';
        $softDeletesImport = $withSoftDeletes ? "\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;" : '';

        // Add the SoftDeletes import after the Model import if needed
        $stub = $this->loadStub('model.stub');

        // If not using soft deletes, remove the import line from stub
        $stub = str_replace(
            'use Illuminate\Database\Eloquent\SoftDeletes;',
            ltrim($softDeletesImport),
            $stub
        );

        $stub = $this->replaceStubPlaceholders($stub, [
            '{{Name}}'              => $name,
            '{{TableName}}'         => $tableName,
            '{{ObserverImport}}'    => $observerImport,
            '{{ObservedByAttribute}}' => $observedByAttr,
            '{{SoftDeletesTrait}}'  => $softDeletesTrait,
        ]);

        $this->writeFile("{$dir}/{$name}.php", $stub, "Model");
    }

    private function writeTrait(string $name, string $type, string $dir): void
    {
        $stub = $this->replaceStubPlaceholders($this->loadStub('trait.stub'), [
            '{{Name}}' => $name,
            '{{Type}}' => $type,
        ]);

        $this->writeFile("{$dir}/{$type}/{$name}{$type}.php", $stub, "{$type} trait");
    }

    private function writeFactory(string $name): void
    {
        $factoryDir = database_path("factories/{$name}");
        File::ensureDirectoryExists($factoryDir);

        $stub = $this->replaceStubPlaceholders($this->loadStub('factory.stub'), [
            '{{Name}}' => $name,
        ]);

        $this->writeFile("{$factoryDir}/{$name}Factory.php", $stub, "Factory");
    }

    private function writeObserver(string $name, string $observerNs): void
    {
        // Build directory path from namespace (e.g. "Admin\Blog" → "Admin/Blog")
        $relPath     = str_replace('\\', '/', $observerNs);
        $observerDir = app_path("Observers/{$relPath}");
        File::ensureDirectoryExists($observerDir);

        $stub = $this->replaceStubPlaceholders($this->loadStub('observer.stub'), [
            '{{Name}}'             => $name,
            '{{ObserverNamespace}}' => $observerNs,
        ]);

        $this->writeFile("{$observerDir}/{$name}Observer.php", $stub, "Observer");
    }

    // ── Private: helpers ───────────────────────────────────────────────────────

    /**
     * Load a stub file from the stubs directory.
     *
     * @throws \RuntimeException When the stub file does not exist.
     */
    private function loadStub(string $stub): string
    {
        $path = $this->stubPath($stub);

        if (! File::exists($path)) {
            $this->error("Stub file not found: {$path}");

            throw new \RuntimeException("Stub file not found: {$path}");
        }

        return File::get($path);
    }

    /**
     * Replace all placeholder tokens in a stub string.
     *
     * @param array<string, string> $replacements
     */
    private function replaceStubPlaceholders(string $stub, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }

    /**
     * Write a file to disk, skipping if it already exists and --force was not passed
     * (the directory-level confirm was already accepted at this point).
     */
    private function writeFile(string $path, string $contents, string $label): void
    {
        if (File::exists($path) && ! $this->option('force')) {
            $this->line("  <fg=yellow>SKIP</>  {$label}: <comment>{$path}</comment> (already exists)");
            return;
        }

        File::put($path, $contents);
        $this->line("  <fg=green>CREATE</> {$label}: <comment>{$path}</comment>");
    }

    /**
     * Print a tidy summary table of what was generated.
     */
    private function printSummary(
        string $name,
        string $tableName,
        string $observerNs,
        bool $withSoftDeletes,
        bool $withObserver,
        bool $withFactory,
        bool $withMigration,
    ): void {
        $this->table(
            ['Component', 'Status', 'Location'],
            [
                ['Model',          '✔ Created', "app/Models/{$name}/{$name}.php"],
                ['Relations trait','✔ Created', "app/Models/{$name}/Relations/{$name}Relations.php"],
                ['Accessors trait','✔ Created', "app/Models/{$name}/Accessors/{$name}Accessors.php"],
                ['Mutators trait', '✔ Created', "app/Models/{$name}/Mutators/{$name}Mutators.php"],
                ['Scopes trait',   '✔ Created', "app/Models/{$name}/Scopes/{$name}Scopes.php"],
                ['Factory',        $withFactory  ? '✔ Created' : '— Skipped', $withFactory  ? "database/factories/{$name}/{$name}Factory.php" : ''],
                ['Observer',       $withObserver ? '✔ Created' : '— Skipped', $withObserver ? "app/Observers/{$observerNs}/{$name}Observer.php" : ''],
                ['Migration',      $withMigration ? '✔ Created' : '— Skipped', $withMigration ? "database/migrations/..._create_{$tableName}_table.php" : ''],
                ['SoftDeletes',    $withSoftDeletes ? '✔ Enabled' : '— Disabled', ''],
            ]
        );
    }
}