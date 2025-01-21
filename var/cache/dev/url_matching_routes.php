<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/auth/login' => [[['_route' => 'login', '_controller' => 'App\\Controller\\LoginController::Login'], null, ['POST' => 0], null, false, false, null]],
        '/api/auth/verify' => [[['_route' => 'login_verify', '_controller' => 'App\\Controller\\LoginController::verifyToken'], null, ['GET' => 0], null, false, false, null]],
        '/api/auth/logout' => [[['_route' => 'login_logout', '_controller' => 'App\\Controller\\LoginController::logout'], null, ['GET' => 0], null, false, false, null]],
        '/api/auth/recovery-account' => [[['_route' => 'login_recovery', '_controller' => 'App\\Controller\\LoginController::recoveryAccount'], null, ['POST' => 0], null, false, false, null]],
        '/api/role/add' => [[['_route' => 'app_role_add', '_controller' => 'App\\Controller\\RoleController::addRole'], null, ['POST' => 0], null, false, false, null]],
        '/api/role/exists' => [[['_route' => 'app_role_exists', '_controller' => 'App\\Controller\\RoleController::roleExists'], null, ['GET' => 0], null, false, false, null]],
        '/api/role/update' => [[['_route' => 'app_role_update', '_controller' => 'App\\Controller\\RoleController::updateRole'], null, ['PUT' => 0], null, false, false, null]],
        '/api/role/findById' => [[['_route' => 'app_role_findById', '_controller' => 'App\\Controller\\RoleController::findRoleById'], null, ['GET' => 0], null, false, false, null]],
        '/api/role/findByName' => [[['_route' => 'app_role_findByName', '_controller' => 'App\\Controller\\RoleController::findRoleByName'], null, ['GET' => 0], null, false, false, null]],
        '/api/role/findAll' => [[['_route' => 'app_role_findAll', '_controller' => 'App\\Controller\\RoleController::findAll'], null, ['GET' => 0], null, false, false, null]],
        '/api/role/delete' => [[['_route' => 'app_role_delete', '_controller' => 'App\\Controller\\RoleController::deleteRole'], null, ['DELETE' => 0], null, false, false, null]],
        '/api/user/add' => [[['_route' => 'api_user_add', '_controller' => 'App\\Controller\\UserController::add'], null, ['POST' => 0], null, false, false, null]],
        '/api/user/twoFactorAuth' => [[['_route' => 'api_user_twoFactorAuth', '_controller' => 'App\\Controller\\UserController::twoFactorAuth'], null, ['POST' => 0], null, false, false, null]],
        '/api/user/validade' => [[['_route' => 'api_user_validadeUser', '_controller' => 'App\\Controller\\UserController::validade'], null, ['POST' => 0], null, false, false, null]],
        '/api/user/exist' => [[['_route' => 'api_user_verifyUserExist', '_controller' => 'App\\Controller\\UserController::verifyUserExist'], null, ['POST' => 0], null, false, false, null]],
        '/api/user/update' => [[['_route' => 'api_user_resetPassword', '_controller' => 'App\\Controller\\UserController::update'], null, ['PUT' => 0], null, false, false, null]],
        '/api/user/delete' => [[['_route' => 'api_user_delete', '_controller' => 'App\\Controller\\UserController::delete'], null, ['DELETE' => 0], null, false, false, null]],
        '/api/user/findUserSession' => [[['_route' => 'login_findUserSession', '_controller' => 'App\\Controller\\UserController::findUser'], null, ['GET' => 0], null, false, false, null]],
        '/api/user/findDocument' => [[['_route' => 'api_user_get', '_controller' => 'App\\Controller\\UserController::findByDocument'], null, ['GET' => 0], null, false, false, null]],
        '/api/user/verifyTokenRecoveryAccount' => [[['_route' => 'api_user_verifyTokenRecoveryAccount', '_controller' => 'App\\Controller\\UserController::verifyTokenRecoveryAccount'], null, ['GET' => 0], null, false, false, null]],
        '/api/user/confirmPasswordReset' => [[['_route' => 'api_user_confirmPasswordReset', '_controller' => 'App\\Controller\\UserController::confirmPasswordReset'], null, ['POST' => 0], null, false, false, null]],
        '/api/doc' => [[['_route' => 'app.swagger_ui', '_controller' => 'nelmio_api_doc.controller.swagger_ui'], null, ['GET' => 0], null, false, false, null]],
        '/api/doc.json' => [[['_route' => 'app.swagger', '_controller' => 'nelmio_api_doc.controller.swagger'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
