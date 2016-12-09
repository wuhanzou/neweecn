<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;
/**
 * 文档模型标签库
 */
class Article extends TagLib{
    /**
     * 定义标签列表
     * @var array
     */
    protected $tags   =  array(
        'partlist' => array('attr' => 'id,field,page,type,count,name', 'close' => 1), //获取列表信息。根据pid=id，type(1目录,2主题,3段落),还可以根据当前的$_GET['p']分页获取lime条数据,$count每页显示多少条,listrow是否获取列表全部内容详情，默认true,不获取全部信息，只获取列表信息
        'partpage' => array('attr' => 'id,listrow', 'close' => 0), //文章数据分页,需要指定type类型
        'prev'     => array('attr' => 'name,info', 'close' => 1), //获取上一篇文章信息
        'next'     => array('attr' => 'name,info', 'close' => 1), //获取下一篇文章信息
        'page'     => array('attr' => 'cate,listrow', 'close' => 0), //列表分页,不需要指定type类型
        'position' => array('attr' => 'pos,cate,limit,filed,name', 'close' => 1), //获取推荐位列表
        'list'     => array('attr' => 'name,category,child,page,row,field', 'close' => 1), //获取指定分类列表
    );

    public function _list($tag, $content){
        $name   = $tag['name'];
        $cate   = $tag['category'];
        $child  = empty($tag['child']) ? 'child' : $tag['child'];
        $row    = empty($tag['row'])   ? '10' : $tag['row'];
        $field  = empty($tag['field']) ? 'true' : $tag['field'];

        $parse  = '<?php ';
        $parse .= '$__CATE__ = D(\'Category\')->getChildrenId('.$cate.','.$child.');';
        $parse .= '$__LIST__ = D(\'Document\')->page(!empty($_GET["p"])?$_GET["p"]:1,'.$row.')->lists(';
        $parse .= '$__CATE__, \'`level` DESC,`id` DESC\', 1,';
        $parse .= $field . ');';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /**
     * 推荐位列表
     * @param  string  $name      
     * @param  number  $pos      推荐位 1-列表推荐，2-频道页推荐，4-首页推荐
     * @param  number  $cate    $cate=$category_id分类ID
     * @param  number  $limit    列表显示行数
     * @param  boolean $filed    查询字段,true显示查询所有字段，也可指定相应字段
     */
    public function _position($tag, $content){
        $pos    = $tag['pos'];
        $cate   = $tag['cate'];
        $limit  = empty($tag['limit']) ? 'null' : $tag['limit'];
        $field  = empty($tag['field']) ? 'true' : $tag['field'];
        $name   = $tag['name'];
        $parse  = '<?php ';
        $parse .= '$__POSLIST__ = D(\'Document\')->position(';
        $parse .= $pos . ',';
        $parse .= $cate . ',';
        $parse .= $limit . ',';
        $parse .= $field . ');';
        $parse .= ' ?>';
        $parse .= '<volist name="__POSLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* *
    * 列表数据分页 
    * @param  number  $cate   $cate=category_id分类id,这里不需要指定type类型
    * @param  number $listrow  每页显示条数
    */
    public function _page($tag){
        $cate    = $tag['cate'];
        $listrow = $tag['listrow'];
        $parse   = '<?php ';
        $parse  .= '$__PAGE__ = new \Think\Page(get_list_count(' . $cate . '), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    /** 
        *获取下一篇文章信息
        * @param  string $name 
        * @param  array $info 当前文档信息
    */
    public function _next($tag, $content){
        $name   = $tag['name'];
        $info   = $tag['info'];
        $parse  = '<?php ';
        $parse .= '$' . $name . ' = D(\'Document\')->next(' . $info . ');';
        $parse .= ' ?>';
        $parse .= '<notempty name="' . $name . '">';
        $parse .= $content;
        $parse .= '</notempty>';
        return $parse;
    }

    /** 
        *获取上一篇文章信息 
        * @param  string $name 
        * @param  array $info 当前文档信息
    */
    public function _prev($tag, $content){
        $name   = $tag['name'];
        $info   = $tag['info'];
        $parse  = '<?php ';
        $parse .= '$' . $name . ' = D(\'Document\')->prev(' . $info . ');';
        $parse .= ' ?>';
        $parse .= '<notempty name="' . $name . '">';
        $parse .= $content;
        $parse .= '</notempty>';
        return $parse;
    }

    /**
     * 文章数据分页
     * @param  number $id       文章所属id, pid=id
     * @param  number $type     文章类型(1目录,2主题,3段落)
     * @param  number $listrow  每页显示条数
    */
    public function _partpage($tag){
        $id      = $tag['id'];
        if ( isset($tag['type']) ) {
            $type = $tag['type'];
        }else{
            $type = 3;
        }
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse   = '<?php';
        $parse  .= ' $__PAGE__ = new \Think\Page(get_part_count('.$id.','.$type.'),'.$listrow.');';
        $parse  .= ' echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    //获取列表信息。根据pid=id，type(1目录,2主题,3段落),还可以根据当前的$_GET['p']分页获取lime条数据,$count每页显示多少条,listrow是否获取列表全部内容详情，默认true,不获取全部信息，只获取列表信息
    public function _partlist($tag, $content){
        $id     = $tag['id'];
        $field  = $tag['field'];
        $name   = $tag['name'];
        if ( isset($tag['type']) ) {
            $type = $tag['type'];
        }else{
            $type = 3;
        }
        if ( isset($tag['count']) ) {
            $count = $tag['count'];
        }else{
            $count = 10;
        }
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = true;
        }
        $parse  = '<?php ';
        $parse .= '$__PARTLIST__ = D(\'Document\')->part(' . $id . ',  !empty($_GET["p"])?$_GET["p"]:1,' . $type . ',' . $count . ', ' . $field . ','. $listrow .');';
        $parse .= '?>';
        $parse .= '<?php $page=(!empty($_GET["p"])?$_GET["p"]:1)-1; ?>';
        $parse .= '<volist name="__PARTLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}
