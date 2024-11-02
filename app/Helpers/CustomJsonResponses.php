<?php

if (!function_exists('mdsJsonResponse')) {
    function mdsJsonResponse($message = '', $data, $status = 'success', $status_code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status_code);
    }
}
