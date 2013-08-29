<?php
Yii::import("gcommon.cms.models.Object");
Yii::import("gcommon.cms.models.Page");
class SiteMapCommand extends CConsoleCommand {
    protected $count = 0;

    protected $domain = "http://www.1378.com";

    protected $top = "<?xml version='1.0' encoding='utf-8' ?>\n <urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
    protected $max = 100;
    protected $filename = "sitemap.xml";

    protected $path;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run($args) {
        $this->path = Yii::app()->publisher->domains['www.17w78.com'];
        $this->topSiteMap();
        $this->createSiteMap($this->path);
        $this->top .= "</urlset>";
        file_put_contents($this->path.DIRECTORY_SEPARATOR.$this->filename, $this->top);
    }


    /**
     * get tasks from database
     *
     * @param $num:
     *
     * @return
     */

    public function createSiteMap($path){
        $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
        while(($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
            $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
            if($file == '.' || $file == '..') {
                continue;
            } else if(is_dir($sub_dir)) {    //如果是目录,进行递归
                // echo 'Directory ' . $file . ':\n';
                $this->createSiteMap($sub_dir);
            } else {    //如果是文件,直接输出
                $this->top .= "<url>\n";
                $this->top .= "<loc>".str_replace($this->path,$this->domain,$path . DIRECTORY_SEPARATOR . $file) ."</loc>\n";
                $this->top .= "<lastmod>".date("Y-m-d",filemtime($path . DIRECTORY_SEPARATOR . $file))."</lastmod>\n";
                $this->top .= "<changefreq>always</changefreq>\n";
                $this->top .="<priority>0.5000</priority>\n";
                $this->top .="</url>\n";
            }
        } 
    }

    public function topSiteMap(){
        $this->top .= "<url>
              <loc>http://www.1378.com</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>1.0000</priority>
            </url>
            <url>
              <loc>http://www.1378.com/webgame</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>0.9000</priority>
            </url>
            <url>
              <loc>http://www.1378.com/news/index.html</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>0.5000</priority>
            </url>
            <url>
              <loc>http://www.1378.com/libao</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>0.5000</priority>
            </url>
            <url>
              <loc>http://www.1378.com/baike</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>0.5000</priority>
            </url>
            <url>
              <loc>http://www.1378.com/libao/</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>0.5000</priority>
            </url>
            <url>
              <loc>http://www.1378.com/game/</loc>
              <lastmod>".date('Y-m-d')."</lastmod>
              <changefreq>always</changefreq>
              <priority>0.5000</priority>
            </url>";
    }
}
