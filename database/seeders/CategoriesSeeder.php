<?php

namespace  Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name'     => '手机数码',
                'children' => [
                    [
                        'name'     => '手机通讯',
                        'children' => [
                            ['name' => '手机','image' => 'https://img10.360buyimg.com/focus/s140x140_jfs/t11503/241/2246064496/4783/cea2850e/5a169216N0701c7f1.jpg'],
                        ],
                    ],
                    [
                        'name'     => '手机配件',
                        'children' => [
                            ['name' => '数据线','image' => 'https://img12.360buyimg.com/focus/s140x140_jfs/t18055/312/1342501458/9462/4699ed8a/5ac48672N11cf61fe.jpg'],
                        ],
                    ],
                ],
            ],
            [
                'name'     => '电脑办公',
                'children' => [
                    [
                        'name'     => '电脑整机',
                        'children' => [
                            ['name' => '笔记本','image' => 'https://img11.360buyimg.com/focus/s140x140_jfs/t13852/288/980080912/2623/73d2a1a5/5a17b976N7ab8a3a6.jpg'],
                        ],
                    ],
                    [
                        'name'     => '电脑配件',
                        'children' => [
                            ['name' => '内存','image' => 'https://img13.360buyimg.com/focus/s140x140_jfs/t12430/209/999346936/2994/bc6ab03f/5a17b5f6N09faf599.jpg'],
                        ],
                    ],
                ],
            ],
            [
                'name'     => '家用电器',
                'children' => [
                    [
                        'name'     => '厨房小电',
                        'children' => [
                            ['name' => '电磁炉','image' => 'https://img13.360buyimg.com/focus/s140x140_jfs/t11209/197/2422417970/2811/d167e855/5a17f1edN56abbe6e.jpg'],
                        ],
                    ],
                ],
            ],
            [
                'name'     => '男装',
                'children' => [
                    [
                        'name'     => '男士外套',
                        'children' => [
                            ['name' => '短袖T恤','image' => 'https://img13.360buyimg.com/focus/s140x140_jfs/t18436/155/1324938407/6646/1a66cfa0/5ac47fffNe7a93aca.jpg'],
                        ],
                    ],
                ],
            ],
            [
                'name'     => '女装',
                'children' => [
                    [
                        'name'     => '上装',
                        'children' => [
                            ['name' => '白衬衫','image' => 'https://img11.360buyimg.com/focus/s140x140_jfs/t14266/108/2448202334/2099/c038057b/5a9fbfc7N33c2ad32.jpg'],
                        ],
                    ],
                ],
            ],
            [
                'name'     => '美妆护肤',
                'children' => [
                    [
                        'name'     => '香水',
                        'children' => [
                            ['name' => '女士香水','image' => 'https://img13.360buyimg.com/focus/s140x140_jfs/t21634/217/114542271/9364/cbd83d13/5afd3c4bNc8d91bef.jpg'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $data) {
            $this->createCategory($data);
        }
    }

    protected function createCategory($data, $parent = null)
    {
        // 创建一个新的类目对象
        $category = new Category(['name' => $data['name']]);
		// 如果有 children 字段则代表这是一个父类目
        $category->is_directory = isset($data['children']);
        // 如果image插入图片
        $category->image = isset($data['image'])?$data['image']:'';
		// 如果有传入 $parent 参数，代表有父类目
        if (!is_null($parent)) {
            $category->parent()->associate($parent);
        }
		//  保存到数据库
        $category->save();
		// 如果有 children 字段并且 children 字段是一个数组
        if (isset($data['children']) && is_array($data['children'])) {
		    // 遍历 children 字段
            foreach ($data['children'] as $child) {
			    // 递归调用 createCategory 方法，第二个参数即为刚刚创建的类目
                $this->createCategory($child, $category);
            }
        }
    }
}
