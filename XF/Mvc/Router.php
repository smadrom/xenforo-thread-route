<?php
declare(strict_types=1);

namespace SM\ThreadRoute\XF\Mvc;

use XF\Http\Request;
use XF\Mvc\RouteMatch;

/**
 * Class Router
 * @package SM\ThreadRoute\XF\Mvc
 */
class Router extends XFCP_Router
{
    /**
     * @param $path
     * @param Request|null $request
     * @return RouteMatch
     */
    public function routeToController($path, Request $request = null)
    {
        if ($path === '') {
            $path = 'index';
        }

        $parts = explode('/', $path, 2);
        $prefix = $parts[0];
        $suffix = $parts[1] ?? null;

        $newThreadRoute = null;

        if (!isset($this->routes[$prefix]) || $prefix === 'threads') {
            $newThreadRoute = $this->routes['threads'] ?? null;
            if ($newThreadRoute) {
                $routePath = (string)substr($path, 0, -1);
                foreach ($newThreadRoute as $key => $route) {
                    $this->addRoute($routePath, $key, $route);
                }
            }
        }

        $match = parent::routeToController($path, $request);

        $newMatch = $match;

        if ($newThreadRoute) {
            $suffix = $prefix === 'threads' ? $suffix : $prefix;
            foreach ($newThreadRoute as $route) {
                $newMatch = $this->suffixMatchesRoute($suffix, $route, $match, $request);
                if ($newMatch) {
                    break;
                }
            }
        }

        return $newMatch;
    }

    /**
     * @param $prefix
     * @param $routeUrl
     * @return mixed
     */
    public function applyRouteFilterToUrl($prefix, $routeUrl)
    {
        $routeUrl = parent::applyRouteFilterToUrl($prefix, $routeUrl);

        $routeUrl = preg_replace('/^threads\//', '', $routeUrl);

        return $routeUrl;
    }
}
