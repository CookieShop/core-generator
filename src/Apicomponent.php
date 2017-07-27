<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Generator;

/**
 * Description of Apicomponent
 *
 * @author dev
 */

use Zend\Console\Adapter\AdapterInterface;
use ZF\Console\Route;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
use Zend\View\HelperPluginManager;

class Apicomponent 
{
    protected $console;
    
    public function __invoke(Route $route, AdapterInterface $console)
    {
        //configs
        $this->console = $console;
        $path = $route->getMatchedParam('repo');
        $package = $route->getMatchedParam('package');
        $namespace = $route->getMatchedParam('namespace');
        $service = $route->getMatchedParam('service'); 
        $this->create($path, $package,$namespace,$service);
    }
    

    /**
     * 
     * @param type $path
     * @param type $package
     */
    public function create($path,$package,$namespace,$service)
    {
        if($dir = $this->getRootpkg($package,$path,$namespace,$service)){
            $params = $this->getTemplates($package,$path,$namespace,$service);
            foreach ($params as $param){
                $this->console->write("Generate {$param['namefile']} ... \n"); 
                $data = $this->getContent($param['vars'], $param['data']);
                $this->createTemplate($param,$data,$namespace,$service);
            }
        }
        
    } 
    
    private function getTemplates($package,$path,$namespace,$service)
    {
        return [
                [
                    'namefile'=>'README.md',
                    'data'=>'componente/api/README.md.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path
                    ]
                ],
                [
                    'namefile'=>'composer.json',
                    'data'=>'componente/api/composer.json.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace)
                    ]
                ],
                [
                    'namefile'=>'Module.php',
                    'data'=>'componente/api/Module.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ],
                [
                    'namefile'=>'config/module.config.php',
                    'data'=>'componente/api/module.config.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace),
                        'service'=> ucwords(strtolower($service))
                    ]
                ],    
                [
                    'namefile'=>'src/'.$namespace.'/Module.php',
                    'data'=>'componente/api/src/Module.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace)
                    ]
                ], 
                [
                    'namefile'=>'/src/'.$namespace.'/V1/Rest/'.$service.'/'.$service.'Collection.php',
                    'data'=>'componente/api/src/Collection.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace),
                        'service'=> ucwords(strtolower($service))
                    ]
                ],    
                [
                    'namefile'=>'/src/'.$namespace.'/V1/Rest/'.$service.'/'.$service.'Entity.php',
                    'data'=>'componente/api/src/Entity.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace),
                        'service'=> ucwords(strtolower($service))
                    ]
                ],   
                [
                    'namefile'=>'/src/'.$namespace.'/V1/Rest/'.$service.'/'.$service.'Resource.php',
                    'data'=>'componente/api/src/Resource.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace),
                        'service'=> ucwords(strtolower($service))
                    ]
                ],  
                [
                    'namefile'=>'/src/'.$namespace.'/V1/Rest/'.$service.'/'.$service.'ResourceFactory.php',
                    'data'=>'componente/api/src/ResourceFactory.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace),
                        'service'=> ucwords(strtolower($service))
                    ]
                ],             
            ];
    }        

    private function createTemplate($params,$data,$namespace,$service)
    {
        $package = $params['vars']['namepkg'];
        $path = $params['vars']['path'];
        if($dir = $this->getRootpkg($package,$path,$namespace,$service)){
            file_put_contents($dir.'/'.$params['namefile'], $data);
        }         
    }         

    /**
     * 
     * @param type $_vars
     * @param type $template
     * @return type
     */
    private function getContent($_vars,$template){
        $renderer = new PhpRenderer();
        $renderer->resolver()->addPath(__DIR__ . '/../data');
        $renderer->setHelperPluginManager(HelperPluginManager::class);
        $model = new ViewModel();
        $model->setVariable('items' , $_vars);
        $model->setTemplate($template);
        $textContent = $renderer->render($model);
        return $textContent;
    }    
 
    /**
     * 
     * @param type $package
     * @param type $path
     * @return string
     */
    private function getRootpkg($package,$path,$namespace,$service)
    {
        $pkgname = explode('/', $package);
        $dir = false;
        if(isset($pkgname[0])&&isset($pkgname[1])){
            $diroot = $path.$pkgname[0];
            $dirchild = $path.$pkgname[0].'/'.$pkgname[1];
            if(!file_exists($diroot)){
                mkdir($diroot);
            }
            
            if(!file_exists($dirchild)){
                mkdir($dirchild);
                mkdir($dirchild.'/src');
                mkdir($dirchild.'/config');
                mkdir($dirchild.'/src/'.$namespace);
                mkdir($dirchild.'/src/'.$namespace.'/V1');
                mkdir($dirchild.'/src/'.$namespace.'/V1/Rest');
                mkdir($dirchild.'/src/'.$namespace.'/V1/Rest/'.$service);
                
            }  
            $dir = $dirchild;
        }
        return $dir;
    }  
    
    private function mkdirIterate($pathname,$mode)
    {
	umask(0);
	is_dir(dirname($pathname)) || $this->mkdirIterate(dirname($pathname), $mode);
	return is_dir($pathname) || mkdir($pathname, $mode);        
    }
}
