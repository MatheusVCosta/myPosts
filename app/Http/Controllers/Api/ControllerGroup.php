<?php

namespace App\Http\Controllers\Api;

use App\Group;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Exception;

class ControllerGroup extends Controller
{
    private $group;
    private $arrayIndex = ['key', 'operator', 'value'];
    private $filterParams = [];

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    
    /**
     * @method GET
     * @route /api/v1/group
     */
    public function index()
    {
        $query  = Group::query();
        $groups = $query->with(['creator' => function($q){
            return $q->select('id', 'name', 'email', 'mobile_phone');
        }])
        ->with(['users' => function($q){
            return $q->select('name', 'email');
        }])
        ->get();

        return response()->json([
            "data" => $groups
        ]);
    }

   
    /**
     * @method GET
     * @route /api/v1/group/search_group
     */
    public function searchBy(Request $request)
    {
        $query = $this->group->query();

        if($request->has('$filter'))
        {           
            $filter = $request->input('$filter');

            $filterExplode = explode(';', $filter);
            
            foreach($filterExplode as $filter)
            {
                $this->filterParams[] = array_combine($this->arrayIndex, explode(' ', $filter));
            }
            

            foreach($this->filterParams as $param)
            {
                $query->where(function($q) use($param){
                    return $q->where($param['key'], $param['operator'] , "%{$param['value']}%");
                });
            }
        } 

        $query->with(['creator' => function($q){
            return $q->select('id', 'name', 'email', 'mobile_phone');
        }])
        ->with(['users' => function($q){
            return $q->select('name', 'email')
            ->where('is_actived', 1);
        }]);
        $groups = $query->get();
        
        $count  = $query->count();

        return response()->json([
            'data' => [
                'message' => "Esses foram os grupos encontrados!",
                'count'   => $count,
                'result'  => $groups,
            ]
        ]);
    }
    /**
     * @method Post
     * @route /api/v1/group/
     */
    public function create(Request $request)
    {
        $data = $request->all();

        if(empty($data))
        {
            return response()->json([
                'message' => 'Para criar um novo grupo precisa preencher todos campos!'
            ]);
        }
        try
        {
            $user = User::where('id', '=', $data['created_by'])->first();
            if(empty($user))
            {
                return response()->json([
                    'message' => 'Usuário não encontrado'
                ], 404);
            }
            $this->group->create($data);
            return response()->json([
                'message' => 'Seu grupo foi criado com sucesso!',
                'status'  => 'Create'
            ]);
        }
        catch(Exception $ex)
        {
            return response()->json([
                'error' => $ex->getMessage(),
            ]);
        }
    }
    /**
     * @method Update
     * @route /api/v1/group/{id}
     */
    public function update($id, Request $request)
    {
        $data = $request->all();
        try
        {
            $group = Group::findOrFail($id);
            $group->update($data);
            return response()->json([
                'message' => 'Seu grupo foi atualizado com sucesso!',
                'status'  => 'Updated'
            ]);
        }
        catch(Exception $ex)
        {
            return response()->json([
                'error' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * @method Post
     * @route /api/v1/group/addNewMember
     */
    public function addNewMember(Request $request)
    {
        $data = $request->all();
        try
        {
            $user = User::where('id', '=', $data['user_id'])->first();
            $group = Group::where('id', '=', $data['group_id'])->first();

            if(empty($user))
            {
                return response()->json([
                    'message' => 'User not found',
                ]);
                throw new Exception("User not found id -> {$data['user_id']}");
            }
            if(empty($group))
            {
                return response()->json([
                    'message' => 'Group not found',
                ]);
                throw new Exception("Group not found id -> {$data['group_id']}");  
            }

            if($group->users()->countMember($user->id) > 1)
            {
                return response()->json([
                    'message' => 'You are already in that group',
                ]);
                throw new Exception("This member alreay in that group");    
            }
            $group->users()->attach($user->id);
            
            return response()->json([
                'message' => "O {$user->name} agora faz parte do grupo ({$group->group_name})!",
            ]);
        }catch(Exception $ex)
        {
            return response()->json([
                'error' => $ex->getMessage()
            ]);
        }
    }

    /**
     * @method Post
     * @route /api/v1/group/removeMember/{group_id}/{user_id}
     */
    public function removeMember($group_id, $user_id)
    {
        try
        {
            $user  = User::where('id', $user_id)->first();
            $group = Group::where('id', $group_id)->first();

            if(!is_null($user) || !empty($user))
            {
                $status = $group->users()->detach($user->id);
                if($status)
                {
                    return response()->json([
                        'message' => 'Usuário removido do grupo'
                    ]);
                }
                return response()->json([
                    'message' => 'Houve um erro ao remover o usuário!'
                ]);
                
            }
        }catch(Exception $ex)
        {
            return response()->json([
                'error' => $ex->getMessage()
            ]);
        }
    }

    /**
     * @method Delete
     * @route /api/v1/group/{id}
     */
    public function delete($id)
    {
        try
        {
            $group = Group::findOrFail($id);
            $group->delete();

            return response()->json([
                'data' => [
                    'message' => 'Seu grupo foi removido com sucesso!',
                    'status'  => 'deleted'
                ]
            ]);
        }
        catch(Exception $ex)
        {
            return response()->json([
                'error' => $ex->getMessage(),
            ]);
        }
    }
}
