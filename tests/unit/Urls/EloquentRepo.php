<?php

use \Mockery as M;

class EloquentRepo extends BaseTest {

	private function get_mock_slugifier()
	{
		return M::mock('CoandaCMS\Coanda\Urls\Slugifier');
	}

	private function get_real_slugifier()
	{
		return new CoandaCMS\Coanda\Urls\Slugifier;
	}

	private function get_mock_url_model()
	{
		return M::mock('CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url');
	}

	private function get_mock_db()
	{
		return M::mock('Illuminate\Database\DatabaseManager');
	}

	public function test_can_instantiate()
	{
		// Make a repo
		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($this->get_mock_url_model(), $this->get_mock_slugifier(), $this->get_mock_db());

		// Check the repo is correct
		$this->assertInstanceOf('CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository', $repo);
	}

	public function test_find_by_id()
	{
		$model = $this->get_mock_url_model();

		// How the model should act
		$model->shouldReceive('find')->once()->andReturn(true);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_mock_slugifier(), $this->get_mock_db());
		$repo->findById(1);
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound
	 */
	public function test_find_by_id_failed()
	{
		$model = $this->get_mock_url_model();

		// How the model should act - should return false to simulate not being found
		$model->shouldReceive('find')->once()->andReturn(false);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_mock_slugifier(), $this->get_mock_db());
		$repo->findById(1);
	}

	public function test_find_by_slug()
	{
		$model = $this->get_mock_url_model();
	
		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn(true);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_mock_slugifier(), $this->get_mock_db());
		$repo->findBySlug('this-should-be-found');
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound
	 */
	public function test_find_by_slug_failed()
	{
		$model = $this->get_mock_url_model();
	
		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn(false);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_mock_slugifier(), $this->get_mock_db());
		$repo->findBySlug('this-should-not-be-found');
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug
	 */
	public function test_register_invalid_slug()
	{
		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($this->get_mock_url_model(), $this->get_real_slugifier(), $this->get_mock_db());
		$repo->register('throw exception because invalid', 'something', 1);
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists
	 */
	public function test_register_url_in_use()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn($model);
		$model->shouldReceive('getAttribute')->with('urlable_type')->andReturn('something');
		$model->shouldReceive('getAttribute')->with('urlable_id')->andReturn(1);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$repo->register('this-will-already-be-in-used', 'somethingdifferent', 1);
	}

	public function test_register_url_already_registered_for_specified_type_and_id()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn($model);
		$model->shouldReceive('getAttribute')->with('urlable_type')->andReturn('something');
		$model->shouldReceive('getAttribute')->with('urlable_id')->andReturn(1);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$result = $repo->register('url-already-set', 'something', 1);

		$this->assertEquals($model, $result);
	}

	public function test_register_url_which_is_currently_a_redirect()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn($model);
		$model->shouldReceive('getAttribute')->with('urlable_type')->andReturn('redirect');
		$model->shouldReceive('getAttribute')->with('urlable_id')->andReturn(9999);

		$model->shouldReceive('whereUrlableType')->andReturn($model);
		$model->shouldReceive('whereUrlableId')->andReturn($model);
		$model->shouldReceive('first')->andReturn(false);

		// The details should now be set and saved
		$model->shouldReceive('setAttribute')->with('urlable_type', 'something');
		$model->shouldReceive('setAttribute')->with('urlable_id', 1);
		$model->shouldReceive('save')->once();

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$result = $repo->register('url-used-by-redirect', 'something', 1);
	}

	public function test_register_url_which_is_not_used_at_all()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn(false);

		$model->shouldReceive('whereUrlableType')->andReturn($model);
		$model->shouldReceive('whereUrlableId')->andReturn($model);
		$model->shouldReceive('first')->andReturn(false);

		$new_model = $this->get_mock_url_model();
		$new_model->shouldReceive('setAttribute')->with('urlable_type', 'something');
		$new_model->shouldReceive('setAttribute')->with('urlable_id', 1);
		$new_model->shouldReceive('save')->once();

		$model->shouldReceive('create')->andReturn($new_model);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$result = $repo->register('url-used-by-redirect', 'something', 1);

		// Did I get the new model back?
		$this->assertEquals($new_model, $result);
	}

	public function test_register_url_where_we_already_have_it_but_with_different_slug()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->once()->andReturn($model);
		$model->shouldReceive('first')->once()->andReturn(false);

		$current_url = $this->get_mock_url_model();

		$model->shouldReceive('whereUrlableType')->andReturn($model);
		$model->shouldReceive('whereUrlableId')->andReturn($model);
		$model->shouldReceive('first')->andReturn($current_url);

		$new_model = $this->get_mock_url_model();
		$new_model->shouldReceive('setAttribute')->with('urlable_type', 'something');
		$new_model->shouldReceive('setAttribute')->with('urlable_id', 1);
		$new_model->shouldReceive('save')->once();

		$model->shouldReceive('create')->andReturn($new_model);

		$current_url->shouldReceive('getAttribute')->with('slug')->andReturn('something-different');

		$model->shouldReceive('where')->andReturn($model);

		$db = $this->get_mock_db();
		$db->shouldReceive('raw')->andReturn('fake SQL');

		$model->shouldReceive('update');

		$current_url->shouldReceive('setAttribute')->with('urlable_type', 'wildcard');

		$new_model->shouldReceive('getAttribute')->with('id')->andReturn(69);

		$current_url->shouldReceive('setAttribute')->with('urlable_id', 69);
		$current_url->shouldReceive('save');

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $db);
		$result = $repo->register('url-used-by-redirect', 'something', 1);

		// Did I get the new model back?
		$this->assertEquals($new_model, $result);
	}

	public function test_delete_for()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereUrlableType')->andReturn($model);
		$model->shouldReceive('whereUrlableId')->andReturn($model);
		$model->shouldReceive('first')->andReturn($model);
		$model->shouldReceive('delete');

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$repo->delete('something', 1);
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug
	 */
	public function test_can_use_invalid_slug()
	{
		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($this->get_mock_url_model(), $this->get_real_slugifier(), $this->get_mock_db());
		$repo->canUse('throw exception because invalid', 'something', 1);
	}

	/**
	 * @expectedException CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists
	 */
	public function test_can_use_already_exists()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->andReturn($model);
		$model->shouldReceive('first')->andReturn($model);

		$model->shouldReceive('getAttribute')->with('urlable_type')->andReturn('something-different');
		$model->shouldReceive('getAttribute')->with('urlable_id')->andReturn(9999);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$repo->canUse('already-exists', 'something', 1);
	}

	public function test_can_use_already_in_use_for_requested()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->andReturn($model);
		$model->shouldReceive('first')->andReturn($model);

		$model->shouldReceive('getAttribute')->with('urlable_type')->andReturn('something');
		$model->shouldReceive('getAttribute')->with('urlable_id')->andReturn(1);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$this->assertTrue($repo->canUse('already-setup', 'something', 1));
	}

	public function test_can_use_already_in_use_by_redirect()
	{
		$model = $this->get_mock_url_model();

		$model->shouldReceive('whereSlug')->andReturn($model);
		$model->shouldReceive('first')->andReturn($model);

		$model->shouldReceive('getAttribute')->with('urlable_type')->andReturn('redirect');
		$model->shouldReceive('getAttribute')->with('urlable_id')->andReturn(9999);

		$repo = new \CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository($model, $this->get_real_slugifier(), $this->get_mock_db());
		$this->assertTrue($repo->canUse('want-to-use', 'something', 1));
	}
}