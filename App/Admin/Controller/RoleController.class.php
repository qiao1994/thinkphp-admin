<?php
namespace Admin\Controller;
use Think\Controller;

class RoleController extends \Org\Util\AdminController {
    /**
     * CRUD前后置操作
     */
    public function interfaceBeforeCreate() {
    }
    public function interfaceAfterCreate($id) {
    }
    public function interfaceBeforeDelete($id) {
    }
    public function interfaceAfterDelete($id) {
    }
    public function interfaceBeforeUpdate($id) {
    }
    public function interfaceAfterUpdate($id) {
    }

    /**
     * list页面按钮接口
     * return $html
     */
    public function tableOpeationButton() {
    	return "";
    }
    public function wapTableOpeationButton() {
    	return "";
    }
    
    /**
     * 父类中已经有典型的CRUD操作并预留入口
     * 如有较大改动则覆盖一下
     */
    public function create() {
        parent::create();
    }

    public function delete() {
        parent::delete();
    }

    public function update($id=0) {
        parent::update($id);
    }

    public function list() {
        parent::list();
    }

    public function detail($id = 0) {
        parent::detail($id);
    }

    public function import() {
        parent::import();
    }

    public function export() {
        parent::export();
    }
}