<?php
declare(strict_types=1);

namespace Mubeyko\ChangeProductType\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Console\Cli;
use Mubeyko\ChangeProductType\Model\SimpleToBundle as SimpleToBundleModel;

class SimpleToBundle extends Command
{
    /**
     * @var SimpleToBundleModel
     */
    private SimpleToBundleModel $simpleToBundle;
    /**
     * @var State
     */
    private State $appState;

    /**
     * @param SimpleToBundleModel $simpleToBundle
     * @param State $appState
     */
    public function __construct(
        SimpleToBundleModel $simpleToBundle,
        State               $appState
    ) {
        parent::__construct();
        $this->simpleToBundle = $simpleToBundle;
        $this->appState = $appState;
    }

    public const SKU = "sku";

    /**
     * Initialization of the command.
     */
    protected function configure(): void
    {
        $this->setName('catalog:product-type:update');
        $this->setDescription('Change product type from simple to bundle');
        $this->addOption(
            self::SKU,
            null,
            InputOption::VALUE_REQUIRED,
            'Product SKU'
        );
        parent::configure();
    }

    /**
     * Change product type from Simple to Bundle
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        $sku = $input->getOption(self::SKU);
        if (empty($sku)) {
            $output->writeln('<error>Please, enter the SKU of the item that you want to change</error>');
            return Cli::RETURN_FAILURE;
        }
        $output->writeln('<comment>Provided SKU is `' . $sku . '`</comment>');
        try {
            $this->simpleToBundle->changeTypeToBundle($sku);
            $output->writeln('<info>Product type successfully changed.</info>');
        } catch (\Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
        }
        return Cli::RETURN_SUCCESS;
    }
}
