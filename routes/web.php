<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * @var Route $router
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->get('user', 'AccountController@getUser');

    $router->group(['prefix' => 'plans'], function () use ($router) {
        $router->get('', 'PlansController@index');
        $router->post('', 'PlansController@store');
        $router->put('', 'PlansController@update');
    });

    $router->group(['prefix' => 'objectives'], function () use ($router) {
        $router->get('', 'ObjectivesController@index');
        $router->post('', 'ObjectivesController@store');
        $router->put('', 'ObjectivesController@update');
    });

    $router->group(['prefix' => 'interventions'], function () use ($router) {
        $router->get('', 'InterventionsController@index');
        $router->post('', 'InterventionsController@store');
        $router->put('', 'InterventionsController@update');
    });

    $router->group(['prefix' => 'swots'], function () use ($router) {
        $router->get('', 'SwotsController@index');
        $router->post('', 'SwotsController@store');
        $router->put('', 'SwotsController@update');
    });

    $router->group(['prefix' => 'swot-categories'], function () use ($router) {
        $router->get('', 'SwotCategoriesController@index');
        $router->post('', 'SwotCategoriesController@store');
        $router->put('', 'SwotCategoriesController@update');
    });

    $router->group(['prefix' => 'outputs'], function () use ($router) {
        $router->get('', 'OutputsController@index');
        $router->post('', 'OutputsController@store');
        $router->put('', 'OutputsController@update');
    });

    $router->group(['prefix' => 'output-indicators'], function () use ($router) {
        $router->get('', 'OutputIndicatorsController@index');
        $router->post('', 'OutputIndicatorsController@store');
        $router->put('', 'OutputIndicatorsController@update');
    });

    $router->group(['prefix' => 'output-indicator-milestones'], function () use ($router) {
        $router->get('', 'OutputIndicatorMilestonesController@index');
        $router->post('', 'OutputIndicatorMilestonesController@store');
        $router->put('', 'OutputIndicatorMilestonesController@update');
    });

    $router->group(['prefix' => 'outcomes'], function () use ($router) {
        $router->get('', 'OutcomesController@index');
        $router->post('', 'OutcomesController@store');
        $router->put('', 'OutcomesController@update');
    });

    $router->group(['prefix' => 'outcome-indicators'], function () use ($router) {
        $router->get('', 'OutcomeIndicatorsController@index');
        $router->post('', 'OutcomeIndicatorsController@store');
        $router->put('', 'OutcomeIndicatorsController@update');
    });

    $router->group(['prefix' => 'outcome-indicator-milestones'], function () use ($router) {
        $router->get('', 'OutcomeIndicatorMilestonesController@index');
        $router->post('', 'OutcomeIndicatorMilestonesController@store');
        $router->put('', 'OutcomeIndicatorMilestonesController@update');
    });

    $router->group(['prefix' => 'activities'], function () use ($router) {
        $router->get('', 'ActivitiesController@index');
        $router->post('', 'ActivitiesController@store');
        $router->put('', 'ActivitiesController@update');
    });

    $router->group(['prefix' => 'tasks'], function () use ($router) {
        $router->get('', 'TasksController@index');
        $router->post('', 'TasksController@store');
        $router->put('', 'TasksController@update');
    });
});
