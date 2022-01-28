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
 * DateTime: 2021-11-25 09:51:33
 */

namespace rocket\crud;

use think\facade\Request;
use Exception;

/**
 * Trait创建
 */
trait TraitAdd
{
    /**
     * 新增
     * @return bool
     * @throws Exception
     * create_at: 2021-12-09 16:27:13
     * update_at: 2021-12-09 16:27:13
     */
    public function add()
    {
        // 请求参数
        $originParams = Request::param();
        // 过滤参数
        $filterParams = Request::only($this->attrsAdd);
        // 校验前处理
        $params = $this->bfrAddVerify($filterParams, $originParams);
        // 验证
        if ($this->validator && !$this->validator->scene('add')->check($params)) {
            throw new Exception($this->validator->getError());
        }
        // 入库前处理
        $params = $this->bfrAdd($params, $originParams);
        // 新增
        $mdl = new self;
        $mdl->save($params);
        // 入库后处理
        $aftAdd = $this->aftAdd($mdl->id, $params, $originParams);
        // 校验页面token
        $this->csrfCheck($originParams);
        return true;
    }
}
