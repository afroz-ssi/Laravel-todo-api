<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

use Validator;
use Exception;
use DB;
use Auth;

class PostController extends Controller
{

    public function CreatePost(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'errors' => $validator->errors(),
            ], 400);
        }
        // DB::beginTransaction();
        try {

            $post_created = Post::create([
                "user_id" => $req->user_id,
                "title" => $req->title,
                "description" => $req->description,
            ]);
            if ($post_created) {
                $data = Post::latest()->first();
                return response()->json([
                    'status' => 200,
                    'message' => 'Post created successfully',
                    'data' => $data,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()], 404);
        }
    }


    public function PostLists(Request $req)
    {
        try {
            try {
                $post_list = Post::with('user')->orderByDesc('posts.id')->get();
                if ($post_list->isEmpty()) {
                    return response()->json(['status' => 404, 'error' => "No records found!"], 404);
                }
                return response()->json(['status' => 200, 'data' => $post_list,], 200);
            } catch (\Exception $e) {
                $errorMessage = 'Database query error: ' . $e->getMessage();
                return response()->json(['status' => 500, 'error' => $errorMessage], 500);
            }
        } catch (\Exception $e) {
            $errorMessage = 'Unexpected error: ' . $e->getMessage();
            return response()->json(['error' => $errorMessage], 500);
        }
    }


    public function GetSinglePost(Request $req, $id)
    {
        try {
            try {
                $post_list = Post::with('user')
                ->where('user_id', $req->id)
                ->orderByDesc('posts.id')->get();
                if ($post_list->isEmpty()) {
                    return response()->json(['status' => 404, 'error' => "No records found!"], 404);
                }
                return response()->json(['status' => 200, 'data' => $post_list,], 200);
            } catch (\Exception $e) {
                $errorMessage = 'Database query error: ' . $e->getMessage();
                return response()->json(['status' => 500, 'error' => $errorMessage], 500);
            }
        } catch (\Exception $e) {
            $errorMessage = 'Unexpected error: ' . $e->getMessage();
            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function EditPost(Request $req)
    {
        $data = null;
        try {
            $data = Post::find($req->id);
            if ($data != null) {
                return response()->json(['status' => 200, 'data' => $data], 200);
            } else {
                return response()->json(['status' => 404, 'data' => $data, 'error' => "No record found!. "], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 404, 'data' => $data, 'error' => $th->getMessage()], 404);
        }
    }

    public function UpdatePost(Request $req, $id)
    {
        $data = null;
        try {
            $data = Post::find($req->id);
            if ($data != null) {
                $updated = $data->update($req->all());
                if ($updated) {
                    return response()->json(['status' => 200, 'data' => $data, 'message' => 'Post updated successfully.'], 200);
                }
                return response()->json(['status' => 404, 'error' => 'Failed to update!.'], 404);
            } else {
                return response()->json(['status' => 404, 'data' => $data, 'error' => "Record not found!."], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 404, 'data' => $data, 'error' => $th->getMessage()], 404);
        }
    }


    public function DeletePost(Request $req)
    {

        try {

            $data = Post::find($req->id);
            if ($data != null) {
                $deleted = $data->delete();
                if ($deleted) {
                    return response()->json(['status' => 200, 'data' => $data, 'message' => 'Post deleted successfully.'], 200);
                } else {
                    return response()->json(['status' => 404, 'error' => 'Failed to delete!.'], 404);
                }
            } else {
                return response()->json(['status' => 404, 'error' => "Record not found!."], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 404, 'data' => $data, 'error' => $th->getMessage()], 404);
        }
    }

    public function Logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logout successfully',
        ], 200);
    }
}
