<?php

namespace App\Http\Controllers;

use App\Traits\JsonRespondController;

class ApiController extends Controller
{
    use JsonRespondController;

    protected int $limitPerPage = 10;

    protected string $sort = 'created_at';

    protected ?string $withParameter = null;

    protected string $sortDirection = 'asc';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->has('sort')) {
                $this->setSortCriteria($request->input('sort'));

                if (empty($this->getSortCriteria())) {
                    return $this->setHTTPStatusCode(400)
                        ->setErrorCode(39)
                        ->respondWithError();
                }
            }

            if ($request->has('limit')) {
                if ($request->input('limit') > config('api.max_limit_per_page')) {
                    return $this->setHTTPStatusCode(400)
                        ->setErrorCode(30)
                        ->respondWithError();
                }

                $this->setLimitPerPage($request->input('limit'));
            }

            if ($request->has('with')) {
                $this->setWithParameter($request->input('with'));
            }

            return $next($request);
        });
    }

    public function setWithParameter(string $with): static
    {
        $this->withParameter = $with;

        return $this;
    }

    public function getLimitPerPage(): int
    {
        return $this->limitPerPage;
    }

    public function setLimitPerPage(int $limit): static
    {
        $this->limitPerPage = $limit;

        return $this;
    }

    public function getSortCriteria(): string
    {
        return $this->sort;
    }

    public function setSortCriteria(string $criteria): static
    {
        $acceptedCriteria = [
            'created_at',
            'updated_at',
            '-created_at',
            '-updated_at',
        ];

        if (in_array($criteria, $acceptedCriteria)) {
            $this->setSQLOrderByQuery($criteria);

            return $this;
        }

        $this->sort = '';

        return $this;
    }

    /**
     * Set both the column and order necessary to perform an orderBy.
     */
    public function setSQLOrderByQuery($criteria): void
    {
        $this->sortDirection = $criteria[0] == '-' ? 'desc' : 'asc';
        $this->sort = ltrim($criteria, '-');
    }
}
