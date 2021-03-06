<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class ArticleController extends HomeController {

	//系统首页
    public function index(){
         /* 当前文档信息 */
         $map['id'] = I('get.id');
         $map['status']  = array('GT', 0);
        $data = M('Document') ->field(true)->where($map)->find();   //当前文档信息
        if($data){
            M('Document') ->where('id='.$map['id'])->setInc('view',1); // 文章阅读数加1
            $category = get_parent_category( $data['category_id'] );
            $Categories =  end($category);  
            $this-> assign('Categories', $Categories); //获取最后一个元素，赋值当前文章的上级目录
            $this->assign('info',$category);  //获取子栏目的所有父级栏目
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('该文章已被禁用或者删除');
        }
        
    }

}