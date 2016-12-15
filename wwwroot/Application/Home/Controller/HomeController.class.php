<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}

    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
    }
    /*获取栏目分类的所有信息,用于制作面包屑,关键字,网站描述等*/
    public function Crumbs($id){
        $category = D('Category')->getTree($id);
        $this->assign('info',$category); //获取指定栏目下的所有子栏目(包括当前栏目) 
    }
    /*搜索ajax 请求下拉列表*/
    public function searchs(){
        $map['title']  = array('LIKE', "%".I('post.name','')."%");
        $map['status']  = array('GT', 0);
        $data = M("Document")->field('id,title')->where($map)->select();
        $this->ajaxReturn($data);
    }
    /*搜索跳转*/
    public function searchUrl(){
        $map['title']  = I('get.name');
        $data = M("Document")->field('id')->where($map)->find();
        if($data){
            $this->redirect("Article/index", array('id' => $data['id']));
        } else {
            $this->redirect("Index/index", "", 3, "没有此文章，请重新搜索");
        }
    }

}
