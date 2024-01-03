<?php

namespace App\Http\Controllers;

use App\Services\GenericService;
use App\Http\Requests\GenericRequest;
use App\Http\Resources\GenericResource;

class GenericController extends ApiController
{
    public function __construct(
        protected GenericRequest $request,
        protected GenericResource $resource,
        protected GenericService $service
    ) {
    }

    /**
     * Retrieve all resource items.
     *
     * @return Response A response containing a collection of resources and a success message.
     */
    public function getAll()
    {
        $items = $this->service->getAll();
        return $this->successResponse(
            $this->toResource($items, $this->resource::class),
            __('messages.dataFetchedSuccessfully')
        );
    }

    /**
     * Retrieve a resource item by its ID.
     *
     * @param int $modelId The ID of the resource to retrieve.
     * @return Response A response containing the retrieved resource and a success message.
     */
    public function findById($modelId)
    {
        $model = $this->service->findById($modelId);

        return $this->successResponse(
            $this->toResource($model, $this->resource::class),
            __('messages.dataFetchedSuccessfully')
        );
    }

    /**
     * Store a new resource.
     *
     * @return Response A response containing the newly created resource and a success message.
     */
    public function store()
    {
        $validatedData = request()->validate($this->request->rules());
        $model = $this->service->store($validatedData);

        return $this->successResponse(
            $this->toResource($model, $this->resource::class),
            __('messages.dataAddedSuccessfully')
        );
    }

    /**
     * Store multiple resources.
     *
     * @return Response A response containing a collection of newly created resources and a success message.
     */
    public function bulkStore()
    {
        $validatedData = request()->validate($this->request->rules());
        $items = $this->service->bulkStore($validatedData);
        return $this->successResponse(
            $this->toResource($items, $this->resource::class),
            __('messages.dataAddedSuccessfully')
        );
    }

    /**
     * Update an existing resource by its ID.
     *
     * @param int $modelId The ID of the resource to update.
     * @return Response A response containing the updated resource and a success message.
     */
    public function update($modelId)
    {
        $validatedData = request()->validate($this->request->rules());
        $model = $this->service->update($validatedData, $modelId);

        return $this->successResponse(
            $this->toResource($model, $this->resource::class),
            __('messages.dataUpdatedSuccessfully')
        );
    }

    /**
     * Delete a resource by its ID.
     *
     * @param int $modelId The ID of the resource to delete.
     * @return Response A response indicating the successful deletion of the resource.
     */
    public function delete($modelId)
    {
        $this->service->delete($modelId);

        return $this->successResponse(
            null,
            __('messages.dataDeletedSuccessfully')
        );
    }

    /**
     * Delete multiple resources.
     *
     * @return Response A response indicating the successful deletion of the resource.
     */
    public function bulkDelete()
    {
        $validatedData = request()->validate($this->request->rules());
        $this->service->bulkDelete($validatedData);

        return $this->successResponse(
            null,
            __('messages.dataDeletedSuccessfully')
        );
    }
}
