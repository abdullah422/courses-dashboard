<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlaceRequest;
use App\Models\Apartment;
use App\Models\Place;
use App\Models\User;
use Yajra\DataTables\DataTables;

class PlaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_places')->only(['index']);
        $this->middleware('permission:create_places')->only(['create', 'store']);
        $this->middleware('permission:update_places')->only(['edit', 'update']);
        $this->middleware('permission:delete_places')->only(['delete', 'bulk_delete']);
    } // end of __construct

    public function index()
    {
        return view('admin.places.index');
    } // end of index

    public function data()
    {
        $places = Place:: whereNotIn('id',[1])->withCount('users')->get();
        return DataTables::of($places)
            ->addColumn('record_select', 'admin.places.data_table.record_select')
            ->editColumn('created_at', function (Place $place) {
                return $place->created_at->format('Y-m-d');
            })
            ->editColumn('users_count', function (Place $place) {
                return $place->users->count();
            })
            ->addColumn('actions', 'admin.places.data_table.actions')
            ->rawColumns(['record_select', 'actions', 'related_apartments'])
            ->toJson();
    } // end of data

    public function create()
    {
        return view('admin.places.create');
    } // end of create

    public function store(PlaceRequest $request)
    {
        Place::create($request->only(['name']));
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('admin.places.index');
    } // end of store

    public function edit(Place $place)
    {
        return view('admin.places.edit', compact('place'));
    } // end of edit

    public function update(PlaceRequest $request, Place $place)
    {
        $place->update($request->validated());

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('admin.places.index');
    } // end of update

    public function destroy(Place $place)
    {
        //$id = $place->id;
        $user = User::where('place_id', $place->id)->count();
        if ($user > 0) {

            session()->flash('error', __('site.can_not_place'));
            return response(__('site.can_not_place'));
        } else {

            $this->delete($place);
            session()->flash('success', __('site.deleted_successfully'));
            return response(__('site.deleted_successfully'));
        }
    } // end of destroy

    public function bulkDelete()
    {
        foreach (json_decode(request()->record_ids) as $recordId) {


            $user = User::where('place_id', $recordId)->count();
            if($user >  0){
                session()->flash('error', __('site.can_not_place'));
                return response(__('site.can_not_place'));
            }else {
                $place = Place::FindOrFail($recordId);
                $this->delete($place);

            }

        } //end of for each

        session()->flash('success', __('site.deleted_successfully'));
        return response(__('site.deleted_successfully'));
    } // end of bulkDelete

    private function delete(Place $place)
    {
        $place->delete();
    } // end of delete
}
