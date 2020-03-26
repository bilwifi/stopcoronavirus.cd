<?php

namespace App\Admin\Forms\Category;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\StepForm;

class CatergoryKg extends StepForm
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Kikongo';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        return $this->next($request->all());
        admin_success('Processed successfully.');
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {

        $this->html('Kikongo', __('Language'));
        $this->hidden('locale')
        ->default("kg")->rules('required')
        ->value("kg");
        $this->hidden('id');
        $this->text('name', __('Name'))->required();
        $this->text('slug', __('Slug'));
        $this->summernote("description", __('Description'));
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return parent::data();
    }
}