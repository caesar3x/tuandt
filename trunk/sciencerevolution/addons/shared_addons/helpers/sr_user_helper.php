<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/16/13
 */
defined('BASEPATH') OR exit('No direct script access allowed.');
if(!function_exists('get_sr_user_name')){
    function get_sr_user_name($id = null)
    {
        $ci = &get_instance();
        if($id == null){
            $current_user = get_current_sr_user();
            if(empty($current_user)){
                return null;
            }
            $user_type = $current_user->user_type;
            if(!$user_type){
                return null;
            }
            if($user_type == 'personal'){
                $ci->load->model('sr_users/sr_private_profile_m');
                $profile = $ci->sr_private_profile_m->get_by('id',$current_user->profile_id);
                if(!$profile){
                    return null;
                }
                $name = $profile->first_name.' '.$profile->last_name;
                return $name;
            }
        }else{
            $ci->load->model('sr_users/sr_user_m');
            $user = $ci->sr_user_m->get_user($id);
            if(empty($user)){
                return null;
            }
            $user_type = $user->user_type;
            if(!$user_type){
                return null;
            }
            if($user_type == 'personal'){
                $ci->load->model('sr_users/sr_private_profile_m');
                $profile = $ci->sr_private_profile_m->get_by('id',$user->profile_id);
                if(!$profile){
                    return null;
                }
                $name = $profile->first_name.' '.$profile->last_name;
                return $name;
            }
        }
        return null;
    }
}