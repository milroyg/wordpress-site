<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Cli;

use Staatic\WordPress\Cli\DeleteCommand;
use Staatic\WordPress\Cli\MigrateCommand;
use Staatic\WordPress\Cli\PublishCommand;
use Staatic\WordPress\Cli\RedeployCommand;
use Staatic\WordPress\Module\ModuleInterface;
use WP_CLI;

final class RegisterCommands implements ModuleInterface
{
    /**
     * @var DeleteCommand
     */
    private $deleteCommand;

    /**
     * @var MigrateCommand
     */
    private $migrateCommand;

    /**
     * @var PublishCommand
     */
    private $publishCommand;

    /**
     * @var RedeployCommand
     */
    private $redeployCommand;

    public function __construct(
        DeleteCommand $deleteCommand,
        MigrateCommand $migrateCommand,
        PublishCommand $publishCommand,
        RedeployCommand $redeployCommand
    )
    {
        $this->deleteCommand = $deleteCommand;
        $this->migrateCommand = $migrateCommand;
        $this->publishCommand = $publishCommand;
        $this->redeployCommand = $redeployCommand;
    }

    public function hooks(): void
    {
        if (!defined('WP_CLI') || !constant('WP_CLI')) {
            return;
        }
        WP_CLI::add_command('staatic delete', $this->deleteCommand);
        WP_CLI::add_command('staatic migrate', $this->migrateCommand);
        WP_CLI::add_command('staatic publish', $this->publishCommand);
        WP_CLI::add_command('staatic redeploy', $this->redeployCommand);
    }
}
