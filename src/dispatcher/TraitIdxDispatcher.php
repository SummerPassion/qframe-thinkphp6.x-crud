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
use Exception;

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
            try {
                return json_suc($model->$action());
            } catch (Exception $exception) {
                return json_err($exception->getMessage());
            }
        } else {
            if (property_exists($model, "api")) {
                try {
                    return json_suc($model->$action());
                } catch (Exception $exception) {
                    return json_err($exception->getMessage());
                }
            }
            // 渲染模板变量
            View::assign($model->idxAssign());
            return View::fetch();
        }
    }

    /**
     * 导出
     * create_at: 2022-01-25 15:12:12
     * update_at: 2022-01-25 15:12:12
     */
    public function ept()
    {
        $model = $this->targetModel();
        if (Request::isPost()) {
            try {
                return json_suc($model->ept());
            } catch (Exception $exception) {
                return json_err($exception->getMessage());
            }
        }
    }
}
