<?php

namespace App\Commands;

use App\Facades\TeamSpeak3;
use App\Providers\TeamspeakListenerServiceProvider;
use App\Services\Gateways\TeamspeakGateway;
use LaravelZero\Framework\Commands\Command;

class Run extends Command
{
    /**
     * @var string
     */
    protected $description = 'Run the bot';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run';

    public function handle(): void
    {
        $this->task('Connect to teamspeak server', function () {
            $this->newLine();
        });

        $this->task('Initialize event listeners', function () {
            $this->newLine();

            foreach ($this->app->tagged(TeamspeakListenerServiceProvider::TAG_NAME) as $listener) {
                $listener->init();
            }
        });

        $this->task('Joining default channel', function () {
            $this->newLine();
            if (is_numeric(config('teamspeak.default_channel'))) {
                return TeamspeakGateway::moveClient((int) config('teamspeak.default_channel'));
            }

            return true;
        });

        $this->task('Listen for events', function () {
            $this->newLine();
            $this->listenToEvents();
        });
    }

    public function listenToEvents(): void
    {
        /** @phpstan-ignore-next-line */ // Intended behavior, application loop
        while (1) {
            TeamSpeak3::getAdapter()->wait();
        }
    }
}
