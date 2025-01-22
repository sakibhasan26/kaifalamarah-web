<?php
namespace App\Http\Helpers\Api;

class Helpers{
    public static function unauthorized($message = 'Something Went Wrong', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 401);
    }
    public static function success($message = null, $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 200);
    }
    public static function onlySuccess($message = null)
    {
        return response()->json(['message' => $message], 200);
    }

    public static function no_content($message = 'No Data Found')
    {
        return response()->json(['message' => $message, 'data' => null], 204);
    }

    public static function created($message = 'Data Created', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 201);
    }

    public static function updated($message = 'Data Updated', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 202);
    }

    public static function deleted($message = 'Data Deleted', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 202);
    }

    public static function error($message = 'Something Went Wrong', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 400);
    }


    public static function api_error($message = 'Something Went Wrong', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 400);
    }

    public static function validation($message = 'Invalid Submission', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 422);
    }
    public static function not_found($message = 'Not Found', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data], 404);
    }

    public static function maintenance($message = 'Something Went Wrong', $data = null)
    {
        return response()->json(['message' => $message,'data' => $data], 503);
    }
}
