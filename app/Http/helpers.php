<?php
if (!function_exists('apiSuccess')) {
    /**
     * Tag json回传
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param String $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    function apiSuccess(String $message, array $data = [])
    {
        $json = [
            'code' => 0,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($json);
    }
}

if (!function_exists('apiError')) {
    /**
     * Tag json回传
     *
     * Users Flying Oranges
     * CreateTime 2018/7/19
     * @param String $message
     * @param array $data
     * @param integer code
     * @return \Illuminate\Http\JsonResponse
     */
    function apiError(String $message, array $data = [], $code = 1)
    {
        $json = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($json);
    }
}