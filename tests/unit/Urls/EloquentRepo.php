<?php

use \Mockery as M;

class EloquentRepo extends BaseTest {

	public function test_can_instantiate()
	{
		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);

		$this->assertInstanceOf('CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository', $repo);
	}

	public function test_find_by_id()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$model->shouldReceive('find')->once()->andReturn(true);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$repo->findById(1);
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound
	 */
	public function test_find_by_id_failed()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$model->shouldReceive('find')->once()->andReturn(false); // return false to simulate not being found

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$repo->findById(1);
	}

	public function test_find_by_slug()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$result_model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$result_model->shouldReceive('first')->once()->andReturn(true);

		$model->shouldReceive('whereSlug')->once()->andReturn($result_model);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$repo->findBySlug('this-should-be-found');
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound
	 */
	public function test_find_by_slug_failed()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$result_model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$result_model->shouldReceive('first')->once()->andReturn(false);

		$model->shouldReceive('whereSlug')->once()->andReturn($result_model);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$repo->findBySlug('this-should-be-found');
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug
	 */
	public function test_register_invalid()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');
		$slugifier->shouldReceive('validate')->once()->andReturn(false); // return that the slug is not valid

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$repo->register('this-will-throw-an-exception', 'something', 1);
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists
	 */
	public function test_register_url_in_use()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');
		$slugifier->shouldReceive('validate')->once()->andReturn(true);

		$result_model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$result_model->shouldReceive('first')->once()->andReturn($result_model);
		$result_model->shouldReceive('getAttribute')->times(2)->andReturn('something', 1);

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$model->shouldReceive('whereSlug')->once()->andReturn($result_model);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$repo->register('this-will-already-be-in-used', 'somethingdifferent', 1);
	}

	public function test_register_url_already_setup()
	{
		$slugifier = M::mock('CoandaCMS\Coanda\Urls\Slugifier');
		$slugifier->shouldReceive('validate')->once()->andReturn(true);

		$result_model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$result_model->shouldReceive('first')->once()->andReturn($result_model);
		$result_model->shouldReceive('getAttribute')->times(2)->andReturn('something', 1);

		$model = M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
		$model->shouldReceive('whereSlug')->once()->andReturn($result_model);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $slugifier);
		$this->assertTrue($repo->register('this-will-already-be-in-used', 'something', 1));
	}

	
}