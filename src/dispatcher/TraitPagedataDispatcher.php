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
 * DateTime: 2022-02-17 08:36:30
 */

namespace rocket\dispatcher;

use Exception;
use think\facade\Request;

/**
 * pagedata 调度
 */
trait TraitPagedataDispatcher
{
    use TraitCrudBase;

    /**
     * 页面数据调度
     * @return \think\response\Json|void
     * create_at: 2022-02-17 08:46:57
     * update_at: 2022-02-17 08:46:57
     */
    public function pagedata()
    {
        // 目标模型
        $action = self::$pagedata_name;
        $model = $this->targetModel();

        if (Request::isGet()) {
            try {
                return json_suc($model->$action());
            } catch (Exception $exception) {
                return json_err($exception->getMessage());
            }
        }
    }
}
