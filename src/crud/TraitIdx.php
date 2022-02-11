<?php
declare (strict_types=1);

/**
 * Author:
 *
 *   ┏┛ ┻━━━━━┛ ┻┓
 *   ┃　　　━　　  ┃
 *   ┃　┳┛　  ┗┳  ┃
 *   ┃　　　-　　  ┃
 *   ┗━┓　　　┏━━━┛
 *     ┃　　　┗━━━━━━━━━┓
 *     ┗━┓ ┓ ┏━━━┳ ┓ ┏━┛
 *       ┗━┻━┛   ┗━┻━┛
 * DateTime: 2021-11-25 09:52:17
 */

namespace rocket\crud;

use think\facade\Db;
use think\facade\Request;
use Exception;
use ZipArchive;

/**
 * Trait列表
 */
trait TraitIdx
{
    /**
     * 首页数据
     * @var array[]
     * create_at: 2021-12-03 11:05:52
     * update_at: 2021-12-03 11:05:52
     */
    protected $idxData = [
        'list' => [],
        'statistic' => []
    ];

    /**
     * 导出数据
     * @var null
     * create_at: 2021-12-27 15:16:06
     * update_at: 2021-12-27 15:16:06
     */
    protected $idxExport = [];

    /**
     * 自定义查询
     * @var
     * create_at: 2021-12-02 11:46:00
     * update_at: 2021-12-02 11:46:00
     */
    protected $queryCus;

    /**
     * idx 页
     * @return mixed
     * @throws Exception
     * create_at: 2021-12-02 16:06:57
     * update_at: 2021-12-02 16:06:57
     */
    public function idx()
    {
        // idx 页
        $this->main();
        // 统计
        $this->statistic();
        // 附加信息
        $this->addition();

        return $this->idxData;
    }

    /**
     * 导出
     * @return bool
     * @throws Exception
     * create_at: 2021-12-27 15:23:07
     * update_at: 2021-12-27 15:23:07
     */
    public function ept()
    {
        $this
            ->typeQuery() // 查询类型
            ->aliasQuery() // 别名
            ->customQuery() // 自定义查询
            ->queryCondi() // 查询条件
            ->groupQuery() // 分组条件
            ->orderQuery(); // 排序条件

        // 选中的id
        $checkedIds = Request::param('_checkedIds');

        if ($checkedIds) {
            if (!is_array($checkedIds)) {
                $checkedIds = explode(',', $checkedIds);
            }
            $this->queryCus->whereIn($this->pk, $checkedIds);
        }

        $this->idxExport = $this->queryCus->select();

        // 后台导出标记
        $approachBack = Request::param('_approachBack/b', false);
        if (!$approachBack) {
            // 前台导出 仅返回数据
            return $this->idxExport;
        } else {
            // 后台导出
            // 表头和接口数据映射关系
            $mapData = Request::param('_mapData');
            if (!$mapData) throw new Exception("未定义表头和接口数据映射！");
            $mapData = json_decode($mapData, true);
            $title = Request::param('_title', '导出数据');
            $suffix = Request::param('_suffix', 'xlsx');
            $this->exportProccess($this->idxExport->toArray(), $title, $mapData, $suffix);
        }
    }

    /**
     * 查找多维数组
     * @param $src
     * @param $deeps
     * create_at: 2022-01-24 13:45:56
     * update_at: 2022-01-24 13:45:56
     */
    protected function getVal($src, $deeps) {
        $needle = array_shift($deeps);
        if ($needle && $deeps) {
            return $this->getVal($src[$needle], $deeps);
        } else {
            return $src[$needle];
        }
    }

