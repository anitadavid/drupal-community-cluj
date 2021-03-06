<?php

/**
 * @file
 * Contains \Drupal\Console\EventSubscriber\ValidateDependenciesListener.
 */

namespace Drupal\Console\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Console\Utils\ConfigurationManager;
use Drupal\Console\Utils\TranslatorManager;
use Drupal\Console\Style\DrupalStyle;

class ValidateExecutionListener implements EventSubscriberInterface
{
    /**
     * @var TranslatorManager
     */
    protected $translator;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * ValidateExecutionListener constructor.
     * @param TranslatorManager    $translator
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(
        TranslatorManager $translator,
        ConfigurationManager $configurationManager
    ) {
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function validateExecution(ConsoleCommandEvent $event)
    {
        /* @var Command $command */
        $command = $event->getCommand();
        /* @var DrupalStyle $io */
        $io = new DrupalStyle($event->getInput(), $event->getOutput());

        $configuration = $this->configurationManager->getConfiguration();

        $mapping = $configuration->get('application.disable.commands')?:[];
        if (array_key_exists($command->getName(), $mapping)) {
            $extra = $mapping[$command->getName()];
            $message[] = sprintf(
                $this->translator->trans('application.messages.disable.command.error'),
                $command->getName()
            );
            if ($extra) {
                $message[] =  sprintf(
                    $this->translator->trans('application.messages.disable.command.extra'),
                    $extra
                );
            }
            $io->commentBlock($message);
        }
    }

    /**
     * @{@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ConsoleEvents::COMMAND => 'validateExecution'];
    }
}
