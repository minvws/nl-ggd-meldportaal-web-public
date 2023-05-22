<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Finder\Finder;

/*
 * This is a crude parser that will check our sources for unused or untranslated strings. It is a very rudimentary
 * check, but still good enough to give a good enough result.
 */

class CheckTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trans:check {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks code for unused/missing translations.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transKeys = $this->fetchTranslationKeys();
        $foundKeys = $this->fetchTranslationsFromSource();

        print "Note: false positives can occur due to incorrect scanning of quotes and dynamic messages\n\n";

        $unusedTranslations = array_diff($transKeys['all'], $foundKeys);
        print "Unused translation strings: \n";
        print json_encode($unusedTranslations, JSON_PRETTY_PRINT);
        print "\n";

        print "Untranslated strings: \n";
        $untranslatedTranslations = array_diff($foundKeys, $transKeys['all']);
        print json_encode($untranslatedTranslations, JSON_PRETTY_PRINT);
        print "\n";

        print "Unfinished strings: \n";
        $unfinished = $this->calcUnfinished($transKeys);
        print json_encode($unfinished, JSON_PRETTY_PRINT);


        if ($this->option('update')) {
            print "Updating translations...\n";
            $this->updateTranslations($untranslatedTranslations);
        }

        return 0;
    }

    private function calcUnfinished(array $keys): array
    {
        $max = 0;
        $ret = [];
        foreach (array_keys($keys) as $k) {
            if ($k == "all") {
                continue;
            }

            foreach ($keys[$k] as $label) {
                if (!isset($ret[$label])) {
                    $ret[$label] = 0;
                }
                $ret[$label]++;

                if ($ret[$label] > $max) {
                    $max = $ret[$label];
                }
            }
        }

        // Remove all items not equal to the max
        $ret = array_filter($ret, function ($v) use ($max) {
            return $v < $max;
        });
        return $ret;
    }


    private function fetchTranslationKeys(): array
    {
        $finder = new Finder();
        $files = $finder->files()->name("*.json")->in(App::langPath());

        $transKeys = [];
        foreach ($files as $file) {
            $locale = $file->getBasename(".json");

            $f = file_get_contents($file->getPathname());
            if (!$f) {
                continue;
            }

            $keys = json_decode($f, true);

            $transKeys['all'] = array_merge($transKeys['all'] ?? [], array_keys($keys));
            $transKeys[$locale] = array_merge($transKeys[$locale] ?? [], array_keys($keys));
        }

        // Remove duplicates
        foreach ($transKeys as $k => $v) {
            $transKeys[$k] = array_unique($v);
        }

        return $transKeys;
    }

    private function fetchTranslationsFromSource(): array
    {
        $finder = new Finder();
        $files = $finder->files()
            ->name("*.php")
            ->name("*.js")
            ->in(App::basePath("app"))
            ->in(App::basePath("tests"))
            ->in(App::basePath("resources/views"))
            ->in(App::basePath("resources/js"))
            ->in(App::basePath("config"))
        ;

        print "Scanning: ";

        $foundTrans = [];
        foreach ($files as $file) {
            print ".";
            $data = file_get_contents($file->getPathName());
            if (!$data) {
                continue;
            }

            if (preg_match_all('/(?:__|trans|\@lang)\(\n?\s*(["\'])(.+)\1\n?\s*\)/U', $data, $matches)) {
                foreach ($matches[2] as $match) {
                    $foundTrans[] = $match;
                }
            }
        }
        print "\n";

        return array_unique($foundTrans);
    }

    protected function updateTranslations(array $untranslatedTranslations): void
    {
        $finder = new Finder();
        $files = $finder->files()->name("*.json")->in(App::langPath());

        foreach ($files as $file) {
            $locale = strtoupper($file->getBasename(".json"));

            $f = file_get_contents($file->getPathname());
            if (!$f) {
                continue;
            }

            $missingData = [];
            foreach ($untranslatedTranslations as $k => $v) {
                $missingData[$v] = "__" . $locale . "__" . $v;
            }

            $data = json_decode($f, true);
            $data = array_merge($data, $missingData);

            file_put_contents($file->getPathname(), json_encode($data, JSON_PRETTY_PRINT));
        }
    }
}
