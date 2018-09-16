<?php

namespace LaravelEnso\Discussions\app\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class TaggableUsers implements Responsable
{
    private $query = null;

    public function toResponse($request)
    {
        return $this->query()
            ->filter($request->get('query'))
            ->get();
    }

    private function get()
    {
        return $this->query
            ->with(['avatar'])
            ->get(['id', 'first_name', 'last_name']);
    }

    private function query()
    {
        $this->query = config('auth.providers.users.model')
            ::where('id', '<>', auth()->user()->id)
            ->limit(5);

        return $this;
    }

    private function filter($queryString)
    {
        collect(explode(' ', $queryString))
            ->each(function ($argument) {
                $this->query->where(function ($query) use ($argument) {
                    $query->where('first_name', 'like', '%'.$argument.'%')
                        ->orWhere('last_name', 'like', '%'.$argument.'%');
                });
            });

        return $this;
    }
}
