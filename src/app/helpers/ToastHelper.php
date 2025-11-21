<?php



class Toast
{
    /**
     * Set success toast
     */
    public static function success($message, $title = null)
    {
        $data = $title ? ['title' => $title, 'message' => $message] : $message;
        echo "$message";
        Session::flash('success', $data);
    }

    /**
     * Set error toast
     */
    public static function error($message, $title = null)
    {
        $data = $title ? ['title' => $title, 'message' => $message] : $message;
        Session::flash('error', $data);
    }

    /**
     * Set warning toast
     */
    public static function warning($message, $title = null)
    {
        $data = $title ? ['title' => $title, 'message' => $message] : $message;
        Session::flash('warning', $data);
    }

    /**
     * Set info toast
     */
    public static function info($message, $title = null)
    {
        $data = $title ? ['title' => $title, 'message' => $message] : $message;
        Session::flash('info', $data);
    }
}
