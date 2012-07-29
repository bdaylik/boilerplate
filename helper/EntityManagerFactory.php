<?php

require_once dirname(__FILE__) . '/../config/application.php';

require_once DOCTRINEPATH . '/Doctrine/ORM/Tools/Setup.php';

class EntityManagerFactory
{

    static function getEntityManager()
    {

        global $connectionOptions;
        require dirname(__FILE__) . '/../config/database.php';
        Doctrine\ORM\Tools\Setup::registerAutoloadDirectory(DOCTRINEPATH);

        if (ENVIRONMENT == "development") {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }

        $config = new \Doctrine\ORM\Configuration;

        if (ENVIRONMENT == 'development') {
            // set up simple array caching for development mode
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            // set up caching with APC for production mode
            // this needs to be included in the lib and loaded.
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }

        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // set up proxy configuration
        $config->setProxyDir(APPPATH . 'model/proxies');
        $config->setProxyNamespace('proxies');
        $config->setAutoGenerateProxyClasses(false);

        //$symfonyClassLoader = new \Doctrine\Common\ClassLoader('Symfony', APPPATH . 'Doctrine');
        //$symfonyClassLoader->register();

        $entityClassLoader = new \Doctrine\Common\ClassLoader('entities', APPPATH . 'model');
        $entityClassLoader->register();

        // load the proxy entities
        $proxyClassLoader = new \Doctrine\Common\ClassLoader('proxies', APPPATH . 'model');
        $proxyClassLoader->register();

        // set up YAMIL driver
        $yamlDriver = new \Doctrine\ORM\Mapping\Driver\YamlDriver(APPPATH . 'model/mappings/');
        $config->setMetadataDriverImpl($yamlDriver);

        // create the EntityManager
        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

        return $em;
    }
}