    /**
     * 服务端导出
     * @param $data
     * @param $title
     * @param $header
     * @param string $suffix
     * create_at: 2021-12-28 11:30:26
     * update_at: 2021-12-28 11:30:26
     */
    protected function exportProccess($data, $title, $header, string $suffix='xlsx')
    {
        if (!empty($data)) {
            set_time_limit(0);
            $static = '../runtime/excel/' . date('Y-m',time()) . '/';
            if (!is_dir($static)) {
                mkdir($static, 0777, true);
            }
            // 整合文件路径
            $title = $static . $title;
            // 统计总条数
            $count = count($data);
            // 每次取多少条 默认2000
            $limit = 2000; //每次只从数组取条数
            // buffer计数器
            $cnt = 0;
            $fileNameArr = array();
            // 分段执行,以免内存写满
            for ($i = 0; $i < ceil($count / $limit); $i++) {
                $fp = fopen($title . '_' . $i . '.csv', 'w'); //生成临时文件
                $fileNameArr[] = $title . '_' .  $i . '.csv';//将临时文件保存起来
                // 第一次执行时将表头写入
                if($i == 0){
                    fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));
                    fputcsv($fp, array_values($header));
                }
                // 查询出数据
                $keySort = array_keys($header);
                $pieceDatas =  array_slice($data,$i * $limit, $limit);
                foreach ($pieceDatas as $k=>$v) {
                    $tmp = array_merge(array_flip($keySort), $v);
                    foreach ($tmp as $g => $h) {
                        // 嵌套类型表头
                        if (false !== strpos($g, '.')) {
                            $parts = explode('.', $g);
                            $tmp[$g] = $this->getVal($tmp, $parts);
                        }
                        if (!in_array($g, $keySort)) {
                            unset($tmp[$g]);
                        }
                    }
                    $cnt++;
                    // 执行下一次循环之前清空缓冲区
                    if ($limit == $cnt) {
                        ob_flush();
                        flush();
                        $cnt = 0;
                    }
                    // 每行写入到临时文件
                    fputcsv($fp, array_values($tmp));
                }
                fclose($fp);  //每生成一个文件关闭
            }

            // 将所有临时文件合并成一个
            foreach ($fileNameArr as $file){
                // 如果是文件，提出文件内容，写入目标文件
                if(is_file($file)){
                    $fileName = $file;
                    // 打开临时文件
                    $handle1 = fopen($fileName,'r');
                    // 读取临时文件
                    if($str = fread($handle1, filesize($fileName))){
                        // 关闭临时文件
                        fclose($handle1);
                        // 打开或创建要合并成的文件，往末尾插入的方式添加内容并保存
                        $handle2 = fopen($title.'.csv','a+');
                        // 写入内容
                        if(fwrite($handle2, $str)){
                            // 关闭合并的文件，避免浪费资源
                            fclose($handle2);
                        }
                    }
                }
            }

            //将文件压缩，避免文件太大，下载慢
            $zip = new ZipArchive();
            $outputFilename = $title . ".zip";
            $zip->open($outputFilename, ZipArchive::CREATE);   // 打开压缩包
            $zip->addFile($title.'.csv', basename($title.'.csv'));   // 向压缩包中添加文件
            $zip->close();  //关闭压缩包

            foreach ($fileNameArr as $file) {
                @unlink($file); //删除csv临时文件
            }

            //输出压缩文件提供下载
            header("Cache-Control: max-age=0");
            header("Content-Description: File Transfer");
            header('Content-disposition: attachment; filename=' . basename($outputFilename)); // 文件名
            header("Content-Type: application/zip"); // zip格式的
            header("Content-Transfer-Encoding: binary");
            header('Content-Length: ' . filesize($outputFilename));

            @readfile($outputFilename);//输出文件;
            @unlink($title . '.csv');
            @unlink($outputFilename); //删除压缩包临时文件
            @rmdir($static); // 清除目录

