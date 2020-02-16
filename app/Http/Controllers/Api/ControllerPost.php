<?php

namespace App\Http\Controllers\Api;

use App\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Carbon;

class ControllerPost extends Controller
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function index()
    {
        $postAll = $this->post->paginate(10);

        return $postAll;
    }
    
    public function create(Request $request)
    {
        $data = $request->all();
        
        try
        {
            $data['created_date'] = Carbon::today();

            
            $post = $this->post->create($data);

            return response()->json([
                'data' => [ 
                    'status'  => 'created',
                    'message' => 'Post criado com sucesso!'
                ]
            ], 200);
        }
        catch(Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
    public function update($id, Request $request)
    {

    }
    public function delete($id)
    {
        
    }
}
