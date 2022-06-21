<?php

namespace App\Console\Commands\Install;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '安装项目';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $progress = $this->output->createProgressBar(6);

        # 生成密钥
        \Artisan::call('key:generate');

        $progress->advance();

        # 生成jwt密钥
        \Artisan::call('jwt:secret');

        $progress->advance();

        # 数据库迁移
        \Artisan::call('migrate');

        $progress->advance();

        # 生成假数据
        \Artisan::call('db:seed');

        $progress->advance();

        # Elasticsearch 的迁移命令
        \Artisan::call('es:migrate');

        $progress->advance();

        # 同步商品数据到 Elasticsearch
        \Artisan::call('es:sync-products');

        $progress->finish();

        $this->info('安装完成');
    }
}
