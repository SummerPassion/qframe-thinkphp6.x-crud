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
 * DateTime: 2021-11-25 11:20:04
 */

namespace rocket\dispatcher;

use think\facade\Request;
use think\facade\View;

/**
 * idx 调度
 */
trait TraitIdxDispatcher
{
    use TraitCrudBase;

    /**
     * 列表页
     * create_at: 2021-11-25 11:22:26
     * update_at: 2021-11-25 11:22:26
     */
    public function idx()
    {
        // 目标模型
        $action = self::$idx_name;
        $model = $this->targetModel();

        if (Request::isPost()) {
            return json_suc($model->$action());
        } else {
            // 渲染模板变量
            View::assign($model->idxAssign());
            return View::fetch();
        }
    }
}
