<?php

namespace App\Command;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OrderCommand extends Command
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(OrderRepository $orderRepository, EntityManagerInterface $manager)
    {
        $this->orderRepository = $orderRepository;
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:order-command')
            ->setDescription('Displays the list of all orders')
            ->setHelp('This command allows you to get the list of all orders if no id specified in options')
            ->addOption('list', 'l', InputOption::VALUE_OPTIONAL, '', false)
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, '', false)
            ->addOption('save', 's', InputOption::VALUE_OPTIONAL, '', false)
            ->addArgument('name',
                InputArgument::OPTIONAL,
                'Tap first or last name of client ?',
                false
                );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $listValue = $input->getOption('list');
        $idValue = $input->getOption('id');
        $saveValue = $input->getOption('save');
        $listOption = (null === $listValue);
        $idOption = (null === $idValue);
        $saveOption = (null === $saveValue);

        $nameValue = $input->getArgument('name');
        $nameOption = (null === $nameValue);

        $styler = new SymfonyStyle($input, $output);

        if (true === $nameOption && false === $listOption) {
            $name = $styler->ask('what is the name of clent?');
            $input->setArgument('name', $name);
        } elseif (false === $listOption
            && (true === $idOption
            || $saveOption)) {
            if ($idOption) {
                $id = $styler->ask('what is the order id ?');
                $input->setOption('id', $id);
            } elseif ($saveOption) {
                $save = $styler->ask('what is the value of status ?');
                $input->setOption('save', $save);
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listValue = $input->getOption('list');
        $idValue = $input->getOption('id');
        $saveValue = $input->getOption('save');
        $listOption = (null === $listValue);
        $formatter = $this->getHelper('formatter');

        $idOption = (null === $idValue) || (is_string($idValue));

        $nameValue = $input->getArgument('name');
        $nameOption = (null === $nameValue) || (is_string($nameValue));

        $table = new Table($output);
        $table->setHeaderTitle('Orders');
        $rows = [];

        if (true === $listOption || true === $idOption || true === $nameOption) {
            if (true === $listOption) {
                $orders = $this->repository->FindAll();
                foreach ($orders as $order) {
                    $rows[] = [
                        $order->getID(),
                        \sprintf('%s %s',
                        $order->getShipping()->getFirstName(),
                        $order->getShipping()->getLastName()
                    ),
                        $order->getCreateAt()->format('Y-m-d H:i:s'),
                        $order->getStatus(),
                    ];
                }
            } elseif (true === $idOption) {
                $order = $this->repository->find($idValue);
                if (null === $order) {
                    $errorMessages = ['Error!', 'No order foun with this id'];
                    $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
                    $output->writeln($formattedBlock);

                    return 5;
                } else {
                    if (false !== $saveValue) {
                        $order->setStatus($saveValue);
                        $this->manager->flush();
                    }
                    $rows[] = [
                        $order->getID(),
                        \sprintf('%s %s',
                        $order->getShipping()->getFirstName(),
                        $order->getShipping()->getLastName()),
                        $order->getCreateAt()->format('Y-m-d H:i:s'),
                        $order->getStatus(),
                    ];
                }
            } elseif (true === $nameOption) {
                $orders = $this->orderRepository->getOrdersByName($nameValue);
                foreach ($orders as $order) {
                    $rows[] = [
                        $order->getID(),
                        \sprintf('%s %s',
                        $order->getShipping()->getFirstName(),
                        $order->getShipping()->getLastName()
                    ),
                        $order->getCreateAt()->format('Y-m-d H:i:s'),
                        $order->getStatus(),
                    ];
                }
            }

            $table
            ->setHeaders(['ID', 'User', 'Create at', 'Status'])
            ->setRows($rows);
            $table->render();

            return 0;
        } else {
            $formattedLine = $formatter->formatSection(
                'OrderCommand',
                'This command allows you to get all orders, filtred by  by id, set the status of this order or name'
            );
            $output->writeln($formattedLine);

            return 0;
        }
    }
}
