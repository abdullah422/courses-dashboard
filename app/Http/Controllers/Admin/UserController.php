<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Coupon;
use App\Models\Stage;
use App\Models\User;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_users')->only(['index']);
        $this->middleware('permission:create_users')->only(['create', 'store']);
        $this->middleware('permission:update_users')->only(['edit', 'update']);
        $this->middleware('permission:delete_users')->only(['delete', 'bulk_delete']);

    }// end of __construct

    public function index()
    {
        return view('admin.users.index');

    }// end of index

    public function data()
    {
        $users = User::where('type', 'user')->select();

        return DataTables::of($users)
            ->addColumn('record_select','admin.users.data_table.record_select')->
            editColumn('image', function (User $user) {
                return view('admin.users.data_table.image', compact('user'));
            })
            ->editColumn('created_at', function (User $user) {
                return $user->created_at->format('Y-m-d');
            })
            ->editColumn('gender', function (User $user) {
                if($user->gender=='male'){
                    return 'ذكر';
                }else{
                    return  'انثي';
                }
            })
            ->editColumn('stage', function (User $user) {
                $name = $user->stage->name;
                return view('admin.users.data_table.stage', compact('name'));
            })
            ->addColumn('actions', 'admin.users.data_table.actions')
            ->rawColumns(['record_select', 'actions'])
            ->toJson();

    }// end of data

    public function create()
    {
        $stages = Stage::all();
        return view('admin.users.create',compact('stages'));

    }// end of create


    public function test()
    {
       return 'test';

    }// end of create
    public function store(UserRequest $request)
    {
        $requestData = $request->validated();
        User::create([
            'name' =>$request->name,
            'email' =>$request->email,
            'phone' =>$request->phone,
            'type' =>'user',
            'gender' =>$request->gender,
            'password' =>bcrypt($request->password),
            'balance' =>$request->balance,
            'parent_phone' =>$request->parent_phone,
            'parent_name' =>$request->parent_name,
            'status' =>'1',
            'stage_id'=>$request->stage_id,
        ]);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('admin.users.index');

    }// end of store

    public function edit(User $user)
    {
        $stages = Stage::all();
        return view('admin.users.edit', compact('user','stages'));

    }// end of edit

    public function update(UserRequest $request, User $user)
    {
        //return $request;
       $user->update([
            'name' =>$request->name,
            'email' =>$request->email,
            'phone' =>$request->phone,
            'type' =>'user',
            'gender' =>$request->gender,
            'parent_phone' =>$request->parent_phone,
            'parent_name' =>$request->parent_name,
            'stage_id'=>$request->stage_id,
        ]);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('admin.users.index');

    }// end of update

    public function destroy(User $user)
    {
        $this->delete($user);
        session()->flash('success', __('site.deleted_successfully'));
        return response(__('site.deleted_successfully'));

    }// end of destroy

    public function bulkDelete()
    {
        foreach (json_decode(request()->record_ids) as $recordId) {

            $user = User::FindOrFail($recordId);
            $this->delete($user);

        }//end of for each

        session()->flash('success', __('site.deleted_successfully'));
        return response(__('site.deleted_successfully'));

    }// end of bulkDelete

    private function delete(User $user)
    {
        $user->delete();

    }// end of delete

}//end of controller
