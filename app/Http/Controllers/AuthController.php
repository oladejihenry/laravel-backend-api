<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string',
            'mobile' => 'required',
            'website_url' => 'required',
        ]);
        //Creates new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'mobile' => $data['mobile'],
            'website_url' => $data['website_url'],
        ]);
        //issues a token after creating an account and display it
        $token = $user->createToken('userToken')->plainTextToken;

        //Display all details of user created with the token
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        //Validates the request
        $data = $request->validate([
            'email' => 'required|email|max:191',
            'password' => 'required|string',
        ]);

        $user = User::where('email',$data['email'])->first();

        if(!$user || !Hash::check($data['password'], $user->password))
        {
            return response(['message' => 'Invalid User Details'], 401);
        }
        else{
            $token = $user->createToken('userTokenLogin')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token,
            ];

            return response($response, 200);
        }
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if($user){
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $request->website_url = $request->website_ur;
            $user->update();

            return response()->json(['message' => 'User Updated Successfully'], 200);
        }

        else{
            return response()->json(['message' => 'User Does Not Exist'], 404);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response(['message' => 'Logged Out']);
    }
    
}
