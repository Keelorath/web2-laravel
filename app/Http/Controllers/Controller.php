<?php

namespace App\Http\Controllers;

use Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    private const FILLER = [null, null];

    public function getMiddleware()
    {
        return array_map(self::class . '::mapMiddleware', $this->middleware);
    }

    protected function attachAll(
        Model $model,
        FormRequest $request,
        string $name
    ) {
        /** @var BelongsToMany $relation */
        $relation = $model->{$name}();
        foreach ($request->get($name, []) as $id) {
            $relation->attach($id);
        }
    }

    protected function reattachAll(
        Model $model,
        FormRequest $request,
        string $name
    ) {
        $model->{$name}()->detach();
        $this->attachAll($model, $request, $name);
    }

    public static function mapMiddleware($middleware): array
    {
        if (\is_array($middleware)) {
            return $middleware;
        }

        [$middleware, $rest] =
            array_replace(self::FILLER, explode('|', $middleware, 2));
        $options = iterator_to_array(self::buildOptions($rest));
        return compact('middleware', 'options');
    }

    private static function buildOptions(?string $options): Generator
    {
        if ($options === null) {
            return;
        }

        foreach (explode('|', $options) as $string) {
            [$key, $value] =
                array_replace(self::FILLER, explode(':', $string, 2));
            yield $key => explode(',', $value);
        }
    }
}
