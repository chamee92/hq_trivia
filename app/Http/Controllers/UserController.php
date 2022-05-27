<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class UserController extends Controller
{
    public function autheticate(Request $request)
    {
    
        try {
            $data = json_decode($request->getContent(),true);
            $login_type = isset($data['login_type']) ? intval($data['login_type']) : 1;
            $mobile_number = isset($data['mobile_number']) ? $data['mobile_number'] : null; 
            $email_address = isset($data['email_address']) ? $data['email_address'] : null; 
            $password = isset($data['password']) ? $data['password'] : null; 
            $push_id = isset($data['push_id']) ? $data['push_id'] : null;
            $device_id = isset($data['device_id']) ? $data['device_id'] : null;
            $os_type = isset($data['os_type']) ? $data['os_type'] : 1;
            if($login_type == 1) {   
                //valid credential
                $validator = Validator::make($data, [
                    'mobile_number' => 'required',
                    'password' => 'required',
                    'login_type' => 'required'
                ]);

                //Send failed response if request is not valid
                if ($validator->fails()) {
                    $output['success'] = false;
                    $output['data'] = [];
                    $output['message'] = 'Invalid request';
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                }

                //Request is validated
                try {
                    $user_data = User::where('mobile_number', $data['mobile_number'])->first();
                    if(isset($user_data->id) && intval($user_data->id) > 0) {
                        $user_data->email  = $mobile_number; 
                        $user_data->password  = $user_data->normal_password; 
                        $user_data->push_id  = $push_id; 
                        $user_data->device_id  = $device_id; 
                        $user_data->os_type  = $os_type; 
                        $user_data->login_type = 1;
                        $user_data->save();
                    } else {
                        $output['success'] = false;
                        $output['data'] = [];
                        $output['message'] = 'User cannot idenitified. Please contact admin!';
                        return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                    }
                    $credentials['email'] = $mobile_number;
                    $credentials['password'] = $password;
                    if (! $token = JWTAuth::attempt($credentials)) {
                        $output['success'] = false;
                        $output['data'] = [];
                        $output['message'] = "Login credentials are invalid";
                        return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                    }
                } catch (JWTException $e) {
                    $output['success'] = false;
                    $output['data'] = null;
                    $output['message'] = "Could not create token";
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

                }
            
                //Token created, return with success response and jwt token
                $output['success'] = true;
                $output['data']['token'] = $token;
                $output['message'] = "User login successfully";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else if($login_type == 2 || $login_type == 3) {
                //valid credential
                $validator = Validator::make($data, [
                    'email_address' => 'required',
                    'login_type' => 'required'
                ]);
                //Send failed response if request is not valid
                if ($validator->fails()) {
                    $output['success'] = false;
                    $output['data'] = [];
                    //$output['message'] = $validator->messages();
                    $output['message'] = 'Invalid request';
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                }

                 //Request is validated
                 try {
                    $user_data = User::where('email_address', $email_address)->first();
                    $credentials['email'] = $email_address;
                    if(isset($user_data->id) && intval($user_data->id) > 0) {
                        $credentials['password'] = $user_data->first_name;
                        if($login_type == 2) {
                            $user_data->password  = $user_data->google_password;
                        } else if($login_type == 3) {
                            $user_data->password  = $user_data->facebook_password;
                        }    
                        $user_data->email  = $email_address;                   
                        $user_data->push_id  = $push_id; 
                        $user_data->device_id  = $device_id; 
                        $user_data->login_type = $login_type;
                        $user_data->save();
                    } else {
                        $output['success'] = false;
                        $output['data'] = [];
                        $output['message'] = 'User canot idenitified. Please contact admin!';
                        return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                    }
                    if (! $token = JWTAuth::attempt($credentials)) {
                        $output['success'] = false;
                        $output['data'] = $credentials;
                        $output['message'] = "Login credentials are invalid";
                        return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                    }
                } catch (JWTException $e) {
                    $output['success'] = false;
                    $output['data']['error'] = $e;
                    $output['message'] = "Could not create token";
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

                }
            
                //Token created, return with success response and jwt token
                $output['success'] = true;
                $output['data']['token'] = $token;
                $output['message'] = "User login successfully";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                
            } else {
                $output['success'] = false;
                $output['data'] = [];
                $output['message'] = "Login type not intergration";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data']['error'] = $e;
            $output['message'] = "Server error. Please contact admin";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_type_id' => 'required',
                'first_name' => 'required|string',
                'address1' => 'required|string',
                'address2' => 'required|string',
                'zip_code' => 'required|string'
            ]);
            if($validator->fails()){
                $validator1 = Validator::make($request->all(), [                 
                                'first_name' => 'required|string'
                            ]);
                $validator2 = Validator::make($request->all(), [          
                                'address1' => 'required|string'
                            ]);
                $validator3 = Validator::make($request->all(), [          
                                'address2' => 'required|string'
                            ]);
                $validator4 = Validator::make($request->all(), [          
                                'zip_code' => 'required|string'
                            ]);
                
                if($validator1->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your first name & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else if($validator2->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your address first field & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else if($validator3->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your address second field & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else if($validator4->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your zip code & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else {
                    $output['success'] = false;
                    $output['message'] = "Other data validation error, please check & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                }
            }
            //Validate data
            $data = $request->input();
            $login_type = isset($data['login_type']) ? intval($data['login_type']) : 0;
            $user_type_id = isset($data['user_type_id']) ? intval($data['user_type_id']) : 0;
            $first_name = isset($data['first_name']) ? $data['first_name'] : null;
            $last_name = isset($data['last_name']) ? $data['last_name'] : null;
            $mobile_number = isset($data['mobile_number']) ? $data['mobile_number'] : null;
            $email_address = isset($data['email_address']) ? $data['email_address'] : null;
            $address1 = isset($data['address1']) ? $data['address1'] : null;
            $address2 = isset($data['address2']) ? $data['address2'] : null;
            $zip_code = isset($data['zip_code']) ? $data['zip_code'] : null;
            $login_type = isset($data['login_type']) ? intval($data['login_type']) : 1;
            $file_extension = isset($data['file_extension']) ? $data['file_extension'] : null;
            $password = isset($data['password']) ? $data['password'] : $first_name;
            $push_id = isset($data['push_id']) ? $data['push_id'] : null;
            $device_id = isset($data['device_id']) ? $data['device_id'] : null;
            $os_type = isset($data['os_type']) ? $data['os_type'] : 1;
            $defaut_password = $first_name;
            $date_time = date('Y-m-d H:i:s');

            if($mobile_number != null) {
                $user_data = User::where('mobile_number', $mobile_number)->where('is_active', 1)->orderBy('id', 'DESC')->first();
                if(isset($user_data->id) && intval($user_data->id) > 0) {
                    $output['success'] = false;
                    $output['message'] = "You enter mobile number previousely used. Please login the system use your mobile number!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                }
            }
            if($email_address != null) {
                $user_data1 = User::where('email_address', $email_address)->where('is_active', 1)->orderBy('id', 'DESC')->first();
                if(isset($user_data1->id) && intval($user_data1->id) > 0) {
                    $output['success'] = false;
                    $output['message'] = "You enter email address previousely used. Please login the system use your email address!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                }
            }
            if($login_type == 1) {
                $email = $mobile_number;
            } else {
                $email = $email_address;
            }

            if($user_type_id <= 0 && $first_name == null && $address1 == null && $address2 == null && $zip_code == null ) {
                $output['success'] = false;
                $output['data'] = [];
                $output['message'] = 'Invalid request. Please check & try again..!';
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
            

            //file upload
            if($request->file()) {
                $ext = pathinfo($request->profile_picture->getClientOriginalName(), PATHINFO_EXTENSION);
                if($ext == null || $ext == ''){
                    $ext = $file_extension;
                }
                $file_name = time().'_'.$first_name . '.' . $ext;
                $file_path = $request->file('profile_picture')->storeAs('uploads/users', $file_name, 'public');
                $profile_picture = 'https://hq.docketapps.com/storage/'.$file_path;
            } else {
                $profile_picture = null;
            }

            $user = User::create([
                        'user_type_id' => $user_type_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'mobile_number' => $mobile_number,
                        'email_address' => $email_address,
                        'email' => $email,
                        'login_type' => $login_type,
                        'password' => bcrypt($password),
                        'normal_password' => bcrypt($password),
                        'google_password' => bcrypt($defaut_password),
                        'facebook_password' => bcrypt($defaut_password),
                        'push_id' => $push_id,
                        'device_id' => $device_id,
                        'os_type' => $os_type,
                        'address1' => $address1,
                        'address2' => $address2,
                        'zip_code' => $zip_code,
                        'profile_picture' => $profile_picture,
                        'file_extension' => $ext,
                        'earn_total' => 0,
                        'pending_withdraw_total' => 0,
                        'withdraw_total' => 0,
                        'earn_balance' => 0,
                        'earn_coin_total' => 0,
                        'pending_withdraw_coin_total' => 0,
                        'withdraw_coin_total' => 0,
                        'earn_coin_balance' => 0,
                        'is_active' => 1, 
                        'created_at' => $date_time,
                        'updated_at' => $date_time
                    ]);

            $output['success'] = true;
            $output['data']['user'] = $user;
            $output['message'] = "User registered successfully";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }

    public function getAuthenticatedUser(Request $request)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                $output['success'] = false;
                $output['message'] = "User not found.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $output['success'] = false;
            $output['message'] = "Token expired, please re-login.";
            $output['data'] = null;
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $output['success'] = false;
            $output['message'] = "Token invalid.";
            $output['data'] = null;
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            $output['success'] = false;
            $output['message'] = "Token absent.";
            $output['data'] = null;
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        }

        $output['success'] = true;
        $output['message'] = "User login successfully.";
        if($user->id > 0) {
            $auth_user = User::select('id AS user_id', 'user_type_id','first_name', 'last_name', 'email_address', 'mobile_number', 'login_type', 'push_id', 'device_id', 'os_type', 'created_at',
                                    'address1', 'address2', 'zip_code', 'profile_picture', 'file_extension', 'earn_total', 'pending_withdraw_total', 'withdraw_total', 'earn_balance', 'earn_coin_total', 
                                    'pending_withdraw_coin_total', 'withdraw_coin_total', 'earn_coin_balance', 'is_active', 'updated_at')
                                ->where('id', $user->id)->orderBy('id','DESC')->first();
            if(isset($auth_user->user_id) && intval($auth_user->user_id) > 0) {
                $output['data']['user_id'] = intval($auth_user->user_id);
                $output['data']['user_type_id']  = intval($auth_user->user_type_id);
                $output['data']['first_name']  = $auth_user->first_name;
                $output['data']['last_name']  = $auth_user->last_name;
                $output['data']['email_address']  = $auth_user->email_address;
                $output['data']['mobile_number']  = $auth_user->mobile_number;
                $output['data']['login_type']  = intval($auth_user->login_type);
                $output['data']['push_id']  = $auth_user->push_id;
                $output['data']['device_id']  = $auth_user->device_id;
                $output['data']['os_type']  = intval($auth_user->os_type); 
                $output['data']['address1']  = $auth_user->address1;
                $output['data']['address2']  = $auth_user->address2;
                $output['data']['zip_code']  = $auth_user->zip_code;
                $output['data']['profile_picture']  = $auth_user->profile_picture;      
                $output['data']['file_extension']  = $auth_user->file_extension; 
                $output['data']['earn_total']  = doubleval($auth_user->earn_total);   
                $output['data']['pending_withdraw_total']  = doubleval($auth_user->pending_withdraw_total);      
                $output['data']['withdraw_total']  = doubleval($auth_user->withdraw_total);   
                $output['data']['earn_balance']  = doubleval($auth_user->earn_balance);  
                $output['data']['earn_coin_total']  = doubleval($auth_user->earn_coin_total);       
                $output['data']['pending_withdraw_coin_total']  = doubleval($auth_user->pending_withdraw_coin_total);   
                $output['data']['withdraw_coin_total']  = doubleval($auth_user->withdraw_coin_total);   
                $output['data']['earn_coin_balance']  = doubleval($auth_user->earn_coin_balance);                   
                $output['data']['is_active']  = $auth_user->is_active;
                $output['data']['created_at']  = $auth_user->created_at;
                $output['data']['updated_at']  = $auth_user->updated_at;
            } else {
                $output['success'] = true;
                $output['message'] = "User not found. please contact admin.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
            
        } else {
            $output['success'] = false;
            $output['message'] = "Wrong user data. Please check & try again.";
            $output['data'] = null;
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        }
        return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

    }

    public function usersData(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_type_id = isset($data['user_type_id']) ? intval($data['user_type_id']) : 0;
            $user_id = isset($data['user_id'])? intval($data['user_id']) : 0;

            $sql = "SELECT `id` AS 'user_id', `user_type_id`, `first_name`, `last_name`, `mobile_number`, `email_address`,
                    `login_type`, `push_id`, `device_id`, `os_type`, `address1`, `address2`, `zip_code`, `profile_picture`, 
                    `file_extension`, `earn_total`, `pending_withdraw_total`, `withdraw_total`, `earn_balance`, `earn_coin_total`, 
                    `pending_withdraw_coin_total`, `withdraw_coin_total`, `earn_coin_balance`, `is_active`, `created_at`, `updated_at`
                    FROM `users`
                    WHERE (`is_active` = 1 OR  `is_active` = 0)";
            if($user_type_id > 0) {
                $sql .= " AND `user_type_id` = " . $user_type_id;
            }

            if($user_id != 0) {
                $sql .= " AND `id` = " . $user_id;
            }
            $sql .= " ORDER BY `id` DESC";

            $output['data'] = DB::select($sql);
            $output['success'] = true;
            $output['message'] = "Users data passed successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        } catch (Exception $e) {
            $output['success'] = false;
            $output['message'] = "Something went wrong please try again.";
            $output['data'] = null;
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        }    
    }

    public function logout(Request $request)
    {
        try {
            //valid credential
            $data = json_decode($request->getContent(),true);
            $validator = Validator::make($data, [
                'token' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Invalid request.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
            $token = $data['token'];
                 
            JWTAuth::invalidate($token);
            $output['success'] = true;
            $output['data'] = null;
            $output['message'] = "User has been logout successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }

    public function passwordChange(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $password = isset($data['user_id']) ? $data['password'] : null;
            $updated_at = date("Y-m-d H:i:s");

            if($user_id > 0 && $password != null ) {
                $user_data = User::where('id', $user_id)->orderBy('id', 'DESC')->first();
                $new_password = bcrypt($password);
                $user_data->password = $new_password;
                $user_data->normal_password = $new_password;
                $user_data->updated_at = $updated_at;
                $user_data->save();
                $output['success'] = true;
                $output['message'] = "User password change successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }

    public function userManage(Request $request)
    {
        try {
            $data = $request->input();
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $first_name = isset($data['first_name']) ? $data['first_name'] : null;
            $last_name = isset($data['last_name']) ? $data['last_name'] : null;
            $mobile_number = isset($data['mobile_number']) ? $data['mobile_number'] : null;
            $email_address = isset($data['email_address']) ? $data['email_address'] : null;
            $address1 = isset($data['address1']) ? $data['address1'] : null;
            $address2 = isset($data['address2']) ? $data['address2'] : null;
            $zip_code = isset($data['zip_code']) ? $data['zip_code'] : null;
            $file_extension = isset($data['file_extension']) ? $data['file_extension'] : null;
            $date_time = date('Y-m-d H:i:s');

            if($user_id > 0 && $first_name != null  && $address1 != null && $address2 != null && $zip_code != null) {
                //file upload
                if($request->file()) {
                    $ext = pathinfo($request->profile_picture->getClientOriginalName(), PATHINFO_EXTENSION);
                    if($ext == null || $ext == ''){
                        $ext = $file_extension;
                    }
                    $file_name = time().'_'.$first_name . '.' . $ext;
                    $file_path = $request->file('profile_picture')->storeAs('uploads/users', $file_name, 'public');
                    $profile_picture = 'https://hq.docketapps.com/storage/'.$file_path;
                } else {
                    $profile_picture = null;
                }

                $user_data = User::where('id', $user_id)->orderBy('id', 'DESC')->first();
                
                $user_data->first_name = $first_name;
                $user_data->last_name = $last_name;
                $user_data->email_address = $email_address;
                $user_data->mobile_number = $mobile_number;
                $user_data->address1 = $address1;
                $user_data->address2 = $address2;
                $user_data->zip_code = $zip_code;
                if($profile_picture != null) {
                    $user_data->file_extension = $ext;
                    $user_data->profile_picture = $profile_picture;
                }
                $user_data->updated_at = $date_time;
                $user_data->save();
                
                $output['success'] = true;
                $output['message'] = "User account updated successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
}
