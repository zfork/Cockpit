<?php

namespace App\Helper;


class Acl extends \Lime\Helper {

    protected $roles;

    protected function initialize() {

        $this->roles = $this->app->helper('cache')->read('app.roles.permissions', function() {
            return $this->cache();
        });
    }

    public function roles() {

        $roles = [
            ['appid' => 'admin', 'name' => 'Admin']
        ];

        foreach ($this->roles as $role) {
            
            $roles[] = [
                'appid' => $role['appid'],
                'name' => $role['name'],
            ];
        }

        return $roles;
    }

    public function isAllowed($permission, $role = null) {

        $role = $role ?? $this->app->helper('auth')->getUser('role');

        if ($role == 'admin') {
            return true;
        }

        return isset($this->roles[$role][$permission]) && $this->roles[$role][$permission];
    }

    public function cache() {

        $cache = [];

        $roles = $this->app->data->find('system/roles', [
            'sort' => ['name' => 1]
        ])->toArray();

        foreach ($roles as $role) {
            $cache[$role['appid']] = $role;
        }

        $this->app->helper('cache')->write('app.roles.permissions', $cache);

        return $cache;
    }
}