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
 * DateTime: 2021-12-15 10:09:09
 */

namespace rocket\dispatcher;

use think\facade\Request;
use think\facade\Db;
use Exception;

/**
 * del 调度
 */
trait TraitDelDispatcher
{
    use TraitCrudBase;

    /**
     * del调度
     * create_at: 2021-12-15 13:10:31
     * update_at: 2021-12-15 13:10:31
     */
    public function del()
    {
        // 目标模型
        $action = self::$del_name;
        $model = $this->targetModel();

        if (Request::isPost()) {
            if (property_exists($model, 'delTransac') && $model->delTransac) {
                $transac = true;
            }
            try {
                // 开启事务
                isset($transac) && Db::startTrans();
                // 成功
                if ($model->$action()) {
                    isset($transac) && Db::commit();
                    return json_suc();
                }
                // 失败
                isset($transac) && Db::rollback();
                return json_err();
            } catch (Exception $exception) {
                // 异常
                isset($transac) && Db::rollback();
                return json_err($exception->getMessage());
            }
        }
    }
}
