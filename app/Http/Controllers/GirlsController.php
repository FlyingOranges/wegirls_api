<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GirlsController extends Controller
{
    /**
     * Tag 标题栏数据抓取(10分钟刷新一次)
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags()
    {
        $data = Cache::remember('MEIZI_DATA_TAGS', 10, function () {
            return $this->interception($this->getTages('http://www.meizitu.com'));
        });

        return apiSuccess('success', $data);
    }

    /**
     * Tag 获取分类下属图组
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function imageList(Request $request)
    {
        $c = $request->get('c');
        $p = $request->get('p');

        $data = Cache::remember('MEIZI_IMAGES_LIST_PAGE_' . $p . '_URL' . $c, 10, function () use ($c, $p) {
            return $this->getImages($c, $p);
        });

        return apiSuccess('success', $data);
    }

    /**
     * Tag 传输图组内部的数据
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function image(Request $request)
    {
        $href = $request->get('c');

        $data = Cache::remember('IMAGE_INFO_URL_' . $href, 60 * 24 * 7, function () use ($href) {
            return $this->getImageInfo($href);
        });

        return apiSuccess('success', $data);
    }

    /**
     * Tag 获取图组数据
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param $url
     * @return mixed
     */
    private function getImageInfo($url)
    {
        $content = file_get_contents($url);
        $content = iconv("gb2312", "utf-8//IGNORE", $content);

        $reg = "|<div class=\"postContent\">(.*?)<\/div>|is";//正则匹配div
        preg_match_all($reg, $content, $match);

        $image_reg = '/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i'; //正则匹配图片路径src
        preg_match_all($image_reg, $match[1][0], $src_img);

        return $src_img[1];
    }

    /**
     * Tag 获取分类下属的图组
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param $url
     * @param $page
     * @return array
     */
    private function getImages($url, $page)
    {
        $content = file_get_contents($url);
        $content = iconv("gb2312", "utf-8//IGNORE", $content);

        $regex = "/<ul class=\"wp-list clearfix\".*?>.*?<\/ul>/ism";//正则匹配ul 得到图片区域信息
        preg_match_all($regex, $content, $match);

        $alt_reg = "/alt=\"(.*)\"/";
        preg_match_all($alt_reg, $match[0][0], $img_alt);

        $image_reg = '/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i'; //正则匹配图片路径src
        preg_match_all($image_reg, $match[0][0], $img_src);

        $href = "/<a[^<>]+href *\= *[\"']?(http\:\/\/[^ '\"]+)/i";
        preg_match_all($href, $match[0][0], $a_href);  //正则匹配图片href

        $imageArray = [];
        foreach ($img_src[1] as $key => $src) {
            $imageArray[$key]['thumbSrc'] = $src;
            $imageArray[$key]['href'] = $a_href[1][$key];
            $imageArray[$key]['alt'] = strip_tags($img_alt[1][$key]);
        }

        return $imageArray;
    }

    /**
     * Tag 抓取页面上的tags数据
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param string $url
     * @return array
     */
    private function getTages($url)
    {
        $content = file_get_contents($url);
        $content = iconv("gb2312", "utf-8//IGNORE", $content);

        $reg = "|<div class=\"tags\">(.*?)<\/div>|is";//正则匹配div
        preg_match_all($reg, $content, $match);

        $a = "|<a[^>]*>(.*?)<\/a>|is";
        preg_match_all($a, $match[1][0], $tages);

        $hrefArray = [];
        foreach ($tages[0] as $tag_key => $item) {
            $href = "/<a[^<>]+href *\= *[\"']?(http\:\/\/[^ '\"]+)/i";
            preg_match_all($href, $item, $xx);
            $hrefArray[$tag_key] = $xx[1][0];
        }

        $tag = [];
        foreach ($tages[1] as $key => $val) {
            $tag[$key]['title'] = $val;
            $tag[$key]['href'] = $hrefArray[$key];
        }

        return $tag;
    }

    /**
     * Tag 去掉末尾被注释的标签
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param $data
     * @param int $lastLength
     * @return array
     */
    private function interception($data, $lastLength = 19)
    {
        $count = count($data);
        $number = $count - $lastLength;

        $data = array_slice($data, 0, $number);
        return $data;
    }
}
