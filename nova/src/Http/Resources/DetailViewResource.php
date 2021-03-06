<?php

namespace Laravel\Nova\Http\Resources;

use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;

class DetailViewResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceDetailRequest  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->authorizedResourceForRequest($request);

        $payload = with($resource->serializeForDetail($request, $resource), function ($detail) use ($request) {
            $detail['fields'] = collect($detail['fields'])
                ->when($request->viaResource, function ($fields) use ($request) {
                    return $fields->reject(function ($field) use ($request) {
                        if (! $field instanceof RelatableField) {
                            return false;
                        }

                        return $request->viaResource === $field->resourceName
                                || in_array($request->relationshipType, ['hasOne', 'morphOne']);
                    });
                })
                ->values()->all();

            return $detail;
        });

        return [
            'title' => (string) $resource->title(),
            'panels' => $resource->availablePanelsForDetail($request, $resource, FieldCollection::make($payload['fields'])),
            'resource' => $payload,
        ];
    }

    /**
     * Get authorized resource for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceDetailRequest  $request
     * @return \Laravel\Nova\Resource
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizedResourceForRequest(ResourceDetailRequest $request)
    {
        return tap($request->newResourceWith(
            tap($request->findModelQuery(), function ($query) use ($request) {
                $resource = $request->resource();
                $resource::detailQuery($request, $query);
            })->firstOrFail()
        ))->authorizeToView($request);
    }
}
