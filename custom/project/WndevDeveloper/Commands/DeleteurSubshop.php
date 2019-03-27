<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Shopware\Components\Api\Manager;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Order\Order;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteurSubshopCommand
 *
 * @package WndevDeveloper\Commands
 */
class DeleteurSubshop extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:deleteur:subshop')
            ->setDescription('Delete subshop with orders and users. WARNING: Do a backup before executing this function!')
            ->addArgument(
                'subshopID',
                InputArgument::REQUIRED,
                'The DB id of the subshop to be deleted'
            )
            ->setHelp('The <info>%command.name%</info> installs plugins from pluginlist file.')
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subshopID = $input->getArgument('subshopID');
        $output->writeln('Deleting subshop with ID ' . $subshopID);

        $this->deleteCustomersForSubshop($subshopID, $output);
        $this->deleteOrdersForSubshop($subshopID, $output);
        $this->deleteSubshop($subshopID, $output);
        $output->writeln('Done');
    }

    /**
     * @param                 $subshopID
     * @param OutputInterface $output
     */
    private function deleteCustomersForSubshop($subshopID, OutputInterface $output)
    {
        $output->writeln('> Deleting users for subshop with ID ' . $subshopID);
        /** @var \Shopware\Components\Api\Resource\Customer $customerResource */
        $customerResource = Manager::getResource('customer');

        /** @var \Shopware\Models\Customer\Repository $customerRepository */
        $customerRepository = $customerResource->getRepository();

        $customers = $customerRepository->findBy(['shopId' => $subshopID]);
        $output->writeln('> Found customers: ' . count($customers));

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            Shopware()->Models()->remove($customer);
        }
        try {
            $output->writeln('> Customers deleted');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * @param                 $subshopID
     * @param OutputInterface $output
     */
    private function deleteOrdersForSubshop($subshopID, OutputInterface $output)
    {
        $output->writeln('> Deleting orders for subshop with ID ' . $subshopID);
        /** @var \Shopware\Components\Api\Resource\Order $orderResource */
        $orderResource = Manager::getResource('order');

        /** @var \Shopware\Models\Order\Repository $orderRepository */
        $orderRepository = $orderResource->getRepository();

        $orders = $orderRepository->findBy(['shopId' => $subshopID]);
        $output->writeln('> Found orders: ' . count($orders));

        /** @var Order $order */
        foreach ($orders as $order) {
            Shopware()->Models()->remove($order);
        }
        try {
            Shopware()->Models()->flush();
            $output->writeln('> Orders deleted');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * @param                 $subshopID
     * @param OutputInterface $output
     */
    private function deleteSubshop($subshopID, OutputInterface $output)
    {
        $output->writeln('> Deleting subshop with ID ' . $subshopID);
        /** @var \Shopware\Components\Api\Resource\Shop $shopResource */
        $shopResource = Manager::getResource('shop');

        /** @var \Shopware\Models\Order\Repository $shopRepository */
        $shopRepository = $shopResource->getRepository();

        /** @var Order $shop */
        $shop = $shopRepository->find($subshopID);

        if (!$shop) {
            $output->writeln('> No subshop found');
            return;
        }

        Shopware()->Models()->remove($shop);

        try {
            Shopware()->Models()->flush();
            $output->writeln('> Shop deleted');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
