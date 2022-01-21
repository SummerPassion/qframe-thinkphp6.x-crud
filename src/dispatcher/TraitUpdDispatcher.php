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
 * DateTime: 2021-12-14 15:10:15
 */

namespace rocket\dispatcher;

use think\facade\Request;
use think\facade\View;
use think\facade\Db;

/**
 * upd调度
 */
trait TraitUpdDispatcher
{
    use TraitCrudBase;

    /**
     * 更新
     * @return mixed
     * create_at: 2021-12-15 10:13:27
     * update_at: 2021-12-15 10:13:27
     */
    public function upd()
    {
        // 目标更新id
        $id = Request::param('id/d');
        // 目标模型
        $action = self::$upd_name;
        $model = $this->targetModel();
        if (Request::isPost()) {
            if (property_exists($model, 'updTransac') && $model->updTransac) {
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
            // 目标更新数据
            $data = $model->where("id", $id)->find()->toArray();
            // assign前处理
            $data = $model->bfrAssign($data);
            // 渲染模板变量
            View::assign($model->updAssign([
                'data' => $data
            ]));
            return View::fetch();
        }
    }
}
