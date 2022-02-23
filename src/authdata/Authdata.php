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
 * DateTime: 2022-02-11 09:21:57
 */

namespace rocket\authdata;

use app\backend\model\Bkprivileges;
use app\backend\model\Bkusersrelations;
use think\facade\Request;
use Exception;

/**
 * 数据权限
 * Class Authdata
 * @package rocket\authdata
 * create_at: 2022-02-11 09:22:42
 * update_at: 2022-02-11 09:22:42
 */
class Authdata
{
    const ENV_UD = 1; // 改 | 删
    const ENV_I = 2; // 查

    /**
     * 模型数据权限校验
     * @param $env
     * @param $mdl string 模型
     * @param $pk string 主键
     * @param $ids string|array 目标数据
     * @param $uid int|string 用户
     * @param string $dataAuthVar
     * @return array
     * @throws Exception
     * create_at: 2022-02-11 14:18:48
     * update_at: 2022-02-11 14:18:48
     */
    public static function authMdl($env, string $mdl, ?string $pk, $ids, $uid, string $dataAuthVar = "create_by")
    {
        //
        if (self::ENV_UD == $env && empty($ids)) {
            throw new Exception("未知的目标数据！");
        }

        // 请求控制器 & 方法
        $reqUrl = Request::controller(true) . '/' . Request::action(true);
        // 权限模型
        $priviMdl = new Bkprivileges();
        $dataType = $priviMdl->where('route', $reqUrl)->value('data_type');

        // 实例化模型
        $model = new $mdl;

        // 验证结果
        $auth = false;

        // 范围
        $scope = [];

        // 数据权限类型
        switch ($dataType) {
            case Bkprivileges::DATATYPE_1:
                // 无
                $auth = true;
                break;
            case Bkprivileges::DATATYPE_2:
                // 仅自身   当前登录用户
                $scope = [$uid];
                break;
            case Bkprivileges::DATATYPE_3:
                // 租户整体 当前登录用户向上找到lev=3的用户下所有
                $bkUsersRelationsMdl = new Bkusersrelations();
                $tenant = $bkUsersRelationsMdl
                    ->alias("r")
                    ->leftJoin("bk_users u", "r.rid=u.id")
                    ->field('u.id')
                    ->where("r.uid", $uid)
                    ->where("u.lev", 3)
                    ->value('u.id');
                if ($tenant) {
                    $subs = $bkUsersRelationsMdl->where("rid", $tenant)->column('uid');
                    $subs[] = $tenant;
                    if ($subs) {
                        $scope = array_merge($scope, $subs);
                    }
                }
                $scope[] = $uid;
                break;
            case Bkprivileges::DATATYPE_4:
                // 向下包含 当前登录用户下所有
                $bkUsersRelationsMdl = new Bkusersrelations();
                $subs = $bkUsersRelationsMdl->where("rid", $uid)->column('uid');
                $subs[] = $uid;
                $scope = $subs;
                break;
        }

        if (self::ENV_UD == $env && Bkprivileges::DATATYPE_1 != $dataType) {
            $valid = $model->whereIn($pk, $ids)->whereNotIn($dataAuthVar, $scope)->find();
            if ($valid) {
                throw new Exception("无数据权限进行该操作！");
            }
        }

        return $scope;
    }
}