<?php
/*
 * This file is a part of the CKFinder bundle for Symfony.
 *
 * Copyright (C) 2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * Licensed under the terms of the MIT license.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace CKSource\Bundle\CKFinderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more, see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ckfinder');
        $rootNode = $this->getRootNode($treeBuilder, 'ckfinder');
        $this->addGlobalOptions($rootNode);
        $this->addBackendNode($rootNode);
        $this->addImageNode($rootNode);
        $this->addACLNode($rootNode);
        $this->addConnectorNode($rootNode);
        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addGlobalOptions(ArrayNodeDefinition $rootNode) {
        $rootNode
            ->children()
                ->scalarNode('defaultResourceTypes')
                    ->defaultValue('')
                    ->info('Please check http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_resourceTypes for configuration.')
                ->end()
                ->scalarNode('licenseName')->defaultValue('')->end()
                ->scalarNode('licenseKey')->defaultValue('')->end()
                ->arrayNode('privateDir')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('backend')->defaultValue('default')->end()
                        ->scalarNode('tags')->defaultValue('.ckfinder/tags')->end()
                        ->scalarNode('logs')->defaultValue('.ckfinder/logs')->end()
                        ->scalarNode('cache')->defaultValue('.ckfinder/cache')->end()
                        ->scalarNode('thumbs')->defaultValue('.ckfinder/cache/thumbs')->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addBackendNode(ArrayNodeDefinition $rootNode){
        $rootNode
            ->children()
                ->arrayNode('backends')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('adapter')->end()
                            ->scalarNode('root')->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('default')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('default')->end()
                                ->scalarNode('adapter')->defaultValue('local')->end()
                                ->scalarNode('baseUrl')->defaultValue('/userfiles/')->end()
                                ->scalarNode('root')->defaultValue('%kernel.project_dir%/../public/userfiles')->end()
                                ->scalarNode('chmodFiles')->defaultValue('0777')->end()
                                ->scalarNode('chmodFolders')->defaultValue('0755')->end()
                                ->scalarNode('filesystemEncoding')->defaultValue('UTF-8')->end()
                            ->end()
                        ->end()
                        ->arrayNode('symfony_cache')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('symfony_cache')->end()
                                ->scalarNode('adapter')->defaultValue('local')->end()
                                ->scalarNode('root')->defaultValue('%kernel.cache_dir%')->end()
                            ->end()
                        ->end()
                        ->arrayNode('symfony_logs')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('default')->end()
                                ->scalarNode('adapter')->defaultValue('local')->end()
                                ->scalarNode('root')->defaultValue('%kernel.logs_dir%')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addImageNode(ArrayNodeDefinition $rootNode){

        $rootNode
            ->children()
                ->arrayNode('images')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('maxWidth')->defaultValue(1600)->end()
                        ->integerNode('maxHeight')->defaultValue(1200)->end()
                        ->integerNode('quality')->defaultValue(80)->end()
                        ->arrayNode('sizes')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('width')->end()
                                    ->scalarNode('height')->end()
                                    ->scalarNode('quality')->end()
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('small')
                                    ->children()
                                        ->integerNode('width')->defaultValue(480)->end()
                                        ->integerNode('height')->defaultValue(320)->end()
                                        ->integerNode('quality')->defaultValue(80)->end()
                                    ->end()
                                ->end()
                                ->arrayNode('medium')
                                    ->children()
                                        ->integerNode('width')->defaultValue(600)->end()
                                        ->integerNode('height')->defaultValue(480)->end()
                                        ->integerNode('quality')->defaultValue(80)->end()
                                    ->end()
                                ->end()
                                ->arrayNode('large')
                                    ->children()
                                        ->integerNode('width')->defaultValue(800)->end()
                                        ->integerNode('height')->defaultValue(600)->end()
                                        ->integerNode('quality')->defaultValue(80)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('secureImageUploads')->defaultTrue()->end()
            ->end();
    }



    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addConnectorNode(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('resourceTypes')
                    ->performNoDeepMerging()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('backend')->isRequired()->end()
                            ->scalarNode('label')->end()
                            ->scalarNode('directory')->end()
                            ->scalarNode('allowedExtensions')->end()
                            ->scalarNode('deniedExtensions')->end()
                            ->scalarNode('maxSize')->end()
                            ->booleanNode('lazyLoad')->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('overwriteOnUpload')->defaultFalse()->end()
                ->booleanNode('checkDoubleExtension')->defaultTrue()->end()
                ->booleanNode('disallowUnsafeCharacters')->defaultFalse()->end()
                ->booleanNode('checkSizeAfterScaling')->defaultTrue()->end()
                ->arrayNode('htmlExtensions')
                    ->scalarPrototype()->end()
                    ->defaultValue(array('html', 'htm', 'xml', 'js'))
                ->end()
                ->arrayNode('hideFolders')
                    ->scalarPrototype()->end()
                    ->defaultValue(array('.*', 'CVS', '__thumbs'))
                ->end()
                ->arrayNode('hideFiles')
                    ->scalarPrototype()->end()
                    ->defaultValue(array('.*'))
                ->end()
                ->booleanNode('forceAscii')->defaultFalse()->end()
                ->booleanNode('xSendfile')->defaultFalse()->end()
                ->booleanNode('debug')->defaultFalse()->end()
                ->arrayNode('debugLoggers')
                    ->scalarPrototype()->end()
                    ->defaultValue(array('ckfinder_log', 'error_log', 'firephp'))
                ->end()
                ->arrayNode('plugins')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('cache')
                    ->children()
                        ->integerNode('imagePreview')->defaultValue(24 * 3600)->end()
                        ->integerNode('thumbnails')->defaultValue(24 * 3600 * 365)->end()
                        ->integerNode('proxyCommand')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->scalarNode('tempDirectory')->defaultValue('')->end()
                ->booleanNode('sessionWriteClose')->defaultTrue()->end()
                ->booleanNode('csrfProtection')->defaultTrue()->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addACLNode(ArrayNodeDefinition $rootNode){
        $rootNode
            ->children()
                ->scalarNode('roleSessionVar')->defaultValue('')->end()
                ->arrayNode('accessControl')
                    ->children()
                        ->scalarNode('role')->isRequired()->end()
                        ->scalarNode('resourceType')->isRequired()->end()
                        ->scalarNode('folder')->isRequired()->end()
                        ->booleanNode('FOLDER_VIEW')->defaultTrue()->end()
                        ->booleanNode('FOLDER_CREATE')->defaultTrue()->end()
                        ->booleanNode('FOLDER_RENAME')->defaultTrue()->end()
                        ->booleanNode('FOLDER_DELETE')->defaultTrue()->end()
                        ->booleanNode('FILE_VIEW')->defaultTrue()->end()
                        ->booleanNode('FILE_UPLOAD')->defaultTrue()->end()
                        ->booleanNode('FILE_RENAME')->defaultTrue()->end()
                        ->booleanNode('FILE_DELETE')->defaultTrue()->end()
                        ->booleanNode('IMAGE_RESIZE')->defaultTrue()->end()
                        ->booleanNode('IMAGE_RESIZE_CUSTOM')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();
    }


    /**
     * @param TreeBuilder $treeBuilder
     * @param $name
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getRootNode(TreeBuilder $treeBuilder, $name)
    {
        // BC layer for symfony/config 4.1 and older
        if (!method_exists($treeBuilder, 'getRootNode')) {
            return $treeBuilder->root($name);
        }

        return $treeBuilder->getRootNode();
    }
}
