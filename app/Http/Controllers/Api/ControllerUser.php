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
use Illuminate\Support\Facades\DB;

class ControllerUser extends Controller
{
    private $user;
    private $photo;
    private $about;

    public function __construct(User $user)
    {
        $this->user  = $user;
        $this->photo = App::make("App\Photo"); 
        $this->about = App::make("App\AboutUser");
    }

    public function index()
    {
        $query = $this->user->query();

        $query->with('aboutUser');
        $query->with(['profilePhoto' => function($query){
            $query->where('is_profile', 1)->select(['name', 'path', 'type', 'description', 'tags']);
        }]);
        $query->with(['photos' => function($query){
            $query->select(['name', 'path', 'type', 'description', 'tags']);
        }]);
        $query->isActived();

        $user = $query->paginate(5);
        return response()->json([
            $user
        ]);
    }
    public function search($id)
    {
        
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
            DB::beginTransaction();
            // Assigning values to the user
            $this->user->name       = $user['user']['name'];
            $this->user->email      = $user['user']['email'];
            $this->user->password   = bcrypt($user['user']['password']);
            $this->user->is_actived = 1;
            $this->user->deleted_at = null;

            if($this->user->save())
            {
                if(isset($user['aboutUser']))
                {
                    $aboutUser = $user['aboutUser'];
                    $this->about->about        = $aboutUser['about'];
                    $this->about->phone        = $aboutUser['phone'];
                    $this->about->mobile_phone = $aboutUser['mobilePhone'];
                    $this->about->birthday     = date('Y-m-d', strtotime($aboutUser['birthday']));
                    $this->about->user_id      = $this->user->id;

                    $this->about->save();
                }
                if(isset($user['photo']))
                {
                    $userPhoto = $user['photo'];                            
                    $this->photo->name        = "user_{$this->user->name}_" . Carbon::today();
                    // HARDCODED
                    $this->photo->path        = "/photos/users/photo_user_{$this->user->name}_".Carbon::today();
                    //
                    $this->photo->type        = $userPhoto['type'];
                    $this->photo->description = $userPhoto['description'];
                    $this->photo->tags        = $userPhoto['tags'];

                    if($this->photo->save())
                    {
                        $photoProfile = $user['photoProfile'];
                        $this->user->photos()->attach($this->photo->id);
                        $this->user->profilePhoto()->attach($this->photo->id, ["is_profile" => $photoProfile['is_profile']]);   
                    }
                    
                }
            }

            DB::commit();
            return response()->json([
                'msg' => "User created with success!"
            ]);
            
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json([
                "line"  => $e->getLine(),
                'error' => $e->getMessage(), 
                'file' => $e->getFile()
            ]);
        }
    }
    public function update($id, Request $request)
    {

    }
    public function delete($id)
    {
       
    }

}
