<?php

namespace WndevDeveloper\Components;

use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;
use Shopware\Models\Config\Value;
use Shopware\Models\Plugin\Plugin;

/**
 * Class PluginConfig
 *
 * @package WndevDeveloper\Components
 */
class PluginConfig
{
    private $repository;

    /**
     * PluginConfig constructor.
     *
     * @param ModelRepository $repository
     */
    public function __construct(ModelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the config state of given plugin
     * If plugin name is null all plugin configs are gathered
     *
     * @param null|string $pluginName
     *
     * @return array
     */
    public function getState($pluginName = null) : array
    {
        $builder = $this->repository->createQueryBuilder('p');

        $builder->select(['p, f, e, v']);

        $builder->leftJoin('p.configForms', 'f');
        $builder->leftJoin('f.elements', 'e');
        $builder->leftJoin('e.values', 'v');

        $builder->where($builder->expr()
            ->andX(
                $builder->expr()
                    ->eq('p.active', true),
                $builder->expr()
                    ->orX(
                        $builder->expr()
                            ->eq('p.source', $builder->expr()
                                ->literal('Community')),
                        $builder->expr()
                            ->eq('p.source', $builder->expr()
                                ->literal('Local'))
                    )
            ));

        if ($pluginName) {
            $builder->andWhere($builder->expr()
                ->eq('p.name', $builder->expr()
                    ->literal($pluginName)));
        }

        $plugins = $builder->getQuery()
            ->getResult()
        ;

        $payload = [];

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $forms = $plugin->getConfigForms();
            /** @var Form $form */
            foreach ($forms as $form) {
                $elements = $form->getElements();
                /** @var Element $element */
                foreach ($elements as $element) {
                    $values = $element->getValues();
                    /** @var Value $value */
                    foreach ($values as $value) {
                        $payload[$plugin->getName()][$form->getName()][$element->getName()][$value->getShop()
                            ->getId()] = $value->getValue();
                    }
                }
            }
        }

        return $payload;
    }

    /**
     * Restores the config state from given plugin config
     *
     * @param $config
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setState($config)
    {
        $builder = $this->repository->createQueryBuilder('p');
        $entityManager = $builder->getEntityManager();

        foreach ($config as $pluginName => $pluginConfig) {
            /** @var Plugin $plugin */
            $plugin = $this->repository->findOneBy(['name' => $pluginName]);

            if (!$plugin) {
                echo sprintf('Plugin "%s" not found.' . PHP_EOL, $pluginName);
                continue;
            }

            $forms = $plugin->getConfigForms();
            /** @var Form $form */
            foreach ($forms as $form) {
                $elements = $form->getElements();
                /** @var Element $element */
                foreach ($elements as $element) {
                    $values = $element->getValues();
                    /** @var Value $value */
                    foreach ($values as $value) {
                        $shopId = $value->getShop()
                            ->getId()
                        ;

                        if (!array_key_exists($form->getName(), $pluginConfig)
                            || !array_key_exists($element->getName(), $pluginConfig[$form->getName()])
                            || !array_key_exists($shopId, $pluginConfig[$form->getName()][$element->getName()])
                        ) {
                            continue;
                        }

                        $left = $pluginConfig[$form->getName()][$element->getName()][$shopId];
                        $right = $value->getValue();

                        if ($left !== $right) {
                            if (\is_array($left)) {
                                $left = implode(',', $left);
                            }

                            if (\is_array($right)) {
                                $right = implode(',', $right);
                            }

                            echo sprintf(
                                '%s:%s:%s: %s => %s' . PHP_EOL,
                                $plugin->getName(),
                                $element->getName(),
                                $shopId,
                                $right,
                                $left
                            );

                            $value->setValue($pluginConfig[$form->getName()][$element->getName()][$shopId]);
                            $entityManager->persist($value);
                        }

                        unset($pluginConfig[$form->getName()][$element->getName()][$shopId]);

                        if (!\count($pluginConfig[$form->getName()][$element->getName()])) {
                            unset($pluginConfig[$form->getName()][$element->getName()]);
                        }

                        if (!\count($pluginConfig[$form->getName()])) {
                            unset($pluginConfig[$form->getName()]);
                        }
                    }
                }
            }
        }

        // @TODO leftover values are not mapped til now, we have to create the entries manually

        $entityManager->flush();
    }
}
