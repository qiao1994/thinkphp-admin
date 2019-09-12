<?php
namespace Auto\Controller;
use Think\Controller;

class FlushController extends Controller {
 
    public function index() {
        //menu信息初始化
        /*
        $menuArr = [
            'System'=>[
                'icon'=>'icon-dashboard',
                'action'=>[
                    'index'=>[
                        'title'=>'系统概况',
                        'href'=>U('/Admin/System/index'),
                    ],
                    'update'=>[
                        'title'=>'系统设置',
                        'href'=>U('/Admin/System/update', ['id'=>1]),
                    ],
                ],
            ],
            'Action'=>[],
            'Role'=>[],
            'Author'=>[],
            'User'=>[],
            'Cate'=>[],
            'Tag'=>[],
            'Book'=>[],
        ];
        D('System')->where(['id'=>1])->data(['menu'=>json_encode($menuArr)])->save();
         */

        //menu配置说明
        /*
        'default'=>[
            'model'=>'Default', //缺省则认为和key同名的model
            'title'=>'标题(当model中无信息时使用)',
            'icon'=>'icon-setting(model中无信息时使用)',
            'action'=>[
                'list'=>[
                    'title'=>'默认管理',
                    'href'=>U('default/list'),
                ],  
                'update'=>[
                    'title'=>'默认新增',
                    'href'=>U('default/create'),
                ],  
            ],  
        ],
         */



        $modules = ['Home', 'Admin', 'Common'];
        $controllers = $this->getController($modules);
        $actions = $this->getAction($controllers);
        foreach ($actions as $action) {
            $where = $action;
            unset($where['remark']);
            unset($res);
            $res = D('Action')->where($where)->find();
            if (!$res) {
                $newId = D('Action')->cadd($action);
                $hadIds[] = $newId;
            } else {
                $hadIds[] = $res['id'];
            }
        }
        D('Action')->where(['id'=>['NOT IN', $hadIds]])->delete();
        //提交事务
    }
    public function getAction($controllers) {
        $resultAcions = [];
        $filterArr = [
            'list',
            'create',
            'delete',
            'update',
            'import',
            'export',
            'detail',
            '__construct',
            'interfaceBeforeCreate',
            'interfaceAfterCreate',
            'interfaceBeforeDelete',
            'interfaceAfterDelete',
            'interfaceBeforeUpdate',
            'interfaceAfterUpdate',
            'tableOpeationButton',
            'wapTableOpeationButton',
        ];
        foreach ($controllers as $controller) {
            //从文件中读取action
            unset($content);
            $content = file_get_contents($controller['file']);
            preg_match_all("/.*?public.*?function(.*?)\(.*?\)/i", $content, $matches);
            unset($actions);
            $actions = $matches[1];
            foreach ($actions as $key=>$action) {
                if (!in_array(trim($action), $filterArr)) {
                    $controller['action'] = trim($action);
                    $controller['remark'] = trim(explode('function ', $matches[0][$key])[1]);
                    $resultAcions[] = $controller;
                }
            }
        }
        return $resultAcions;
    }
    public function getController($modules) {
        $controllers = [];
        foreach ($modules as $module) {
            unset($paths);
            $paths[] = APP_PATH.$module.'/Controller/*Controller.class.php';
            $paths[] = APP_PATH.$module.'/Model/*Model.class.php';
            foreach ($paths as $path) { 
                unset($pathFiles);
                $pathFiles = glob($path);
                foreach ($pathFiles as $pathFile) {
                    if (!is_dir($pathFile)) {
                        if (strpos($pathFile, 'Controller.class.php') !== false) {
                            $controllerName = explode('.class.php', $pathFile)[0];
                            $controllerName = explode('Controller/', $controllerName)[1];
                        } else if (strpos($pathFile, 'Model.class.php') !== false) {
                            $controllerName = explode('.class.php', $pathFile)[0];
                            $controllerName = explode('Model/', $controllerName)[1];
                        } else {
                            continue;
                        }
                        unset($data);
                        $data = [
                            'module' => $module,
                            'controller' => $controllerName,
                            'file' => $pathFile,
                        ];
                        $controllers[] = $data;
                    }
                }
            }
        }
        return $controllers;
    }
}