            exit();
        }
    }

    /**
     * 是否返回查询sql
     * @return bool
     * create_at: 2021-12-02 14:34:10
     * update_at: 2021-12-02 14:34:10
     */
    protected function getSql()
    {
        $getSql = isset($this->attrsGetsql) && $this->attrsGetsql;
        if ($getSql) {
            $sql = $this->queryCus->fetchSql(true)->select();
            // SQL语句打印 attrsGetsql为true时
            var_dump($sql);
            exit();
        }
        $this->queryCus->fetchSql(false);

        return $this;
    }

    /**
     * 分页数据处理
     * @return $this
     * create_at: 2021-12-03 09:02:00
     * update_at: 2021-12-03 09:02:00
     */
    protected function eachQuery()
    {
        $this->queryCus->each(function ($item, $key) {
            return $this->eachHandle($item, $key);
        });

        return $this;
    }

    /**
     * 分页查询
     * @return $this
     * create_at: 2021-12-02 14:31:45
     * update_at: 2021-12-02 14:31:45
     */
    protected function pageQuery()
    {
        $pageLimit = Request::param('page_limit'); // 分页条数

        $this->queryCus = $this->queryCus->paginate($pageLimit);
        return $this;
    }

    /**
     * 排序
     * @return $this
     * create_at: 2021-12-02 14:26:20
     * update_at: 2021-12-02 14:26:20
     */
    protected function orderQuery()
    {
        if ($this->attrsOrder) {
            $this->queryCus = $this->queryCus->order($this->attrsOrder);
        }
        return $this;
    }

    /**
     * 分组
     * @return $this
     * create_at: 2021-12-02 14:18:33
     * update_at: 2021-12-02 14:18:33
     */
    protected function groupQuery()
    {
        if (isset($this->attrsGroup) && $this->attrsGroup) {
            $groupByCondi = implode(',', $this->attrsGroup);
            $this->queryCus = $this->queryCus->group($groupByCondi);
        }

        return $this;
    }

    /**
     * 查询条件
     * 支持类型
     *  - 单值 默认参数名和数据库字段名相同，按 "字段名=" 查询  eg. 'name'
     *  - 数组 ['请求参数名' => ['表.表字段', '表达式']] eg. ['school_name' => ['t.school_name', 'like']]
     * @return $this
     * @throws Exception
     * create_at: 2021-12-02 13:19:24
     * update_at: 2021-12-02 13:19:24
     */
    protected function queryCondi()
    {
        // 查询条件
        $where = [];

        if (!$this->queryTerms) {
            return $this;
        }

        // 参数列表
        $params = Request::all();
        foreach ($this->queryTerms as $k => $v) {
            if (is_array($v)) {
                $paramKey = key($v);
                $val = $v[$paramKey];
                // 数组
                if (!is_array($val)) throw new Exception("【queryTerms】{$paramKey} 配置错误！");
                if (count($val) < 2) throw new Exception('【queryTerms】数组类型至少需要2个参数[\'表字段\', \'表达式\']！');
                $tbField = $val[0]; // 查询表字段
                $expr    = $val[1]; // 表达式

                switch ($expr) {
                    case 'between':
                        if (!isset($params[$paramKey]) || !$params[$paramKey]) continue 2; // 查询请求参数中无值直接跳过
                        if (isset($params[$paramKey][0]) && $params[$paramKey][0]) {
                            $where[] = [$tbField , '>=', $params[$paramKey][0]];
                        }
                        if (isset($params[$paramKey][1]) && $params[$paramKey][1]) {
                            $where[] = [$tbField , '<=', $params[$paramKey][1]];
                        }
                        break;
                    case 'time':
                        // 开始 拼接后缀
                        $suffixStart = $paramKey. '_start';
                        // 结束 拼接后缀
                        $suffixEnd = $paramKey . '_end';
                        if (isset($params[$suffixStart]) && $params[$suffixStart]) {
                            if (!is_numeric($params[$suffixStart])) {
                                $params[$suffixStart] = strtotime(date('Y-m-d H:i:s', (int) strtotime($params[$suffixStart])));
                            } else {
                                $params[$suffixStart] = strtotime(date('Y-m-d H:i:s', (int) $params[$suffixStart]));
                            }
                            $where[] = [$tbField , '>=', $params[$suffixStart]];
                        }
                        if (isset($params[$suffixEnd]) && $params[$suffixEnd]) {
                            if (!is_numeric($params[$suffixEnd])) {
                                $params[$suffixEnd] = strtotime(date('Y-m-d H:i:s', (int) strtotime($params[$suffixEnd])));
                            } else {
                                $params[$suffixEnd] = strtotime(date('Y-m-d H:i:s', (int) $params[$suffixEnd]));
                            }
                            $where[] = [$tbField , '<=', $params[$suffixEnd]];
                        }
                        break;
                    case 'date':
                        // 开始 拼接后缀
                        $suffixStart = $paramKey. '_start';
                        // 结束 拼接后缀
                        $suffixEnd = $paramKey . '_end';
                        if (isset($params[$suffixStart]) && $params[$suffixStart]) {
                            if (!is_numeric($params[$suffixStart])) {
                                $params[$suffixStart] = strtotime(date('Y-m-d 0:0:0', (int) strtotime($params[$suffixStart])));
                            } else {
                                $params[$suffixStart] = strtotime(date('Y-m-d 0:0:0', (int) $params[$suffixStart]));
                            }
                            $where[] = [$tbField , '>=', $params[$suffixStart]];
                        }
                        if (isset($params[$suffixEnd]) && $params[$suffixEnd]) {
                            if (!is_numeric($params[$suffixEnd])) {
                                $params[$suffixEnd] = strtotime(date('Y-m-d 23:59:59', (int) strtotime($params[$suffixEnd])));
                            } else {
                                $params[$suffixEnd] = strtotime(date('Y-m-d 23:59:59', (int) $params[$suffixEnd]));
                            }
                            $where[] = [$tbField , '<=', $params[$suffixEnd]];
                        }
                        break;
                    case 'like':
                        if (!isset($params[$paramKey]) || !$params[$paramKey]) continue 2; // 查询请求参数中无值直接跳过
                        $where[] = [$tbField , 'like', "%{$params[$paramKey]}%"];
                        break;
                    default:
                        if (!isset($params[$paramKey]) || !$params[$paramKey]) continue 2; // 查询请求参数中无值直接跳过
                        if ('null_val' == $params[$paramKey]) {
                            // 特殊值比较 判断 null
                            $where[] = [$tbField, 'EXP', Db::raw('IS NULL')];
                        } else {
                            $where[] = [$tbField, $expr, $params[$paramKey]];
                        }
                }

            } else {
                // 单值 默认 =  eg. ['name']
                $paramKey = $v;
                if (!isset($params[$paramKey]) || !$params[$paramKey]) continue;
                $where[] = [$paramKey, '=', $params[$paramKey]];
            }
        }

        $this->queryCus = $this->fixedCondition($this->queryCus->where($where));
        $this->queryCus = $this->dataAuthFilt($this->queryCus, self::class, $this->pk);
        return $this;
    }

    /**
     * 自定义查询
     * @return $this
     * create_at: 2021-12-02 13:18:13
     * update_at: 2021-12-02 13:18:13
     */
    protected function customQuery()
    {
        $this->queryCus = $this->idxQuery($this->queryCus);
        return $this;
    }

    /**
     * 别名
     * @return $this
     * create_at: 2021-12-02 13:16:47
     * update_at: 2021-12-02 13:16:47
     */
    protected function aliasQuery()
    {
        if ($this->alias) {
            $this->queryCus = $this->queryCus->alias($this->alias);
        }

        return $this;
    }

    /**
     * 类型查询
     * @param string $type 查询类型
     * @return $this
     * @throws Exception
     * create_at: 2021-12-02 11:42:50
     * update_at: 2021-12-02 11:42:50
     */
    protected function typeQuery(string $type="list")
    {
        switch ($type) {
            case 'list':
                // 列表
                $field = $this->attrsIdx ? implode(',', $this->attrsIdx) : '*';
                break;
            case 'count':
                // 计数
                $field = "count(*) total";
                break;
            case 'sum':
                // 统计总和
                $field = $this->attrsSum ?
                    implode(',', array_map(function ($v) {
                        if (is_array($v)) {
                            $key = key($v);
                            return 'SUM(' . $key . ') AS ' . $v[$key];
                        } else {
                            return 'SUM(' . $v . ')';
                        }
                    }, $this->attrsSum)) :
                    0;
                break;
            case 'expr':
                // 自定义查询字段
                $field = $this->expr() ?: 0;
                break;
            default:
                throw new Exception('不支持的查询类型！');
        }

        $this->queryCus = self::field($field);
        return $this;
    }

    /**
     * idx 主方法
     * @return bool
     * create_at: 2021-12-06 10:49:55
     * update_at: 2021-12-06 10:49:55
     * @throws Exception
     */
    protected function main()
    {
        $this
            ->typeQuery() // 查询类型
            ->aliasQuery() // 别名
            ->customQuery() // 自定义查询
            ->queryCondi() // 查询条件
            ->groupQuery() // 分组条件
            ->orderQuery() // 排序条件
            ->getSql() // 获取 idx 查询sql
            ->pageQuery() // 分页
            ->eachQuery(); // 分页数据处理

        $this->idxData['list'] = $this->idxDataHandle($this->queryCus->toArray());
        return true;
    }

    /**
     * sum统计
     * create_at: 2021-12-03 14:51:53
     * update_at: 2021-12-03 14:51:53
     * @throws Exception
     */
    protected function statistic()
    {
        // 统计总和
        if ($this->attrsSum) {
            $sumQuery = $this->typeQuery("sum")->aliasQuery()->customQuery()->queryCondi();
            $sumData = $this->queryCus->select();
            $dataSet = [];
            if ($sumData) {
                array_walk($sumData, function ($val, $key) use (&$dataSet) {
                    foreach ($val as $m=>$n) {
                        $dataSet[$m] = $n ?: 0;
                    }
                });
            }
            $this->idxData['sum'] = $dataSet[0] ?? [];
        }
    }

    /**
     * 附加信息
     * create_at: 2021-12-03 14:58:31
     * update_at: 2021-12-03 14:58:31
     */
    protected function addition()
    {
        $this->idxData['addition'] = $this->idxAddition();
    }
}
