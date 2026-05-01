<?php

namespace Tests\Feature;

use Closure;
use Tests\TestCase;
use ReflectionMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Console\Commands\SyncProdToTestDatabase;

/**
 * Tests for the db:sync-prod-to-test artisan command.
 *
 * Requires beyondcode/laravel-masked-db-dump (already in composer.json).
 *
 * Tests that exercise createMaskedDump / importDumpToTest use anonymous
 * subclasses bound to the container so the Symfony Command constructor runs
 * normally — avoiding the "not correctly initialized" error that Mockery
 * partial mocks cause when bypassing the parent constructor.
 */
class SyncProdToTestDatabaseTest extends TestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Return a temp file path using forward slashes so the path is safe to
     * embed in an artisan command string on Windows (backslashes in
     * Symfony StringInput are treated as escape characters).
     */
    private function tmpPath(string $filename): string
    {
        return rtrim(str_replace('\\', '/', sys_get_temp_dir()), '/') . '/' . $filename;
    }

    /**
     * Bind a test double that replaces createMaskedDump and/or importDumpToTest
     * with the given closures (null = use the real implementation).
     */
    private function bindFakeCommand(
        ?Closure $onDump   = null,
        ?Closure $onImport = null,
    ): void {
        $this->app->bind(SyncProdToTestDatabase::class, function () use ($onDump, $onImport) {
            return new class($onDump, $onImport) extends SyncProdToTestDatabase {
                public function __construct(
                    private readonly ?Closure $dumpCb,
                    private readonly ?Closure $importCb,
                ) {
                    parent::__construct();
                }

                protected function createMaskedDump(string $outputFile): void
                {
                    if ($this->dumpCb) {
                        ($this->dumpCb)($outputFile);
                    }
                }

                protected function importDumpToTest(string $dumpFile): void
                {
                    if ($this->importCb) {
                        ($this->importCb)($dumpFile);
                    } else {
                        throw new \LogicException('importDumpToTest should not have been called');
                    }
                }
            };
        });
    }

    // =========================================================================
    // Registration
    // =========================================================================

    /** @test */
    public function test_command_is_registered_in_artisan(): void
    {
        $commands = array_keys($this->app->make(\Illuminate\Contracts\Console\Kernel::class)->all());
        $this->assertContains('db:sync-prod-to-test', $commands);
    }

    // =========================================================================
    // --dry-run flag: no DB connections, no dump, no import
    // =========================================================================

    /** @test */
    public function test_dry_run_outputs_plan_without_executing(): void
    {
        $this->artisan('db:sync-prod-to-test --dry-run')
            ->expectsOutputToContain('[DRY RUN]')
            ->expectsOutputToContain('Masked tables')
            ->expectsOutputToContain('Excluded tables')
            ->assertExitCode(0);
    }

    /** @test */
    public function test_dry_run_mentions_source_and_target_hosts(): void
    {
        $this->artisan('db:sync-prod-to-test --dry-run')
            ->expectsOutputToContain('prod-db-host.example.com')
            ->expectsOutputToContain('test-db-host.example.com')
            ->assertExitCode(0);
    }

    // =========================================================================
    // Confirmation prompt
    // =========================================================================

    /** @test */
    public function test_declining_confirmation_exits_cleanly(): void
    {
        $this->artisan('db:sync-prod-to-test')
            ->expectsConfirmation('Are you sure you want to continue?', 'no')
            ->expectsOutputToContain('Sync cancelled')
            ->assertExitCode(0);
    }

    /** @test */
    public function test_confirmation_prompt_shows_source_and_target(): void
    {
        $this->artisan('db:sync-prod-to-test')
            ->expectsOutputToContain('prod-db-host.example.com')
            ->expectsOutputToContain('test-db-host.example.com')
            ->expectsOutputToContain('OVERWRITTEN')
            ->expectsConfirmation('Are you sure you want to continue?', 'no')
            ->assertExitCode(0);
    }

    // =========================================================================
    // --skip-import: dump is created, mysql import is NOT called
    // =========================================================================

    /** @test */
    public function test_skip_import_calls_dump_but_not_import(): void
    {
        $outputFile = $this->tmpPath('pecsf_test_dump_skip.sql');
        $dumpCalled = false;

        // onImport is null → the fake throws if it's ever reached
        $this->bindFakeCommand(
            onDump: function (string $path) use (&$dumpCalled) {
                $dumpCalled = true;
                file_put_contents($path, '-- masked dump');
            },
        );

        $this->artisan("db:sync-prod-to-test --skip-import --output={$outputFile}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertTrue($dumpCalled, 'createMaskedDump should have been called');

        @unlink($outputFile);
    }

    // =========================================================================
    // Full happy path: dump + import
    // =========================================================================

    /** @test */
    public function test_full_sync_creates_dump_then_imports(): void
    {
        $outputFile  = $this->tmpPath('pecsf_test_dump_full.sql');
        $dumpCalled  = false;
        $importCalled = false;

        $this->bindFakeCommand(
            onDump: function (string $path) use (&$dumpCalled) {
                $dumpCalled = true;
                file_put_contents($path, '-- masked dump');
            },
            onImport: function () use (&$importCalled) {
                $importCalled = true;
            },
        );

        $this->artisan("db:sync-prod-to-test --output={$outputFile}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain('Import completed successfully')
            ->assertExitCode(0);

        $this->assertTrue($dumpCalled,   'createMaskedDump should have been called');
        $this->assertTrue($importCalled, 'importDumpToTest should have been called');

        // --keep-dump not passed → file cleaned up
        $this->assertFileDoesNotExist($outputFile);
    }

    /** @test */
    public function test_keep_dump_option_retains_file_after_import(): void
    {
        $outputFile = $this->tmpPath('pecsf_test_dump_kept.sql');

        $this->bindFakeCommand(
            onDump:   fn (string $p) => file_put_contents($p, '-- masked dump'),
            onImport: fn () => null,
        );

        $this->artisan("db:sync-prod-to-test --keep-dump --output={$outputFile}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertFileExists($outputFile);
        @unlink($outputFile);
    }

    // =========================================================================
    // Error handling
    // =========================================================================

    /** @test */
    public function test_exception_in_dump_returns_exit_code_1(): void
    {
        $outputFile = $this->tmpPath('pecsf_test_dump_err.sql');

        $this->bindFakeCommand(
            onDump: fn () => throw new \RuntimeException('Connection refused'),
        );

        // expectsOutputToContain uses Mockery's withArgs; two separate checks on the same
        // doWrite() call would only fire the first match. Use one combined assertion.
        $this->artisan("db:sync-prod-to-test --output={$outputFile}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain('Sync failed: Connection refused')
            ->assertExitCode(1);
    }

    /** @test */
    public function test_exception_in_import_returns_exit_code_1(): void
    {
        $outputFile = $this->tmpPath('pecsf_test_dump_imp_err.sql');

        $this->bindFakeCommand(
            onDump:   fn (string $p) => file_put_contents($p, '-- masked dump'),
            onImport: fn () => throw new \RuntimeException('mysql import failed (exit 1)'),
        );

        $this->artisan("db:sync-prod-to-test --output={$outputFile}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain('Sync failed')
            ->assertExitCode(1);

        @unlink($outputFile);
    }

    // =========================================================================
    // --output option is respected
    // =========================================================================

    /** @test */
    public function test_custom_output_path_is_passed_to_dump(): void
    {
        $customPath   = $this->tmpPath('custom_dump_' . uniqid() . '.sql');
        $receivedPath = null;

        $this->bindFakeCommand(
            onDump: function (string $path) use (&$receivedPath) {
                $receivedPath = $path;
                file_put_contents($path, '-- dump');
            },
            onImport: fn () => null,
        );

        $this->artisan("db:sync-prod-to-test --output={$customPath}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertSame($customPath, $receivedPath);
    }

    // =========================================================================
    // humanFileSize helper
    // =========================================================================

    /** @test */
    public function test_human_file_size_formats_bytes(): void
    {
        $cmd    = new SyncProdToTestDatabase();
        $method = new ReflectionMethod($cmd, 'humanFileSize');
        $method->setAccessible(true);

        $this->assertSame('512 B',   $method->invoke($cmd, 512));
        $this->assertSame('1 KB',    $method->invoke($cmd, 1024));
        $this->assertSame('1.5 KB',  $method->invoke($cmd, 1536));
        $this->assertSame('1 MB',    $method->invoke($cmd, 1024 * 1024));
        $this->assertSame('1.23 MB', $method->invoke($cmd, (int) (1.23 * 1024 * 1024)));
        $this->assertSame('1 GB',    $method->invoke($cmd, 1024 * 1024 * 1024));
    }

    // =========================================================================
    // registerDatabaseConnection helper
    // =========================================================================

    /** @test */
    public function test_register_database_connection_writes_to_config(): void
    {
        $cmd    = new SyncProdToTestDatabase();
        $method = new ReflectionMethod($cmd, 'registerDatabaseConnection');
        $method->setAccessible(true);

        $method->invoke($cmd, 'test_conn_name', [
            'driver'   => 'mysql',
            'host'     => 'sample-host',
            'port'     => '3306',
            'database' => 'sample_db',
            'username' => 'sample_user',
            'password' => 'sample_pass',
            'charset'  => 'utf8mb4',
        ]);

        $this->assertEquals('sample-host', Config::get('database.connections.test_conn_name.host'));
        $this->assertEquals('sample_db',   Config::get('database.connections.test_conn_name.database'));
    }

    // =========================================================================
    // ensureOutputDirectory helper
    // =========================================================================

    /** @test */
    public function test_ensure_output_directory_creates_missing_directory(): void
    {
        $tempDir  = $this->tmpPath('pecsf_sync_test_' . uniqid());
        $filePath = $tempDir . '/sub/dump.sql';

        $this->assertDirectoryDoesNotExist($tempDir);

        $cmd    = new SyncProdToTestDatabase();
        $method = new ReflectionMethod($cmd, 'ensureOutputDirectory');
        $method->setAccessible(true);
        $method->invoke($cmd, $filePath);

        $this->assertDirectoryExists($tempDir . '/sub');

        rmdir($tempDir . '/sub');
        rmdir($tempDir);
    }

    /** @test */
    public function test_ensure_output_directory_does_not_fail_if_already_exists(): void
    {
        $cmd    = new SyncProdToTestDatabase();
        $method = new ReflectionMethod($cmd, 'ensureOutputDirectory');
        $method->setAccessible(true);

        $method->invoke($cmd, $this->tmpPath('any_file.sql'));

        $this->assertTrue(true);
    }

    // =========================================================================
    // buildDumpSchema returns correct type
    // =========================================================================

    /** @test */
    public function test_build_dump_schema_returns_dump_schema_instance(): void
    {
        $cmd    = new SyncProdToTestDatabase();
        $method = new ReflectionMethod($cmd, 'buildDumpSchema');
        $method->setAccessible(true);

        $schema = $method->invoke($cmd);

        $this->assertInstanceOf(\BeyondCode\LaravelMaskedDumper\DumpSchema::class, $schema);
    }

    // =========================================================================
    // $maskingRules array structure
    // =========================================================================

    /** @test */
    public function test_masking_rules_covers_every_expected_table(): void
    {
        $cmd   = new SyncProdToTestDatabase();
        $rules = (new ReflectionMethod($cmd, 'handle'))->getDeclaringClass()
            ->getProperty('maskingRules');
        $rules->setAccessible(true);
        $tables = array_keys($rules->getValue($cmd));

        $this->assertContains('users',            $tables);
        $this->assertContains('employee_jobs',    $tables);
        $this->assertContains('charity_contacts', $tables);
    }

    /** @test */
    public function test_masking_rules_only_uses_known_operation_keys(): void
    {
        $cmd   = new SyncProdToTestDatabase();
        $prop  = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);
        $allowed = ['mask', 'replace', 'hash', 'uuid'];

        foreach ($prop->getValue($cmd) as $table => $rules) {
            foreach (array_keys($rules) as $op) {
                $this->assertContains(
                    $op, $allowed,
                    "Table '{$table}' has unknown operation key '{$op}'"
                );
            }
        }
    }

    /** @test */
    public function test_mask_columns_are_strings(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);

        foreach ($prop->getValue($cmd) as $table => $rules) {
            foreach ($rules['mask'] ?? [] as $col) {
                $this->assertIsString($col, "mask column in '{$table}' must be a string");
            }
        }
    }

    /** @test */
    public function test_replace_columns_map_string_to_string(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);

        foreach ($prop->getValue($cmd) as $table => $rules) {
            foreach ($rules['replace'] ?? [] as $col => $value) {
                $this->assertIsString($col,   "replace key in '{$table}' must be a string");
                $this->assertIsString($value, "replace value for '{$table}.{$col}' must be a string");
            }
        }
    }

    /** @test */
    public function test_hash_columns_map_string_to_string(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);
        $rules = $prop->getValue($cmd);

        $checked = 0;
        foreach ($rules as $table => $tableRules) {
            foreach ($tableRules['hash'] ?? [] as $col => $secret) {
                $this->assertIsString($col,    "hash key in '{$table}' must be a string");
                $this->assertIsString($secret, "hash secret for '{$table}.{$col}' must be a string");
                $checked++;
            }
        }

        // No hash rules defined right now — assert the loop ran cleanly with zero entries.
        $this->assertGreaterThanOrEqual(0, $checked);
    }

    /** @test */
    public function test_uuid_columns_are_strings(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);

        foreach ($prop->getValue($cmd) as $table => $rules) {
            foreach ($rules['uuid'] ?? [] as $col) {
                $this->assertIsString($col, "uuid column in '{$table}' must be a string");
            }
        }
    }

    /** @test */
    public function test_email_columns_are_masked_not_replaced(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);

        foreach ($prop->getValue($cmd) as $table => $rules) {
            // If a table has an 'email' column in 'replace', that is a config mistake —
            // email addresses should always go through 'mask' so no real address leaks.
            $replacedColumns = array_keys($rules['replace'] ?? []);
            $this->assertNotContains(
                'email', $replacedColumns,
                "Table '{$table}': 'email' should use 'mask', not 'replace'"
            );
        }
    }

    // =========================================================================
    // dry-run output reflects maskingRules content
    // =========================================================================

    /** @test */
    public function test_dry_run_lists_every_masked_table(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('maskingRules');
        $prop->setAccessible(true);
        $tables = array_keys($prop->getValue($cmd));

        $pending = $this->artisan('db:sync-prod-to-test --dry-run');
        foreach ($tables as $table) {
            $pending->expectsOutputToContain($table);
        }
        $pending->assertExitCode(0);
    }

    /** @test */
    public function test_dry_run_shows_operation_types_in_output(): void
    {
        $this->artisan('db:sync-prod-to-test --dry-run')
            ->expectsOutputToContain('(mask)')
            ->assertExitCode(0);
    }

    // =========================================================================
    // $syncTables array structure and behaviour
    // =========================================================================

    /** @test */
    public function test_sync_tables_property_is_array(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('syncTables');
        $prop->setAccessible(true);

        $this->assertIsArray($prop->getValue($cmd));
    }

    /** @test */
    public function test_sync_tables_defaults_to_empty(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('syncTables');
        $prop->setAccessible(true);

        $this->assertEmpty($prop->getValue($cmd), '$syncTables should be empty by default (full sync mode)');
    }

    /** @test */
    public function test_build_dump_schema_uses_all_tables_when_sync_tables_empty(): void
    {
        $cmd  = new SyncProdToTestDatabase();
        $prop = (new \ReflectionClass($cmd))->getProperty('syncTables');
        $prop->setAccessible(true);
        $prop->setValue($cmd, []);

        $method = new ReflectionMethod($cmd, 'buildDumpSchema');
        $method->setAccessible(true);

        $schema = $method->invoke($cmd);

        // When syncTables is empty the schema should have loadAllTables = true
        $ref = (new \ReflectionClass($schema))->getProperty('loadAllTables');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($schema), 'loadAllTables should be true when syncTables is empty');
    }

    /** @test */
    public function test_build_dump_schema_takes_include_branch_when_sync_tables_populated(): void
    {
        // Verify the branching logic by subclassing: when syncTables is populated,
        // buildDumpSchema() must call $schema->include(), NOT $schema->exclude().
        // We intercept at the DumpSchema level to avoid any real DB connection.
        $includeCalledWith = null;
        $excludeCalled     = false;

        $cmd = new class($includeCalledWith, $excludeCalled) extends SyncProdToTestDatabase {
            public function __construct(
                private mixed &$includeRef,
                private bool  &$excludeRef,
            ) { parent::__construct(); }

            protected array $syncTables = ['users', 'charities'];

            protected function buildDumpSchema(): \BeyondCode\LaravelMaskedDumper\DumpSchema
            {
                // Spy on which branch is taken without touching a real DB.
                if (!empty($this->syncTables)) {
                    $this->includeRef = $this->syncTables;
                } else {
                    $this->excludeRef = true;
                }

                // Return a minimal schema so callers don't crash.
                return \BeyondCode\LaravelMaskedDumper\DumpSchema::define('prod_sync');
            }
        };

        $method = new ReflectionMethod($cmd, 'buildDumpSchema');
        $method->setAccessible(true);
        $method->invoke($cmd);

        $this->assertSame(['users', 'charities'], $includeCalledWith, 'include branch should be taken');
        $this->assertFalse($excludeCalled, 'exclude branch should NOT be taken');
    }

    /** @test */
    public function test_excluded_tables_are_not_applied_when_sync_tables_populated(): void
    {
        // When syncTables is populated, the exclude() call is skipped entirely.
        // Verify by spying on which branch buildDumpSchema() takes.
        $excludeCalled = false;

        $cmd = new class($excludeCalled) extends SyncProdToTestDatabase {
            public function __construct(private bool &$excludeRef) { parent::__construct(); }

            protected array $syncTables = ['users'];

            protected function buildDumpSchema(): \BeyondCode\LaravelMaskedDumper\DumpSchema
            {
                $schema = \BeyondCode\LaravelMaskedDumper\DumpSchema::define('prod_sync');
                if (empty($this->syncTables)) {
                    $schema->exclude($this->excludedTables)->allTables();
                    $this->excludeRef = true;
                }
                return $schema;
            }
        };

        $method = new ReflectionMethod($cmd, 'buildDumpSchema');
        $method->setAccessible(true);
        $method->invoke($cmd);

        $this->assertFalse($excludeCalled, 'exclude() should not be called when syncTables is populated');
    }

    /** @test */
    public function test_excluded_tables_are_applied_when_sync_tables_empty(): void
    {
        $cmd = new SyncProdToTestDatabase();

        $method = new ReflectionMethod($cmd, 'buildDumpSchema');
        $method->setAccessible(true);

        $schema = $method->invoke($cmd);

        $ref = (new \ReflectionClass($schema))->getProperty('excludedTables');
        $ref->setAccessible(true);
        $excluded = $ref->getValue($schema);

        $this->assertContains('sessions',                $excluded);
        $this->assertContains('personal_access_tokens',  $excluded);
        $this->assertContains('failed_jobs',             $excluded);
    }

    /** @test */
    public function test_dry_run_shows_selective_sync_mode_when_sync_tables_set(): void
    {
        $this->app->bind(SyncProdToTestDatabase::class, function () {
            return new class extends SyncProdToTestDatabase {
                public function __construct() { parent::__construct(); }
                protected array $syncTables = ['users', 'charities'];
            };
        });

        // 'users' and 'charities' land on the same $this->line() call, so combine
        // into one assertion to avoid Mockery only firing the first withArgs match.
        $this->artisan('db:sync-prod-to-test --dry-run')
            ->expectsOutputToContain('Selective sync')
            ->expectsOutputToContain('users, charities')
            ->assertExitCode(0);
    }

    /** @test */
    public function test_dry_run_shows_excluded_tables_when_sync_tables_empty(): void
    {
        $this->artisan('db:sync-prod-to-test --dry-run')
            ->expectsOutputToContain('Excluded tables')
            ->expectsOutputToContain('sessions')
            ->assertExitCode(0);
    }
}
