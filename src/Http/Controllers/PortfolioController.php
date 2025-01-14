<?php

namespace TomatoPHP\TomatoCms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoCms\Jobs\BeJob;

class PortfolioController extends Controller
{
    public string $model;

    public function __construct()
    {
        $this->model = \TomatoPHP\TomatoCms\Models\Portfolio::class;
    }

    /**
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request): View|JsonResponse
    {
        return Tomato::index(
            request: $request,
            model: $this->model,
            view: 'tomato-cms::portfolios.index',
            table: \TomatoPHP\TomatoCms\Tables\PortfolioTable::class
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {
        return Tomato::json(
            request: $request,
            model: \TomatoPHP\TomatoCms\Models\Portfolio::class,
        );
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return Tomato::create(
            view: 'tomato-cms::portfolios.create',
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $response = Tomato::store(
            request: $request,
            model: \TomatoPHP\TomatoCms\Models\Portfolio::class,
            validation: [
                'service_id' => 'nullable|exists:services,id',
                'title' => 'required|array',
                'title*' => 'required|max:255|string',
                'short_description' => 'nullable|array',
                'short_description*' => 'nullable|max:255|string',
                'keywords*' => 'nullable|array',
                'keywords' => 'nullable|max:65535',
                'company' => 'nullable|array',
                'company*' => 'nullable|max:255|string',
                'body' => 'nullable|array',
                'body*' => 'nullable',
                'activated' => 'required',
            ],
            message: __('Portfolio updated successfully'),
            redirect: 'admin.portfolios.index',
            hasMedia: true,
            collection: [
                "feature" => false,
                "images" => true
            ]
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    /**
     * @param \TomatoPHP\TomatoCms\Models\Portfolio $model
     * @return View|JsonResponse
     */
    public function show(\TomatoPHP\TomatoCms\Models\Portfolio $model): View|JsonResponse
    {
        return Tomato::get(
            model: $model,
            view: 'tomato-cms::portfolios.show',
            hasMedia: true,
            collection: [
                "feature" => false,
                "images" => true
            ]
        );
    }

    /**
     * @param \TomatoPHP\TomatoCms\Models\Portfolio $model
     * @return View
     */
    public function edit(\TomatoPHP\TomatoCms\Models\Portfolio $model): View
    {
        return Tomato::get(
            model: $model,
            view: 'tomato-cms::portfolios.edit',
            hasMedia: true,
            collection: [
                "feature" => false,
                "images" => true
            ]
        );
    }

    /**
     * @param Request $request
     * @param \TomatoPHP\TomatoCms\Models\Portfolio $model
     * @return RedirectResponse|JsonResponse
     */
    public function update(Request $request, \TomatoPHP\TomatoCms\Models\Portfolio $model): RedirectResponse|JsonResponse
    {
        $response = Tomato::update(
            request: $request,
            model: $model,
            validation: [
                'service_id' => 'nullable|exists:services,id',
                'title' => 'sometimes|array',
                'title*' => 'sometimes|max:255|string',
                'short_description' => 'nullable|array',
                'short_description*' => 'nullable|max:255|string',
                'keywords' => 'nullable|array',
                'keywords*' => 'nullable|max:65535',
                'company' => 'nullable|array',
                'company*' => 'nullable|max:255|string',
                'body' => 'nullable|array',
                'body*' => 'nullable',
                'activated' => 'sometimes'
            ],
            message: __('Portfolio updated successfully'),
            redirect: 'admin.portfolios.index',
            hasMedia: true,
            collection: [
                "feature" => false,
                "images" => true
            ]
        );

         if($response instanceof JsonResponse){
             return $response;
         }

         return $response->redirect;
    }

    /**
     * @param \TomatoPHP\TomatoCms\Models\Portfolio $model
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(\TomatoPHP\TomatoCms\Models\Portfolio $model): RedirectResponse|JsonResponse
    {
        $response = Tomato::destroy(
            model: $model,
            message: __('Portfolio deleted successfully'),
            redirect: 'admin.portfolios.index',
            hasMedia: true,
            collection: [
                "feature" => false,
                "images" => true
            ]
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    public function scan(){
        if(config('tomato-cms.behance_username') && config('tomato-cms.behance_service_id')){
            $projects = \TomatoPHP\TomatoCms\Models\Portfolio::all();
            foreach ($projects as $project){
                $project->clearMediaCollection('feature');
                $project->clearMediaCollection('images');
            }
            \TomatoPHP\TomatoCms\Models\Portfolio::truncate();
            dispatch(new BeJob(config('tomato-cms.behance_username')));

            \Toast::success(__('Your Scan Has Been Start Success'))->autoDismiss(2);
            return redirect()->back();
        }
        else {
            \Toast::danger(__('Add your username to config first'))->autoDismiss(2);
            return redirect()->back();
        }
    }
}
