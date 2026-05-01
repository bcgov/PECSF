<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use BeyondCode\LaravelMaskedDumper\DumpSchema;
use BeyondCode\LaravelMaskedDumper\LaravelMaskedDump;
use BeyondCode\LaravelMaskedDumper\TableDefinitions\TableDefinition;

class SyncProdToTestDatabase extends Command
{
    protected $signature = 'db:sync-prod-to-test
                            {--dry-run : Show what would be synced without executing}
                            {--skip-import : Create the dump file only, skip importing to test DB}
                            {--keep-dump : Keep the SQL dump file after a successful import}
                            {--output= : Custom path for the SQL dump file}';

    protected $description = 'Sync production database to test database using a masked dump (PII is anonymized)';

    // -------------------------------------------------------------------------
    // Database configurations — values are read from environment variables
    // injected by OpenShift secrets (see openshift/app/pecsf-dc.yml).
    //
    // Required env vars:
    //   DB_PROD_HOST, DB_PROD_PORT, DB_PROD_DATABASE,
    //   DB_PROD_USERNAME, DB_PROD_PASSWORD
    //   DB_TEST_HOST, DB_TEST_PORT, DB_TEST_DATABASE,
    //   DB_TEST_USERNAME, DB_TEST_PASSWORD
    // -------------------------------------------------------------------------
    protected function prodConfig(): array
    {
        return [
            'driver'    => 'mysql',
            'host'      => env('DB_PROD_HOST',     'prod-db-host.example.com'),
            'port'      => env('DB_PROD_PORT',     '3306'),
            'database'  => env('DB_PROD_DATABASE', 'pecsf_prod'),
            'username'  => env('DB_PROD_USERNAME', 'prod_readonly_user'),
            'password'  => env('DB_PROD_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'options'   => [],
        ];
    }

    protected function testConfig(): array
    {
        return [
            'driver'    => 'mysql',
            'host'      => env('DB_TEST_HOST',     'test-db-host.example.com'),
            'port'      => env('DB_TEST_PORT',     '3306'),
            'database'  => env('DB_TEST_DATABASE', 'pecsf_test'),
            'username'  => env('DB_TEST_USERNAME', 'test_db_user'),
            'password'  => env('DB_TEST_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'options'   => [],
        ];
    }

    // -------------------------------------------------------------------------
    // Masking rules
    //
    // Each key is a table name. Each value is a map of operation → columns:
    //
    //   'mask'    => ['col', ...]
    //       Replace every character in the column value with 'x'.
    //       Good for: email addresses, phone numbers, any free-text PII.
    //
    //   'replace' => ['col' => 'static value', ...]
    //       Overwrite the column with a fixed string on every row.
    //       Good for: names, addresses, user-visible identifiers.
    //
    //   'hash'    => ['col' => 'secret', ...]
    //       Store bcrypt('secret') in the column.
    //       Good for: password columns — all rows get the same known test password.
    //
    //   'uuid'    => ['col', ...]
    //       Replace with a freshly generated UUID (unique per row).
    //       Good for: GUID / external-reference columns that must stay unique.
    // -------------------------------------------------------------------------
    protected array $maskingRules = [

        'users' => [
            'mask'    => ['email'],
            'replace' => ['name' => 'Test User'],
        ],

        'employee_jobs' => [
            'mask'    => ['email'],
            'replace' => [
                'first_name' => 'FirstName',
                'last_name'  => 'LastName',
                'idir'       => 'IDIR_MASKED',
            ],
            'uuid'    => ['guid'],
        ],

        'charity_contacts' => [
            'mask'    => ['email'],
            'replace' => [
                'first_name' => 'Contact',
                'last_name'  => 'Person',
                'phone'      => '000-000-0000',
            ],
        ],

    ];

    // Tables excluded entirely from the data dump (transient / security-sensitive).
    // Only applied when $syncTables is empty (i.e. full sync).
    protected array $excludedTables = [
        'personal_access_tokens',
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_refresh_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    // Selective sync: list only the tables you want copied from prod.
    // Leave empty to sync ALL tables (minus $excludedTables above).
    // Masking rules in $maskingRules still apply to any listed table.
    protected array $syncTables = [
        // 'users',
        // 'employee_jobs',
        // 'charities',
    ];

    public function handle(): int
    {
        $this->info('=================================================');
        $this->info(' Production → Test Database Sync');
        $this->info('=================================================');
        $this->info('Started : ' . now());

        $dumpFile = $this->option('output')
            ?? storage_path('app/db-sync/prod_dump_' . now()->format('Ymd_His') . '.sql');

        if ($this->option('dry-run')) {
            $this->runDryRun($dumpFile);
            return 0;
        }

        if (!$this->confirmSync()) {
            $this->warn('Sync cancelled.');
            return 0;
        }

        try {
            $this->ensureOutputDirectory($dumpFile);

            // Step 1 — dump production with masking
            $this->info('');
            $this->info('Step 1 — Creating masked dump from production...');
            $this->registerDatabaseConnection('prod_sync', $this->prodConfig());
            $this->createMaskedDump($dumpFile);
            $this->info('  Dump written to : ' . $dumpFile);
            $this->info('  File size       : ' . $this->humanFileSize(file_exists($dumpFile) ? (int) filesize($dumpFile) : 0));

            // Step 2 — restore into test
            if (!$this->option('skip-import')) {
                $this->info('');
                $this->info('Step 2 — Importing dump into test database...');
                $this->registerDatabaseConnection('test_sync', $this->testConfig());
                $this->importDumpToTest($dumpFile);
                $this->info('  Import completed successfully.');

                if (!$this->option('keep-dump')) {
                    @unlink($dumpFile);
                    $this->info('  Dump file removed.');
                }
            }

        } catch (\Exception $e) {
            $this->error('');
            $this->error('Sync failed: ' . $e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('Finished : ' . now());
        $this->info('=================================================');

        return 0;
    }

    // -------------------------------------------------------------------------
    // Dump
    // -------------------------------------------------------------------------

    protected function createMaskedDump(string $outputFile): void
    {
        $schema = $this->buildDumpSchema();
        $schema->load();

        $sql    = $this->makeDumper($schema)->dump();
        $tables = array_keys($schema->getDumpTables());

        // Wrap INSERT statements with FK-check disable and TRUNCATE so the
        // import is idempotent against a test DB that already has data.
        $preamble  = "SET FOREIGN_KEY_CHECKS=0;\n";
        foreach ($tables as $table) {
            $preamble .= "TRUNCATE TABLE `{$table}`;\n";
        }
        $epilogue = "\nSET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($outputFile, $preamble . $sql . $epilogue);
    }

    protected function buildDumpSchema(): DumpSchema
    {
        $schema = DumpSchema::define('prod_sync');

        if (!empty($this->syncTables)) {
            $schema->include($this->syncTables);
        } else {
            $schema->exclude($this->excludedTables)->allTables();
        }

        foreach ($this->maskingRules as $tableName => $rules) {
            $schema->table($tableName, function (TableDefinition $table) use ($rules) {

                foreach ($rules['mask'] ?? [] as $column) {
                    $table->mask($column);
                }

                foreach ($rules['replace'] ?? [] as $column => $value) {
                    $table->replace($column, fn () => $value);
                }

                foreach ($rules['hash'] ?? [] as $column => $secret) {
                    $table->replace($column, fn () => bcrypt($secret));
                }

                foreach ($rules['uuid'] ?? [] as $column) {
                    $table->replace($column, fn () => \Illuminate\Support\Str::uuid()->toString());
                }

            });
        }

        return $schema;
    }

    protected function makeDumper(DumpSchema $schema): LaravelMaskedDump
    {
        return new LaravelMaskedDump($schema, $this->output);
    }

    // -------------------------------------------------------------------------
    // Import
    // -------------------------------------------------------------------------

    protected function importDumpToTest(string $dumpFile): void
    {
        $config = $this->testConfig();

        // Write a temporary options file so the password is never exposed in ps output
        $cnfFile = tempnam(sys_get_temp_dir(), 'mysql_sync_');
        file_put_contents($cnfFile, implode(PHP_EOL, [
            '[client]',
            'host='     . $config['host'],
            'port='     . $config['port'],
            'user='     . $config['username'],
            'password=' . $config['password'],
        ]));
        chmod($cnfFile, 0600);

        try {
            $command = sprintf(
                'mysql --defaults-extra-file=%s %s < %s 2>&1',
                escapeshellarg($cnfFile),
                escapeshellarg($config['database']),
                escapeshellarg($dumpFile)
            );

            exec($command, $output, $exitCode);
        } finally {
            @unlink($cnfFile);
        }

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                'mysql import failed (exit ' . $exitCode . '): ' . implode(' | ', $output)
            );
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function registerDatabaseConnection(string $name, array $config): void
    {
        Config::set("database.connections.{$name}", $config);
        DB::purge($name);
    }

    protected function ensureOutputDirectory(string $filePath): void
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    protected function confirmSync(): bool
    {
        $this->warn('');
        $this->warn('  Source : ' . $this->prodConfig()['host'] . ' / ' . $this->prodConfig()['database']);
        $this->warn('  Target : ' . $this->testConfig()['host'] . ' / ' . $this->testConfig()['database']);
        $this->warn('');
        $this->warn('  The test database will be OVERWRITTEN.');
        $this->warn('');

        return $this->confirm('Are you sure you want to continue?');
    }

    protected function runDryRun(string $dumpFile): void
    {
        $this->info('[DRY RUN] — no changes will be made');
        $this->line('');
        $this->line('  Source DB : ' . $this->prodConfig()['host'] . ' / ' . $this->prodConfig()['database']);
        $this->line('  Target DB : ' . $this->testConfig()['host'] . ' / ' . $this->testConfig()['database']);
        $this->line('  Dump file : ' . $dumpFile);

        $this->line('');
        $this->line('  Masked tables:');

        foreach ($this->maskingRules as $table => $rules) {
            $parts = [];

            foreach ($rules['mask'] ?? [] as $col) {
                $parts[] = "{$col} (mask)";
            }
            foreach (array_keys($rules['replace'] ?? []) as $col) {
                $parts[] = "{$col} (replace)";
            }
            foreach (array_keys($rules['hash'] ?? []) as $col) {
                $parts[] = "{$col} (hash)";
            }
            foreach ($rules['uuid'] ?? [] as $col) {
                $parts[] = "{$col} (uuid)";
            }

            $this->line(sprintf('    %-30s %s', $table, implode(', ', $parts)));
        }

        $this->line('');
        if (!empty($this->syncTables)) {
            $this->line('  Selective sync — only these tables will be transferred:');
            $this->line('    ' . implode(', ', $this->syncTables));
        } else {
            $this->line('  Excluded tables (no data transferred):');
            $this->line('    ' . implode(', ', $this->excludedTables));
            $this->line('');
            $this->line('  All other tables: copied verbatim.');
        }
    }

    protected function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
