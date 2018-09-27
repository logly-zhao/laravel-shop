<?php

namespace App\Admin\Controllers;

use App\Models\Banner;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class BannersController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('滚动图片列表')
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑滚动图片')
            ->body($this->form(true)->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建滚动图片')
            ->body($this->form(false));
    }

    protected function grid()
    {
        return Admin::grid(Banner::class, function(Grid $grid){
            $grid->id('ID')->sortable();
            $grid->is_front('显示位置')->display(function ($value) {
                return $value ? '首页' : '关于我们';
            });
            $grid->image('路径');
        });
        /*
        $grid = new Grid(new Banner);
        $grid->id('Id');
        $grid->image('Image');
        return $grid;*/
    }

    protected function detail($id)
    {
        $show = new Show(Banner::findOrFail($id));

        $show->id('ID');
        $show->image('路径');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Banner);
        $form->radio('is_front', '显示位置')
            ->options(['1' => '首页', '0' => '关于我们'])
            ->default('1')
            ->rules('required');
        $form->image('image', '图片路径')->rules('required|image');

        return $form;
    }


}
