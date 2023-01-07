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
 * DateTime: 2021-12-22 11:25:46
 */

namespace rocket\contracts;

interface MdlContract
{
    /********************************************* idx *****************************************************/

    /**
     * idx 自定义查询
     * @param $query
     * @return mixed
     * create_at: 2021-12-22 11:29:12
     * update_at: 2021-12-22 11:29:12
     */
    public function idxQuery($query);

    /**
     * idx 是否withTrashed
     * @return mixed
     * create_at: 2022-07-14 11:50:47
     * update_at: 2022-07-14 11:50:47
     */
    public function midWithTrashed();

    /**
     * idx 固定查询条件
     * @param $query
     * @return mixed
     * create_at: 2021-12-22 11:29:28
     * update_at: 2021-12-22 11:29:28
     */
    public function fixedCondition($query);

    /**
     * 数据权限过滤
     * @param $query
     * @param $mdl
     * @param $pk
     * @return mixed
     * create_at: 2022-02-11 15:16:08
     * update_at: 2022-02-11 15:16:08
     */
    public function dataAuthFilt($query, $mdl, $pk);

    /**
     * 自定义处理 数据权限作用域
     * @param $scope
     * @return mixed
     * create_at: 2022-03-29 16:25:13
     * update_at: 2022-03-29 16:25:13
     */
    public function handleScope($scope);

    /**
     * idx 分页数据处理
     * @param $item
     * @param $key
     * @return mixed
     * create_at: 2021-12-22 11:29:40
     * update_at: 2021-12-22 11:29:40
     */
    public function eachHandle($item, $key);

    /**
     * idx 模板变量赋值
     * @return array
     * create_at: 2021-12-22 11:29:53
     * update_at: 2021-12-22 11:29:53
     */
    public function idxAssign(): array;

    /**
     * idx 附加信息
     * @return array
     * create_at: 2021-12-22 11:30:04
     * update_at: 2021-12-22 11:30:04
     */
    public function idxAddition(): array;

    /**
     * idx 數據處理
     * @param $data
     * @return mixed
     * create_at: 2023-01-07 13:30:42
     * update_at: 2023-01-07 13:30:42
     */
    public function idxDataHandle($data);

    /**
     * ept 前數據處理
     * @param $data
     * @return mixed
     * create_at: 2023-01-07 13:30:50
     * update_at: 2023-01-07 13:30:50
     */
    public function eptDataHandle($data);

    /********************************************* idx *****************************************************/

    /********************************************* add *****************************************************/

    /**
     * add 模板变量赋值
     * @param array $data
     * @return array
     * create_at: 2021-12-22 11:30:23
     * update_at: 2021-12-22 11:30:23
     */
    public function addAssign(array $data = []): array;

    /**
     * 参数校验前参数处理
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-22 11:30:34
     * update_at: 2021-12-22 11:30:34
     */
    public function bfrAddVerify($params, $origin);

    /**
     * 数据入库前处理
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-22 11:30:42
     * update_at: 2021-12-22 11:30:42
     */
    public function bfrAdd($params, $origin);

    /**
     * 数据入库后处理
     * @param $id
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-22 11:30:57
     * update_at: 2021-12-22 11:30:57
     */
    public function aftAdd($id, $params, $origin);

    /********************************************* add *****************************************************/

    /********************************************* upd *****************************************************/

    /**
     * 模板变量assign前处理
     * @param $data
     * @return mixed
     * create_at: 2021-12-22 11:31:06
     * update_at: 2021-12-22 11:31:06
     */
    public function bfrAssign($data);

    /**
     * upd 模板变量赋值
     * @param array $data
     * @return array
     * create_at: 2021-12-22 11:31:21
     * update_at: 2021-12-22 11:31:21
     */
    public function updAssign(array $data = []): array;

    /**
     * 数据校验前处理
     * @param $params
     * @param $origin
     * @return mixed
     * create_at: 2021-12-22 11:31:29
     * update_at: 2021-12-22 11:31:29
     */
    public function bfrUpdVerify($params, $origin);

    /**
     * 数据更新前处理
     * @param $id
     * @param $params
     * @param $originParams
     * @return mixed
     * create_at: 2021-12-22 11:31:38
     * update_at: 2021-12-22 11:31:38
     */
    public function bfrUpd($id, $params, $originParams);

    /**
     * 数据更新后处理
     * @param $id
     * @param $params
     * @param $originParams
     * @return mixed
     * create_at: 2021-12-22 11:31:46
     * update_at: 2021-12-22 11:31:46
     */
    public function aftUpd($id, $params, $originParams);

    /********************************************* upd *****************************************************/

    /********************************************* del *****************************************************/

    /**
     * 删除前
     * @param $ids
     * @param $originParams
     * @return mixed
     * create_at: 2021-12-22 11:31:55
     * update_at: 2021-12-22 11:31:55
     */
    public function bfrDel($ids, $originParams);

    /**
     * 删除后
     * @param $ids
     * @param $originParams
     * @return mixed
     * create_at: 2021-12-22 11:32:04
     * update_at: 2021-12-22 11:32:04
     */
    public function aftDel($ids, $originParams);

    /********************************************* del *****************************************************/

    /********************************************* page *****************************************************/

    /**
     * 页面数据自定义查询
     * @param $query
     * @return mixed
     * create_at: 2022-02-17 09:27:53
     * update_at: 2022-02-17 09:27:53
     */
    public function pagedataQuery($query);

    /**
     * 打包页面数据
     * @param $data
     * @return mixed
     * create_at: 2022-02-17 09:05:18
     * update_at: 2022-02-17 09:05:18
     */
    public function packPagedata($data);
    /********************************************* page *****************************************************/
}
