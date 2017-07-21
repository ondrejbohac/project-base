<?php

namespace Shopsys\ShopBundle\Model\AdminNavigation;

use JMS\TranslationBundle\Annotation\Ignore;
use Shopsys\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class MenuLoader
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \Shopsys\ShopBundle\Component\Translation\Translator $translator
     */
    public function __construct(Filesystem $filesystem, Translator $translator)
    {
        $this->filesystem = $filesystem;
        $this->translator = $translator;
    }

    /**
     * @param string $filename
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\Menu
     */
    public function loadFromYaml($filename)
    {
        $yamlParser = new Parser();

        if (!$this->filesystem->exists($filename)) {
            throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
                'File ' . $filename . ' does not exist'
            );
        }

        $menuConfiguration = new MenuConfiguration();
        $processor = new Processor();

        $inputConfig = $yamlParser->parse(file_get_contents($filename));
        $outputConfig = $processor->processConfiguration($menuConfiguration, [$inputConfig]);

        $menu = $this->loadFromArray($outputConfig);

        return $menu;
    }

    /**
     * @param array $menuItemsData
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\Menu
     */
    public function loadFromArray(array $menuItemsData)
    {
        $items = $this->loadItems($menuItemsData);
        $menu = new Menu($items);

        return $menu;
    }

    /**
     * @param array $menuItemsData
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem[]
     */
    private function loadItems(array $menuItemsData)
    {
        $items = [];

        foreach ($menuItemsData as $menuItemData) {
            $item = $this->loadItem($menuItemData);
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param array $menuItemData
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem
     */
    private function loadItem(array $menuItemData)
    {
        if (isset($menuItemData['items'])) {
            $items = $this->loadItems($menuItemData['items']);
        } else {
            $items = [];
        }

        $item = new MenuItem(
            /** @Ignore Extraction of labels in YAML file is done by \Shopsys\ShopBundle\Component\Translation\AdminMenuYamlFileExtractor */
            $this->translator->trans($menuItemData['label']),
            isset($menuItemData['type']) ? $menuItemData['type'] : null,
            isset($menuItemData['route']) ? $menuItemData['route'] : null,
            isset($menuItemData['route_parameters']) ? $menuItemData['route_parameters'] : null,
            isset($menuItemData['visible']) ? $menuItemData['visible'] : null,
            isset($menuItemData['superadmin']) ? $menuItemData['superadmin'] : null,
            isset($menuItemData['icon']) ? $menuItemData['icon'] : null,
            isset($menuItemData['multidomain_only']) ? $menuItemData['multidomain_only'] : null,
            $items
        );

        return $item;
    }
}
