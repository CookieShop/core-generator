<?php
/**
 * Helper para hacer stream
 * 
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @author Ing. Eduardo Ortiz
 * 
 */
namespace Adteam\Core\Generator;

use Zend\Console\Adapter\AdapterInterface;
use ZF\Console\Route;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
use Zend\View\HelperPluginManager;

class Component
{
    protected $console;
    
    public function __invoke(Route $route, AdapterInterface $console)
    {
        //configs
        $this->console = $console;
//        $path = '/home/dev/Descargas/';
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
                    'data'=>'componente/README.md.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        ''
                    ]
                ],
                [
                    'namefile'=>'composer.json',
                    'data'=>'componente/composer.json.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>addslashes($namespace)
                    ]
                ],
                [
                    'namefile'=>'src/Module.php',
                    'data'=>'componente/Module.php.phtml',
                    'vars'=>[
                        'namepkg'=>$package,
                        'path'=>$path,
                        'namespace'=>$namespace
                    ]
                ],
                [
                    'namefile'=>'config/module.config.php',
                    'data'=>'componente/module.config.php.phtml',
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
