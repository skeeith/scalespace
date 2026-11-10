<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleCollection;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Role repository instance.
     *
     * @var App\Repositories\RoleRepository
     */
    protected $roleRepository;

    /**
     * Create new instance of role controller.
     *
     * @param RoleRepository $roleRepository Role repository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = new RoleCollection($this->roleRepository->paginateWithFilters(request()));

        if (! $roles) {
            return response()->json([
                'message' => 'Failed to retrieve roles',
            ], 404);
        }

        return $roles;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // fields
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $role = $this->roleRepository->store($request)) {
            return response()->json([
                'message' => 'Failed to saved role',
            ], 500);
        }

        return response()->json([
            'message' => 'Role successfully saved',
            'role' => $role,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! $role = $this->roleRepository->findOrFail($id)) {
            return response()->json([
                'message' => 'Role does not exist',
            ], 404);
        }

        return response()->json([
            'message' => 'Role successfully retrieve',
            'role' => $role,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // fields
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $this->roleRepository->update($request, $id)) {
            return response()->json([
                'message' => 'Failed to update role',
            ], 500);
        }

        return response()->json([
            'message' => 'Role successfully updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->roleRepository->findOrFail($id)->delete()) {
            return response()->json([
                'message' => 'Failed to delete role',
            ], 404);
        }

        return response()->json([
            'message' => 'Role successfully deleted',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! $this->roleRepository->restore($id)) {
            return response()->json([
                'message' => 'Failed to restore role',
            ], 404);
        }

        return response()->json([
            'message' => 'Role successfully restored',
        ], 200);
    }

    /**
     * Forcefully remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDestroy($id)
    {
        if (! $this->roleRepository->forceDestroy($id)) {
            return response()->json([
                'message' => 'Failed to permanently delete role',
            ], 404);
        }

        return response()->json([
            'message' => 'Role successfully deleted permanently',
        ], 200);
    }

    /**
     * Search the specified data from the storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $roles = new RoleCollection($this->roleRepository->paginateWithFilters(request()));

        if (! $roles) {
            return response()->json([
                'message' => 'Failed to retrieve roles',
            ], 404);
        }

        return $roles;
    }

    /**
     * Retrieve all resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        if (! $roles = $this->roleRepository->allWithFilters(request())) {
            return response()->json([
                'response' => false,
                'message'  => 'Resources does not exist',
            ], 404);
        }

        return response()->json([
            'response' => true,
            'message'  => 'Resources successfully retrieve',
            'roles'    => $roles,
        ], 200);
    }

    /**
     * Retrieve admin assigned permissions.
     *
     * @param  int $id Admin ID
     * @return \Illuminate\Http\Response
     */
    public function getAssignedPermissions($id)
    {
        return response()->json([
            'message'             => 'Successfully retrieve resource',
            'assignedPermissions' => $this->roleRepository->getAssignedPermissions($id),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function togglePermission(Request $request)
    {
        if (! $this->roleRepository->togglePermission($request)) {
            return response()->json([
                'message'  => 'Failed to toggle permission',
            ], 404);
        }

        return response()->json([
            'message'  => 'Permission successfully toggled',
        ], 200);
    }
}
