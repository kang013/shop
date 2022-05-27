<?php

namespace App\Http\Middleware;

use Closure;

class AcceptHeader
{
    public function handle($request, Closure $next)
    {
        // 添加一个中间件，给所有的 API 路由手动设置 Accept 头
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
