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
 * DateTime: 2021-12-07 16:12:17
 */

namespace rocket\dispatcher;

use think\facade\Request;
use think\facade\View;
use think\facade\Db;
use Exception;

/**
 * add 调度
 */
trait TraitAddDispatcher
{
    use TraitCrudBase;

    /**
     * 新增页
     * @return string
     * create_at: 2021-12-07 16:13:44
     * update_at: 2021-12-07 16:13:44
     */
    public function add()
    {
        // 目标模型
        $action = self::$add_name;
        $model = $this->targetModel();
        if (Request::isPost()) {
            if (property_exists($model, 'addTransac') && $model->addTransac) {
                $transac = true;
            }
            try {
                // 开启事务
                isset($transac) && Db::startTrans();
                // 验证器校验
                $validator = $this->targetValidator() ?: null;
                if ($validator) {
                    $model->setValidator($validator);
                }
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
        } else {
            // 渲染模板变量
            View::assign($model->addAssign());
            return View::fetch();
        }
    }
}
