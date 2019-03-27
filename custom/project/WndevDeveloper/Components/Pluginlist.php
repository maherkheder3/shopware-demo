<?php

namespace WndevDeveloper\Components;

use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\Plugin;

/**
 * Class Pluginlist
 */
class Pluginlist
{
    const DEFAULT_PLUGINLIST_FILENAME = 'plugins.json';

    /**
     * @param $filename
     *
     * @return mixed|null
     */
    public function readFile($filename)
    {
        if (file_exists($filename)) {
            return json_decode(file_get_contents($filename), true);
        }

        return null;
    }

    /**
     * @param        $filename
     * @param string $onlyThisPlugin
     *
     * @return int
     */
    public function writeFileWithCurrentState($filename, $onlyThisPlugin = '') : int
    {
        $data = $onlyThisPlugin ? $this->readFile($filename) : $this->getCurrentState();

        if ($onlyThisPlugin) {
            $data[$onlyThisPlugin] = $this->getStateOfPlugin($onlyThisPlugin);
            ksort($data);
        }

        return $this->writeFile($filename, $data);
    }

    /**
     * @param       $filename
     * @param array $data
     *
     * @return int
     */
    public function writeFile($filename, array $data) : int
    {
        return file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * @return array
     */
    public function getCurrentState() : array
    {
        /** @var  InstallerService $pluginManager */
        $pluginManager = Shopware()
            ->Container()
            ->get('shopware_plugininstaller.plugin_manager')
        ;
        $pluginManager->refreshPluginList();

        /** @var ModelManager $em */
        $em = \Shopware()->Models();

        $repository = $em->getRepository(Plugin::class);
        $builder = $repository->createQueryBuilder('plugin');
        $builder
            ->select([
                //'plugin.namespace',
                'plugin.name',
                'plugin.active',
                'plugin.added',
                'plugin.installed',
                'plugin.updated',
                'plugin.version',
            ])
            ->andWhere('plugin.capabilityEnable = true')
            ->addOrderBy('plugin.name')
            ->addOrderBy('plugin.active', 'desc')
        ;

        $data = $builder->getQuery()
            ->getArrayResult()
        ;
        $result = [];
        foreach ($data as &$d) {
            foreach (['added', 'installed', 'updated'] as $state) {
                if (!empty($d[$state])) {
                    $d[$state] = $d[$state]->getTimestamp();
                } else {
                    $d[$state] = null;
                }
            }
            $result[$d['name']] = $d;
        }

        return $result;
    }

    /**
     * @param $pluginName
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getStateOfPlugin($pluginName) : array
    {
        /** @var  InstallerService $pluginManager */
        $pluginManager = Shopware()
            ->Container()
            ->get('shopware_plugininstaller.plugin_manager')
        ;
        $pluginManager->refreshPluginList();

        /** @var ModelManager $em */
        $em = \Shopware()->Models();

        $repository = $em->getRepository(Plugin::class);
        $builder = $repository->createQueryBuilder('plugin');
        $builder
            ->select([
                //'plugin.namespace',
                'plugin.name',
                'plugin.active',
                'plugin.added',
                'plugin.installed',
                'plugin.updated',
                'plugin.version',
            ])
            ->where('plugin.name = :name')
            ->setParameter(':name', $pluginName)
        ;

        $d = $builder->getQuery()
            ->getOneOrNullResult()
        ;

        foreach (['added', 'installed', 'updated'] as $state) {
            if (!empty($d[$state])) {
                $d[$state] = $d[$state]->getTimestamp();
            } else {
                $d[$state] = null;
            }
        }

        return $d;
    }

    /**
     * @param $filename
     *
     * @return array
     * @throws \Exception
     */
    public function compareFileWithCurrentState($filename) : array
    {
        $storedState = $this->readFile($filename);
        if (empty($storedState)) {
            return [];
        }

        $currentState = $this->getCurrentState();

        $pluginsDiff = array_diff(
            array_keys($storedState),
            array_keys($currentState)
        );
        if (!empty($pluginsDiff)) {
            throw new \RuntimeException('Plugin(s) missing, difference is: ' . implode(', ', $pluginsDiff));
        }

        $updateStatus = [];
        foreach ($storedState as $name => $state) {
            $updateStatus[$name]['name'] = $name;
            $updateStatus[$name]['reinstall'] = $this->isPluginInNeedOfReinstall(
                $storedState[$name],
                $currentState[$name]
            );
            $updateStatus[$name]['update'] = $this->isPluginInNeedOfUpdate($storedState[$name], $currentState[$name]);
            $updateStatus[$name]['disable'] = $this->isPluginInNeedOfDisable($storedState[$name], $currentState[$name]);
        }

        return $updateStatus;
    }

    /**
     * @param $pluginName
     *
     * @return bool
     * @throws \Exception
     */
    public function installPlugin($pluginName) : bool
    {
        /** @var InstallerService $pluginManager */
        $pluginManager = Shopware()
            ->Container()
            ->get('shopware_plugininstaller.plugin_manager')
        ;
        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $pluginManager->getPluginByName($pluginName);
        if ($plugin->getInstalled()) {
            $pluginManager->uninstallPlugin($plugin, !$plugin->hasCapabilitySecureUninstall());
        }
        $pluginManager->installPlugin($plugin);
        $pluginManager->activatePlugin($plugin);

        return true;
    }

    /**
     * @param $pluginName
     *
     * @return bool
     * @throws \Exception
     */
    public function updatePlugin($pluginName) : bool
    {
        /** @var InstallerService $pluginManager */
        $pluginManager = Shopware()
            ->Container()
            ->get('shopware_plugininstaller.plugin_manager')
        ;
        $plugin = $pluginManager->getPluginByName($pluginName);
        $pluginManager->updatePlugin($plugin);

        return true;
    }

    /**
     * @param $pluginName
     *
     * @return bool
     * @throws \Exception
     */
    public function disablePlugin($pluginName) : bool
    {
        /** @var InstallerService $pluginManager */
        $pluginManager = Shopware()
            ->Container()
            ->get('shopware_plugininstaller.plugin_manager')
        ;
        $plugin = $pluginManager->getPluginByName($pluginName);
        $pluginManager->deactivatePlugin($plugin);

        return true;
    }

    /**
     * @param array $storedState
     * @param array $currentState
     *
     * @return bool
     */
    protected function isPluginInNeedOfReinstall(array $storedState, array $currentState) : bool
    {
        return (
            $storedState['active'] && (
                $storedState['active'] !== $currentState['active']
                || $storedState['installed'] > $currentState['installed']
            )
        );
    }

    /**
     * @param array $storedState
     * @param array $currentState
     *
     * @return bool
     */
    protected function isPluginInNeedOfUpdate(array $storedState, array $currentState) : bool
    {
        return (
            $storedState['active']
            && $storedState['updated'] > $currentState['updated']
            && !$this->isPluginInNeedOfReinstall($storedState, $currentState)
        );
    }

    /**
     * @param array $storedState
     * @param array $currentState
     *
     * @return bool
     */
    protected function isPluginInNeedOfDisable(array $storedState, array $currentState) : bool
    {
        return (!$storedState['active'] && $storedState['active'] !== $currentState['active']);
    }
}
