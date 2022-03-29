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
 * DateTime: 2021-12-02 17:57:32
 */

namespace rocket\lifecycle;

use think\facade\Request;
use Exception;

/**
 * 生命周期
 */
trait Lifecycle
{
    /********************************************* csrf *****************************************************/

    /**
     * 校验页面token
     * @param $params
     * @return bool
     * @throws Exception
     * create_at: 2021-12-09 15:49:10
     * update_at: 2021-12-09 15:49:10
     */
    protected function csrfCheck($params)
    {
        // TOKEN 校验
        $check = Request::checkToken('__token__', $params);
        if(false === $check) {
            throw new Exception( lang('lifecycle.the page token is invalid. Please refresh the page and try again') );
        }

        return true;
    }

    /**
     * 格式化
     * @param $id
     * @return string
     * @throws Exception
     * create_at: 2021-01-12 15:22:59
     * update_at: 2021-01-12 15:22:59
     */
    protected function formatIds($id)
    {
        if (is_array($id)) {
            foreach ($id as $v) {
                if (!is_string($v) && !is_numeric($v) && empty($v)) {
                    throw new Exception( lang('lifecycle.parameters error') );
                }
                $specialChars = $this->existsSpecialChar((string) $v);
                if ($specialChars > 0) throw new Exception( lang('lifecycle.contains special characters') );
            }
            return implode(',', $id);
        } else if (is_string($id) || is_numeric($id)) {
            $id = (string) $id;
            $specialChars = $this->existsSpecialChar($id);
            if ($specialChars > 0) throw new Exception( lang('lifecycle.contains special characters') );
            $tmp = explode(',', $id);
            foreach ($tmp as $v) {
                if (empty($v)) {
                    throw new Exception( lang('lifecycle.parameters error') );
                }
            }
            return $id;
        } else {
            throw new Exception( lang('lifecycle.unsupported parameter format') );
        }
    }

    /**
     * 过滤特殊字符
     * @param $strParam
     * @return string|string[]|null
     * create_at: 2021-01-25 17:55:18
     * update_at: 2021-01-25 17:55:18
     */
    protected function existsSpecialChar($strParam){
        $regex = "/\/|\，|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
        return preg_match($regex, $strParam,$strParam);
    }

    /********************************************* csrf *****************************************************/

    /********************************************* idx *****************************************************/

    /**
     * idx 自定义查询
     * @param $query
     * @return mixed
     * create_at: 2021-12-03 08:50:32
     * update_at: 2021-12-03 08:50:32
     */
    public function idxQuery($query)
    {
        return $query;
    }

    /**
     * idx 固定查询条件
     * @param $query
     * @return mixed
     * create_at: 2021-12-03 09:01:16
     * update_at: 2021-12-03 09:01:16
     */
    public function fixedCondition($query)
    {
        return $query;
    }

    /**
     * 数据过滤
     * @param $query
     * @param $mdl
     * @param $pk
     * @return mixed
     * create_at: 2022-02-11 15:16:22
     * update_at: 2022-02-11 15:16:22
     */
    public function dataAuthFilt($query, $mdl, $pk)
    {
        return $query;
    }

    /**
     * 自定义处理 数据权限作用域
     * @param $scope
     * @return mixed
     * create_at: 2022-03-29 16:18:13
     * update_at: 2022-03-29 16:18:13
     */
    public function handleScope($scope)
    {
        return $scope;
    }

    /**
     * idx 分页数据处理
     * @param $item
     * @param $key
     * @return mixed
     * create_at: 2021-12-03 09:23:38
     * update_at: 2021-12-03 09:23:38
     */
    public function eachHandle($item, $key)
    {
        return $item;
    }

    /**
     * idx 数据返回前处理
     * @param $data
     * @return mixed
     * create_at: 2022-01-27 09:17:02
     * update_at: 2022-01-27 09:17:02
     */
    public function idxDataHandle($data)
    {
        return $data;
    }

    /**
     * idx 模板变量赋值
     * @return array
     * create_at: 2021-12-03 09:46:58
     * update_at: 2021-12-03 09:46:58
     */
    public function idxAssign(): array
    {
        return [];
    }

