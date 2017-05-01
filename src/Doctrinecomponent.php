<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Generator;

/**
 * Description of Clicomponentdoctrine
 *
 * @author dev
 */
use Zend\Console\Adapter\AdapterInterface;
use ZF\Console\Route;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
use Zend\View\HelperPluginManager;

class Doctrinecomponent
{
    protected $console;
    
    public function __invoke(Route $route, AdapterInterface $console)
    {
        //configs
        $this->console = $console;
        $path = $route->getMatchedParam('repo');
        $package = $route->getMatchedParam('package');
        $namespace = $route->getMatchedParam('namespace');               
        $this->create($path, $package,$namespace);
    }
    

    /**
     * 
     * @param type $path
     * @param type $package
     */
    public function create($path,$package,$namespace)
    {
        if($dir = $this->getRootpkg($package,$path)){
            $params = $this->getTemplates($package,$path,$namespace);
            foreach ($params as $param){
                $this->console->write("Generate {$param['namefile']} ... \n"); 
                $data = $this->getContent($param['vars'], $param['data']);
                $this->createTemplate($param,$data);
            }
        }
        
    } 
    
    private function getTemplates($package,$path,$namespace)
    {
        return [
                [
                    'namefile'=>'README.md',
                    'data'=>'componente/doctrine/README.md.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        ''
                    ]
                ],
                [
                    'namefile'=>'composer.json',
                    'data'=>'componente/doctrine/composer.json.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace)
                    ]
                ],
                [
                    'namefile'=>'src/Module.php',
                    'data'=>'componente/doctrine/Module.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ],
                [
                    'namefile'=>'config/module.config.php',
                    'data'=>'componente/doctrine/module.config.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ],
                [
                    'namefile'=>'src/Component.php',
                    'data'=>'componente/doctrine/Component.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ],
                [
                    'namefile'=>'src/Entity/ZipCodes.php',
                    'data'=>'componente/doctrine/ZipCodes.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ],
                [
                    'namefile'=>'src/Repository/ZipCodesRepository.php',
                    'data'=>'componente/doctrine/ZipCodesRepository.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ]             
            ];
    }        

    private function createTemplate($params,$data)
    {
        $package = $params['vars']['namepkg'];
        $path = $params['vars']['path'];
        if($dir = $this->getRootpkg($package,$path)){
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
    private function getRootpkg($package,$path)
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
                mkdir($dirchild.'/src/Entity');
                mkdir($dirchild.'/src/Repository');
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
