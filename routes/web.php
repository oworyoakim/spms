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
        $router->get('show', 'ObjectivesController@show');
        $router->get('achievements', 'OutputAchievementsController@index');
        $router->post('achievements', 'OutputAchievementsController@store');
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

    $router->group(['prefix' => 'output-indicator-targets'], function () use ($router) {
        $router->get('', 'OutputIndicatorTargetsController@index');
        $router->post('', 'OutputIndicatorTargetsController@store');
        $router->put('', 'OutputIndicatorTargetsController@update');
    });

    $router->group(['prefix' => 'output-achievements'], function () use ($router) {
        $router->get('', 'OutputAchievementsController@index');
        $router->post('', 'OutputAchievementsController@store');
        $router->put('', 'OutputAchievementsController@update');
    });

    $router->group(['prefix' => 'key-result-areas'], function () use ($router) {
        $router->get('', 'KeyResultAreaController@index');
        $router->post('', 'KeyResultAreaController@store');
        $router->put('', 'KeyResultAreaController@update');
        $router->get('show', 'KeyResultAreaController@show');
        $router->get('achievements', 'OutcomeAchievementsController@index');
        $router->post('achievements', 'OutcomeAchievementsController@store');
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

    $router->group(['prefix' => 'outcome-indicator-targets'], function () use ($router) {
        $router->get('', 'OutcomeIndicatorTargetsController@index');
        $router->post('', 'OutcomeIndicatorTargetsController@store');
        $router->put('', 'OutcomeIndicatorTargetsController@update');
    });

    $router->group(['prefix' => 'outcome-achievements'], function () use ($router) {
        $router->get('', 'OutcomeAchievementsController@index');
        $router->post('', 'OutcomeAchievementsController@store');
        $router->put('', 'OutcomeAchievementsController@update');
    });

    $router->group(['prefix' => 'work-plans'], function () use ($router) {
        $router->get('', 'WorkPlansController@index');
        $router->post('', 'WorkPlansController@store');
        $router->put('', 'WorkPlansController@update');
    });

    $router->group(['prefix' => 'activities'], function () use ($router) {
        $router->get('', 'ActivitiesController@index');
        $router->post('', 'ActivitiesController@store');
        $router->put('', 'ActivitiesController@update');
        $router->patch('hold', 'ActivitiesController@hold');
        $router->patch('unhold', 'ActivitiesController@unhold');
        $router->patch('complete', 'ActivitiesController@complete');
    });

    $router->group(['prefix' => 'stages'], function () use ($router) {
        $router->get('', 'StagesController@index');
        $router->post('', 'StagesController@store');
        $router->put('', 'StagesController@update');
    });

    $router->group(['prefix' => 'tasks'], function () use ($router) {
        $router->get('', 'TasksController@index');
        $router->post('', 'TasksController@store');
        $router->put('', 'TasksController@update');
        $router->patch('complete', 'TasksController@complete');
    });


    $router->group(['prefix' => 'directives_and_resolutions'], function () use ($router) {
        $router->get('', 'DirectivesAndResolutionsController@index');
        $router->post('', 'DirectivesAndResolutionsController@store');
        $router->put('', 'DirectivesAndResolutionsController@update');
    });

    $router->group(['prefix' => 'directives_and_resolutions_activities'], function () use ($router) {
        $router->get('', 'DirectivesAndResolutionsActivitiesController@index');
        $router->post('', 'DirectivesAndResolutionsActivitiesController@store');
        $router->put('', 'DirectivesAndResolutionsActivitiesController@update');
    });

    $router->group(['prefix' => 'directives_and_resolutions_outputs'], function () use ($router) {
        $router->get('', 'DirectivesAndResolutionsOutputsController@index');
        $router->post('', 'DirectivesAndResolutionsOutputsController@store');
        $router->put('', 'DirectivesAndResolutionsOutputsController@update');
    });
});