    /**
     * idx 附加信息
     * @return array
     * create_at: 2021-12-03 15:00:12
     * update_at: 2021-12-03 15:00:12
     */
    public function idxAddition(): array
    {
        return [];
    }

    /********************************************* idx *****************************************************/

    /********************************************* add *****************************************************/

    /**
     * add 模板变量赋值
     * @param array $data
     * @return array
     * create_at: 2021-12-14 15:42:50
     * update_at: 2021-12-14 15:42:50
     */
    public function addAssign(array $data = []): array
    {
        return [];
    }

    /**
     * 参数校验前参数处理
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-09 16:29:10
     * update_at: 2021-12-09 16:29:10
     */
    public function bfrAddVerify($params, $origin)
    {
        // ...
        return $params;
    }

    /**
     * 数据入库前处理
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-10 13:09:24
     * update_at: 2021-12-10 13:09:24
     */
    public function bfrAdd($params, $origin)
    {
        // ...
        return $params;
    }

    /**
     * 数据入库后处理
     * @param $id
     * @param $params
     * @param $origin
     * @return bool
     * create_at: 2021-12-10 14:06:04
     * update_at: 2021-12-10 14:06:04
     */
    public function aftAdd($id, $params, $origin)
    {
        // ...
        return true;
    }

    /********************************************* add *****************************************************/

    /********************************************* upd *****************************************************/

    /**
     * 模板变量assign前处理
     * @param $data
     * @return mixed
     * create_at: 2021-12-14 16:19:02
     * update_at: 2021-12-14 16:19:02
     */
    public function bfrAssign($data)
    {
        return $data;
    }
    
    /**
     * upd 模板变量赋值
     * @param array $data
     * @return array
     * create_at: 2021-12-14 15:44:13
     * update_at: 2021-12-14 15:44:13
     */
    public function updAssign(array $data = []): array
    {
        return $data;
    }

    /**
     * 数据校验前处理
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-14 17:06:08
     * update_at: 2021-12-14 17:06:08
     */
    public function bfrUpdVerify($params, $origin)
    {
        // ...
        return $params;
    }

    /**
     * 数据更新前处理
     * @param $id
     * @param $params
     * @param $originParams
     * @return mixed
     * create_at: 2021-12-14 17:22:01
     * update_at: 2021-12-14 17:22:01
     */
    public function bfrUpd($id, $params, $originParams)
    {
        // ...
        return $params;
    }

    /**
     * 数据更新后处理
     * @param $id
     * @param $params
     * @param $originParams
     * @return bool
     * create_at: 2021-12-14 17:23:19
     * update_at: 2021-12-14 17:23:19
     */
    public function aftUpd($id, $params, $originParams)
    {
        // ...
        return true;
    }

    /********************************************* upd *****************************************************/

    /********************************************* del *****************************************************/

    /**
     * 删除前
     * @param $ids
     * @param $originParams
     * @return bool
     * create_at: 2021-12-15 13:20:00
     * update_at: 2021-12-15 13:20:00
     */
    public function bfrDel($ids, $originParams)
    {
        return true;
    }

    /**
     * 删除后
     * @param $ids
     * @param $originParams
     * @return bool
     * create_at: 2021-12-15 13:26:07
     * update_at: 2021-12-15 13:26:07
     */
    public function aftDel($ids, $originParams)
    {
        return true;
    }
    
    /********************************************* del *****************************************************/

    /********************************************* page *****************************************************/

    /**
     * 页面数据自定义查询
     * @param $query
     * @return mixed
     * create_at: 2022-02-17 09:27:22
     * update_at: 2022-02-17 09:27:22
     */
    public function pagedataQuery($query)
    {
        return $query;
    }

    /**
     * 打包页面数据
     * @param $data
     * @return mixed
     * create_at: 2022-02-17 09:06:13
     * update_at: 2022-02-17 09:06:13
     */
    public function packPagedata($data)
    {
        return $data;
    }
    /********************************************* page *****************************************************/
}
