<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CheckTranslation extends Command
{
    protected $signature = 'translation:check {key} {--locale=fil}';
    protected $description = 'Check translation for a given key';

    public function handle()
    {
        $key = $this->argument('key');
        $locale = $this->option('locale');
        
        $this->info("Checking translation for: $key in locale: $locale");
        
        $original = App::getLocale();
        App::setLocale($locale);
        
        $translated = __($key);
        
        App::setLocale($original);
        
        $this->info("Translation: $translated");
        
        return Command::SUCCESS;
    }
}