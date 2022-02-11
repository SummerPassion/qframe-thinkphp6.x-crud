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
 * DateTime: 2021-12-15 10:08:15
 */

namespace rocket\crud;

use rocket\authdata\Authdata;
use think\facade\Request;
use Exception;

/**
 * Trait删除
 */
trait TraitDel
{
    /**
     * 删除
     * @return bool
     * @throws Exception
     * create_at: 2021-12-21 15:51:30
     * update_at: 2021-12-21 15:51:30
     */
    public function del()
    {
        // 请求参数
        $originParams = Request::param();
        // 过滤参数
        $filterParams = Request::only($this->attrsDel);
        // 目标`id`
        if (!key_exists($this->pk, $originParams)) {
            throw new Exception("`{$this->pk}`参数名不存在，请使用主键！");
        }
        $ids = $originParams[$this->pk];
        if (!$ids) {
            throw new Exception("`{$this->pk}`参数错误， 未知的删除目标！");
        }
        // 格式化删除目标
        $ids = $this->formatIds($ids);
        // 数据权限
        $this->dataAuth(Authdata::ENV_UD, self::class, $this->pk, $ids);
        // 删除前
        $bfr = $this->bfrDel($ids, $originParams);
        // 删除
        $del = self::whereIn($this->pk, $ids)->delete();
        // 删除后
        (false !== $del) && $this->aftDel($ids, $originParams);

        return true;
    }
}
