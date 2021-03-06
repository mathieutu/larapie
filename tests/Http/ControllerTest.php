<?php

namespace LarapieTests\Http;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Larapie\Http\Controller;
use LarapieTests\TestCase;
use Mockery;

class ControllerTest extends TestCase
{
    private $responseFactory;

    private $request;

    private $config;

    private $container;

    public function setUp()
    {
        parent::setUp();
        $this->config = Mockery::mock(Repository::class);
        $this->request = Mockery::mock(Request::class);
        $this->container = Mockery::mock(Container::class);
        $this->responseFactory = Mockery::mock(ResponseFactory::class);

        $this->container->shouldReceive('make')->with('request')->andReturn($this->request);
    }

    public function testIndexWithSimpleResource()
    {
        $this->mockConfig(['resources' => ['model_stub' => ['model' => ModelStub::class]]]);
        $this->mockRouteName('model_stub.index');
        $this->mockJsonResponse($expected = 'all');

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->index();

        $this->assertSame($expected, $response);
    }

    public function testIndexWithNestedResource()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub'            => ['model' => ModelStub::class],
                'model_stub.model_stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.model_stub.index');
        $this->mockJsonResponse($expected = 'children');

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->index();

        $this->assertSame($expected, $response);
    }

    public function testShowWithSimpleResource()
    {
        $this->mockConfig(['resources' => ['model_stub' => ['model' => ModelStub::class]]]);
        $this->mockRouteName('model_stub.show');
        $this->mockJsonResponse($expected = Mockery::type(ModelStub::class));

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->show();

        $this->assertSame($expected, $response);
    }

    public function testShowWithNestedResource()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub'            => ['model' => ModelStub::class],
                'model_stub.model_stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.model_stub.show');
        $this->mockJsonResponse($expected = Mockery::type(ModelStub::class));

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->show();

        $this->assertSame($expected, $response);
    }

    public function testShowNotFound()
    {
        $this->mockConfig(['resources' => ['model_stub' => ['model' => NotFoundModelStub::class]]]);
        $this->mockRouteName('model_stub.show');
        $this->mockJsonResponse($expected = ['error' => 'Not Found'], 404);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->show();

        $this->assertSame($expected, $response);
    }

    public function testStoreWithSimpleResource()
    {
        $this->mockConfig(['resources' => ['model_stub' => ['model' => ModelStub::class]]]);
        $this->mockRouteName('model_stub.store');
        $this->mockJsonResponse($expected = 'new model', 201);
        $this->mockRequestAll([]);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->store();

        $this->assertSame($expected, $response);
    }

    public function testStoreWithNestedResource()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub'            => ['model' => ModelStub::class],
                'model_stub.model_stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.model_stub.store');
        $this->mockJsonResponse($expected = 'new model', 201);
        $this->mockRequestAll([]);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->store();

        $this->assertSame($expected, $response);
    }

    public function testStoreParentNotFound()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub'      => ['model' => NotFoundModelStub::class],
                'model_stub.stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.stub.store');
        $this->mockJsonResponse($expected = ['error' => 'Not Found'], 404);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->store();

        $this->assertSame($expected, $response);
    }

    public function testUpdateWithSimpleResource()
    {
        $this->mockConfig(['resources' => ['model_stub' => ['model' => ModelStub::class]]]);
        $this->mockRouteName('model_stub.update');
        $this->mockJsonResponse($expected = Mockery::type(ModelStub::class));
        $this->mockRequestAll([]);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->update();

        $this->assertSame($expected, $response);
    }

    public function testUpdateWithNestedResource()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub'            => ['model' => ModelStub::class],
                'model_stub.model_stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.model_stub.update');
        $this->mockJsonResponse($expected = Mockery::type(ModelStub::class));
        $this->mockRequestAll([]);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->update();

        $this->assertSame($expected, $response);
    }

    public function testUpdateParentNotFound()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub'      => ['model' => NotFoundModelStub::class],
                'model_stub.stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.stub.update');
        $this->mockJsonResponse($expected = ['error' => 'Not Found'], 404);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->update();

        $this->assertSame($expected, $response);
    }

    public function testUpdateNotFound()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub' => ['model' => NotFoundModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.update');
        $this->mockJsonResponse($expected = ['error' => 'Not Found'], 404);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->update();

        $this->assertSame($expected, $response);
    }

    public function testDestroyWithSimpleResource()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.destroy');
        $this->mockJsonResponse($expected = null, 204);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->destroy();

        $this->assertSame($expected, $response);
    }

    public function testDestroyWithMultipleResource()
    {
        $this->mockConfig([
            'resources' => [
                'stub'      => ['model' => ModelStub::class],
                'stub.model_stub' => ['model' => ModelStub::class],
            ],
        ]);

        $this->mockRouteName('stub.model_stub.destroy');
        $this->mockJsonResponse($expected = null, 204);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->destroy();

        $this->assertSame($expected, $response);
    }

    public function testDestroyNotFound()
    {
        $this->mockConfig([
            'resources' => [
                'model_stub' => ['model' => NotFoundModelStub::class],
            ],
        ]);

        $this->mockRouteName('model_stub.destroy');
        $this->mockJsonResponse($expected = ['error' => 'Not Found'], 404);

        $controller = new Controller($this->container, $this->config, $this->responseFactory);
        $response = $controller->destroy();

        $this->assertSame($expected, $response);
    }

    protected function mockConfig($config)
    {
        return $this->config->shouldReceive('get')->with('larapie')->andReturn($config);
    }

    protected function mockRouteName($name)
    {
        return $this->request->shouldReceive('route->getName')->withNoArgs()->once()->andReturn($name);
    }

    protected function mockJsonResponse($expected, $code = 200)
    {
        return $this->responseFactory->shouldReceive('json')->with($expected, $code)->once()->andReturn($expected);
    }

    protected function mockRequestAll($expected)
    {
        return $this->request->shouldReceive('all')->withNoArgs()->once()->andReturn($expected);
    }
}

class ModelStub
{
    public $model_stubs = 'children';

    public function model_stubs()
    {
        return new self;
    }

    public static function all()
    {
        return 'all';
    }

    public static function find()
    {
        return new self;
    }

    public static function create()
    {
        return 'new model';
    }

    public static function update()
    {
    }

    public static function delete()
    {
    }
}

class NotFoundModelStub
{
    public static function find()
    {
        return null;
    }
}
