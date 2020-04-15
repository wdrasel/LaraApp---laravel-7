<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */


    public function index()
    {
        //$this->authorize('isAdmin');
        if (Gate::allows('isAdmin') || Gate::allows('isAuthor')) {
            // The current user can edit settings
         return User::latest()->paginate(10);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
           'name'     => 'required|string|max:191',
           'email'     => 'required|string|email|max:191|unique:users',
           'password'     => 'required|string|min:6',
        ]);
       return  User::create([
           'name' => $request['name'],
           'email' => $request['email'],
           'type' => $request['type'],
           'bio' => $request['bio'],
           'photo' => $request['photo'],
           'password' => Hash::make($request['password']),
       ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile()
    {
        return auth('api')->user();
    }


    public function updateProfile(Request $request)
    {
        $user =  auth('api')->user();
        $request->validate([
            'name'     => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'     => 'sometimes|required|min:6',
         ]);

        $currentPhoto = $user->photo;
        if($request->photo != $currentPhoto)
        {
            $name = time().'.'.explode('/', explode(':', substr($request->photo, 0, strpos
                ($request->photo, ';')))[1] )[1];

            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);
        };

        $userPhoto = public_path('img/profile/').$currentPhoto;

        if(file_exists($userPhoto)){
                @unlink($userPhoto);
        }

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request ['password'])]);
        }

        $user->update($request->all());

        return ["messge" => " success Update Profile"];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'     => 'sometimes|string|min:6',
         ]);

        $user->update($request->all());

        return ['message' => 'update successfully'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);

        $user->delete();
        return ['meaasge'=> 'user deleted'];


    }

    public function search(){
        if($search = \Request::get('q')){
            $user = User::where(function($query) use ($search){
                $query->where('name', 'LIKE' , "%$search%")
                ->orWhere('email','LIKE',"%$search%")
                ->orWhere('type','LIKE', "%$search%");
            })->paginate(10);


        }else{
            $user = User::latest()->paginate(5);
        }

        return $user;


    }

}
