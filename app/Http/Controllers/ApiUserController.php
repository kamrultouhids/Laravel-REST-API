<?php

namespace App\Http\Controllers;

use JWTAuth;

use App\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{

    public function index()
    {
        $user = User::get();
        if(count($user) > 0) {
            return response()->json([
                'code'      => 200,
                'response'  => 'success',
                'data'      => $user
            ]);
        }
		
        return response()->json([
            'code'      => 200,
            'response'  => 'Data Not found',
            'data'      => []
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required|unique:users,email',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'      => 422,
                'response'  => 'Validation error',
                'errors'     => apiValidateError($validator->errors())
            ]);
        }

        $request->request->add(['password' => Hash::make($request->password)]);

        try{
            $result = User::create($request->all());
            return response()->json([
                'code'      => 201,
                'response'  => 'User successfully saved',
                'data'      => $result
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'code'      => 404,
                'response'  => 'error',
                'errors'    => ['massage' => 'User not saved,something error found.']
            ]);
        }
    }


    public function edit($id)
    {
        $editModeData = user::FindOrFail($id);
        if( $editModeData ){
            return response()->json([
                'code'      => 200,
                'response'  => 'success',
                'data'      => $editModeData
            ]);
        }
		
        return response()->json([
            'code'      => 200,
            'response'  => 'Data Not found',
            'data'      => $editModeData
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 =>'required|unique:users,email,'.$id.',id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'      => 422,
                'response'  => 'Validation error',
                'errors'     => apiValidateError($validator->errors())
            ]);
        }

        $data = user::FindOrFail($id);
        try{
            $data->update($request->all());
            return response()->json([
                'code'      => 201,
                'response'  => 'User successfully updated.',
                'data'      => $data
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'code'      => 404,
                'response'  => 'error',
                'errors'    => ['massage' => 'User not update,something error found.']
            ]);
        }
    }


    public function destroy($id)
    {
        try{
            $user = User::FindOrFail($id);
            $user->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if( $bug == 0 ){
            return response()->json([
                'code'      => 200,
                'response'  => 'User delete successfully.',
                'data'      => []
            ]);
        }elseif ($bug == 1451 ) {
            return response()->json([
                'code'      => 404,
                'response'  => 'error',
                'errors'    => ['massage' => 'Cannot delete a parent data,this data is used anywhere.']
            ]);
        } else {
            return response()->json([
                'code'      => 404,
                'response'  => 'error',
                'errors'    => ['massage' => 'User not deleted,something error found.']
            ]);
        }
    }

}
