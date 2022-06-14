<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->namespace('Api')
    ->name('api.v1.')
    ->group(function () {
        // 短信验证码
        Route::post('verificationCodes', 'VerificationCodesController@store')
            ->name('verificationCodes.store');
        // 用户注册
        Route::post('users', 'UsersController@store')
            ->name('users.store');
        // 第三方登录
        //注意这里的参数，我们对 social_type 进行了限制，只会匹配 wechat，如果你增加了其他的第三方登录，可以在这里增加限制，例如支持微信及微博：->where('social_type', 'wechat|weibo')
        Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->where('social_type', 'wechat')
            ->name('socials.authorizations.store');
        // 登录
        Route::post('authorizations', 'AuthorizationsController@store')
            ->name('authorizations.store');
        // 刷新token
        Route::put('authorizations/current', 'AuthorizationsController@update')
            ->name('authorizations.update');
        // 删除token
        Route::delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('authorizations.destroy');


        Route::middleware('throttle:' . config('api.rate_limits.access'))
            ->group(function () {
                // 游客可以访问的接口
                // 图片验证码
                Route::post('captchas', 'CaptchasController@store')
                    ->name('captchas.store');
                // 某个用户的详情
                Route::get('users/{user}', 'UsersController@show')
                    ->name('users.show');

                // 登录后可以访问的接口
                Route::middleware('auth:api')->group(function() {
                    // 当前登录用户信息
                    Route::get('user', 'UsersController@me')
                        ->name('user.show');
                    // 编辑登录用户信息
                    Route::patch('user', 'UsersController@update')
                        ->name('user.update');
                    Route::put('user', 'UsersController@update')
                        ->name('user.update');
                    // 上传图片
                    Route::post('images', 'ImagesController@store')
                        ->name('images.store');
                    // 收货地址
                    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
                    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
                    Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
                    Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');
                    Route::get('user_addresses_default', 'UserAddressesController@default')->name('user_addresses.default');
                    // 商品收藏
                    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
                    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
                    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
                    // 购物车
                    Route::post('cart', 'CartController@add')->name('cart.add');
                    Route::get('cart', 'CartController@index')->name('cart.index');
                    Route::delete('cart/{cart_item}', 'CartController@remove')->name('cart.remove');
                    // 订单
                    Route::post('orders', 'OrdersController@store')->name('orders.store');
                    Route::get('orders', 'OrdersController@index')->name('orders.index');
                    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
                    Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
                    Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
                    Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');
                    Route::post('orders/{order}/apply_refund', 'OrdersController@applyRefund')->name('orders.apply_refund');
                    Route::post('orders/{order}/cancel', 'OrdersController@cancel')->name('orders.cancel');
                    Route::post('orders/{order}/delete', 'OrdersController@delete')->name('orders.delete');
                    // 支付
                    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
                    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
                    Route::get('payment/{order}/wechat', 'PaymentController@payByWechat')->name('payment.wechat');
                    // 优惠券
                    Route::get('coupon_codes/{code}', 'CouponCodesController@show')->name('coupon_codes.show');
                    // 众筹
                    Route::post('crowdfunding_orders', 'OrdersController@crowdfunding')->name('crowdfunding_orders.store');
                    // 秒杀下单
                    Route::post('seckill_orders', 'OrdersController@seckill')->name('seckill_orders.store')->middleware('random_drop:10');

                    // 商品详情，执行登录后的
                    Route::get('products/{product}/auth', 'ProductsController@show')->name('products.show.auth');
                });
            });

        // 商品
        Route::get('products', 'ProductsController@index')->name('products.index');
        Route::get('products/{product}', 'ProductsController@show')->name('products.show');
        Route::get('products/{product}/review', 'ProductsController@review')->name('products.review');
        // 分类
        Route::get('categories', 'CategoriesController@index')->name('categories.index');
        // 支付回调
        Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');
        Route::post('payment/wechat/notify', 'PaymentController@wechatNotify')->name('payment.wechat.notify');
        Route::post('payment/wechat/refund_notify', 'PaymentController@wechatRefundNotify')->name('payment.wechat.refund_notify');

        // 广告轮播图
        Route::get('slide', 'HomeController@index')->name('slide');
        // 首页秒杀列表
        Route::get('seckill', 'HomeController@seckill')->name('seckill');
        // 猜你喜欢
        Route::get('like_product', 'HomeController@likeProduct')->name('like_product');
    });
