<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Photo;
use App\User;
use Carbon\Carbon as CarbonCarbon;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class ControllerUser extends Controller
{
    private $user;
    private $photo;

    public function __construct(User $user)
    {
        $this->user  = $user;
        $this->photo = App::make("App\Photo"); 
    }

    public function index()
    {
        $query = $this->user->where('is_actived', 1);
        
        $query->with(['photos' => function($query){
            $query->select('path', 'name', 'description');
        }]);
        $user = $query->paginate(10);

        return $user;
    }
    public function search($id)
    {

        try
        {
            $user = $this->user->findOrFail($id);
            $user->post;
           
            if($user->is_actived == 0)
            {
                return response()->json([
                    'messgae' => 'Usuário removido ou desativado'
                ], 200);
            }else{
                return response()->json([
                    'data'    => $user,
                    'messgae' => 'Usuário encontrado com sucesso!'
                ], 200);
            }
            
        }
        catch(Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
    public function create(Request $request)
    {
        if(!$request->has('user'))
        {
            throw new Exception("User data not informed");
        }
        $user = $request->all();
        try
        {   
            // Assigning values to the user
            $this->user->name       = $user['user']['name'];
            $this->user->email      = $user['user']['email'];
            $this->user->password   = bcrypt($user['user']['password']);
            $this->user->is_actived = 1;
            $this->user->deleted_at = null;

            if($this->user->save())
            {
                if(isset($user['photo']))
                {
                    $userPhoto = $user['photo'];                            
                    $this->photo->name        = "user_{$this->user->name}_" . Carbon::today();
                    $this->photo->path        = $userPhoto['path'];
                    $this->photo->type        = $userPhoto['type'];
                    $this->photo->description = $userPhoto['description'];
                    $this->photo->tags        = $userPhoto['tags'];

                    $this->photo->save();
                    
                    if(isset($user['photoProfile']))
                    {
                        $photoProfile = $user['photProfile'];
                        $user->photos()->attach($this->photo->id, $photoProfile['is_profile']);
                    }   
                }
            }
            
            return response()->json([
                'msg' => "User created with success!"
            ]);
            
        }
        catch(Exception $e)
        {
            return response()->json(['error' => $e->getMessage(), "line" => $e->getLine()], 401);
        }
    }
    public function update($id, Request $request)
    {
        $data = $request->all();
        
        try
        {
            $user = $this->user->findOrFail($id);

            $user->update($data);
            return response()->json([
                'data' => [
                    'status'  => 'updated',
                    'message' => 'Usuário atualizado com succeso!'
                ]
            ]);
        }
        catch(Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
    public function delete($id)
    {
        try
        {
            $data = [
                'deleted_at' => Carbon::today(), 
                'is_actived' => 0
            ];

           
            $user = $this->user->findOrFail($id);
            $user->update($data);
            return response()->json([
                'data' => [
                    'status' => 'Disabled',
                    'messge' => 'Usuário desativado com succeso!'
                ]
            ]);
        }catch(Exception $e)
        {

        }
    }

}
