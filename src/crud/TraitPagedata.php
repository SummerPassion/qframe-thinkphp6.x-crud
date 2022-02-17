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
 * DateTime: 2022-02-17 08:47:32
 */

namespace rocket\crud;

use think\facade\Request;
use Exception;

/**
 * Trait页面数据
 */
trait TraitPagedata
{
    /**
     * 页面数据
     * @return array[]
     * @throws Exception
     * create_at: 2022-02-17 09:04:13
     * update_at: 2022-02-17 09:04:13
     */
    public function pagedata()
    {
        // 返回数据
        $dataSet = [
            "original" => [],   // 原始数据
            "selectList" => [], // 下拉列表
        ];
        // 参数
        $params = Request::param();

        if (!isset($params[$this->pk])) throw new Exception("`{$this->pk}`参数错误， 未知的查询目标！");
        $id = $params[$this->pk];
        if (!$id || 0 == count($this->attrsPagedata)) {
            return $dataSet;
        }

        $query = self::field($this->attrsPagedata)->where($this->pk, $id);
        $this->pagedataQuery($query);
        $query = $query->find();
        $query && $dataSet['original'] = $query->toArray();

        return $this->packPagedata($dataSet);
    }
}
