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
 * DateTime: 2021-11-25 09:01:03
 */

namespace rocket\dispatcher;

use rocket\contracts\MdlContract;
use think\facade\Request;
use think\facade\Config;

/**
 * Crud基类
 */
trait TraitCrudBase
{
    /**
     * 操作{列表}名
     * @var string
     * create_at: 2021-11-25 09:27:45
     * update_at: 2021-11-25 09:27:45
     */
    protected static $idx_name      = "idx";

    /**
     * 操作{创建}名
     * @var string
     * create_at: 2021-11-25 09:27:51
     * update_at: 2021-11-25 09:27:51
     */
    protected static $add_name     = "add";

    /**
     * 操作{更新}名
     * @var string
     * create_at: 2021-11-25 09:27:59
     * update_at: 2021-11-25 09:27:59
     */
    protected static $upd_name     = "upd";

    /**
     * 操作{删除}名
     * @var string
     * create_at: 2021-11-25 09:28:06
     * update_at: 2021-11-25 09:28:06
     */
    protected static $del_name     = "del";

    /**
     * 操作{添加和编辑页面数据}名
     * @var string
     * create_at: 2021-11-25 09:28:13
     * update_at: 2021-11-25 09:28:13
     */
    protected static $pgd_name   = "pgd";

    /**
     * 解析app path
     * @return string
     * create_at: 2021-12-10 09:49:48
     * update_at: 2021-12-10 09:49:48
     */
    protected function appResolve(): string
    {
        // 默认访问应用
        $defaultApp = Config::get('app.default_backend_app');
        // 获取URL访问根目录
        $reqRootUrl = ltrim(Request::rootUrl(), '/');

        return 'app\\' . ($reqRootUrl ?: $defaultApp);
    }

    /**
     * 目标模型完整命名空间（默认）
     * create_at: 2021-01-04 14:09:12
     * update_at: 2021-01-04 14:09:12
     */
    protected function modelNamespace(): string
    {
        return $this->appResolve() . "\\" . "model";
    }

    /**
     * 目标模型验证器完整命名空间（默认）
     * @return string
     * create_at: 2021-12-10 09:50:50
     * update_at: 2021-12-10 09:50:50
     */
    protected function validateNamespace(): string
    {
        return $this->appResolve() . "\\" . "validate";
    }

    /**
     * 目标请求控制器
     * @return string
     * create_at: 2021-11-25 13:45:10
     * update_at: 2021-11-25 13:45:10
     */
    protected function reqCtrl(): string
    {
        return strtolower(Request::controller());
    }

    /**
     * 实例化目标模型
     * create_at: 2021-11-25 11:26:12
     * update_at: 2021-11-25 11:26:12
     */
    protected function targetModel()
    {
        $model = $this->modelNamespace() . "\\" . ucfirst($this->reqCtrl());
        return $this->make($model);
    }

    /**
     * make 模型
     * @param $mdlName
     * @return MdlContract
     * create_at: 2021-12-22 11:36:18
     * update_at: 2021-12-22 11:36:18
     */
    protected function make($mdlName): MdlContract
    {
        return new $mdlName();
    }

    /**
     * 实例化验证器
     * @return mixed
     * create_at: 2021-12-10 09:51:37
     * update_at: 2021-12-10 09:51:37
     */
    protected function targetValidator() {
        $validator = $this->validateNamespace() . "\\" . ucfirst($this->reqCtrl());
        if (class_exists($validator)) {
            return new $validator;
        }

        return false;
    }
}
