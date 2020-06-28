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
        $routePath = (string)substr($path, 0, -1);

        $newThreadRoute = null;

        if ($path === '') {
            $path = 'index';
        }

        $parts = explode('/', $path, 2);
        $prefix = $parts[0];

        if (!isset($this->routes[$prefix]) || stripos($path, 'threads') !== false) {
            $newThreadRoute = $this->routes['threads'] ?? null;
            if ($newThreadRoute) {
                $this->addRoute($routePath, 'post', $newThreadRoute['post']);
                $this->addRoute($routePath, '', $newThreadRoute['']);
            }
        }

        $match = parent::routeToController($path, $request);

        $newMatch = $match;

        if ($newThreadRoute) {
            foreach ($newThreadRoute as $route) {
                $newMatch = $this->suffixMatchesRoute($prefix, $route, $match, $request);
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

        if (stripos($routeUrl, 'threads') !== false) {
            $routeUrl = str_replace('threads/', '', $routeUrl);
        }

        return $routeUrl;
    }
}
