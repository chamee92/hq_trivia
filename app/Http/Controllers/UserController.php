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
    public function authenticate(Request $request)
    {
    
        try {
            $data = $request->input();
            $login_type = isset($data['login_type']) ? intval($data['login_type']) : 1;
            if($login_type == 1) {   
                $mobile_number = isset($data['mobile_number']) ? $data['mobile_number'] : null;       
                $password = isset($data['password']) ? $data['password'] : null;  
                //valid credential
                $validator = Validator::make($data, [
                    'mobile_number' => 'required',
                    'password' => 'required',
                    'login_type' => 'required',
                    'push_id' => 'required',
                    'device_id' => 'required',
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
                    $user_data = User::where('mobile_number', $data['mobile_number'])->first();
                    if(isset($user_data->id) && intval($user_data->id) > 0) {
                        $user_data->password  = $user_data->normal_password; 
                        $user_data->push_id  = $data['push_id']; 
                        $user_data->device_id  = $data['device_id']; 
                        $user_data->login_type = 1;
                        $user_data->save();
                    }
                    if (! $token = JWTAuth::attempt($credentials)) {
                        $output['success'] = false;
                        $output['data'] = [];
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
            } else if($login_type == 2 || $login_type == 3) {
                //valid credential
                $validator = Validator::make($data, [
                    'email_address' => 'required',
                    'login_type' => 'required',
                    'push_id' => 'required',
                    'device_id' => 'required',
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
                    $user_data = User::where('email_address', $data['email_address'])->first();
                    if(isset($user_data->id) && intval($user_data->id) > 0) {
                        $user_data->password  = bcrypt($user_data->email_address);
                        $user_data->google_password  = bcrypt($user_data->email_address);
                        $user_data->apple_password  = bcrypt($user_data->email_address);
                        $credentials['password'] =  $user_data->email_address;                       
                        $user_data->push_id  = $data['push_id']; 
                        $user_data->device_id  = $data['device_id']; 
                        $user_data->login_type = $login_type;
                        $user_data->save();
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
                /*'mobile_number' => 'required|string|unique:users',*/
                'address1' => 'required|string',
                'address2' => 'required|string',
                'zip_code' => 'required|string'
            ]);
            if($validator->fails()){
                /*$validator1 = Validator::make($request->all(), [                 
                                'mobile_number' => 'required|string|unique:users'
                            ]);*/
                $validator2 = Validator::make($request->all(), [                 
                                'first_name' => 'required|string'
                            ]);
                $validator3 = Validator::make($request->all(), [          
                                'address1' => 'required|string'
                            ]);
                $validator4 = Validator::make($request->all(), [          
                                'address2' => 'required|string'
                            ]);
                $validator5 = Validator::make($request->all(), [          
                                'zip_code' => 'required|string'
                            ]);
                /*if($validator1->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Your mobile number previously use or not correct format, please check & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else */if($validator2->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your first name & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else if($validator3->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your address first field & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else if($validator4->fails()) {
                    $output['success'] = false;
                    $output['message'] = "Please enter your address second field & try again..!";
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
                } else if($validator5->fails()) {
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
            $defaut_password = $first_name;
            $date_time = date('Y-m-d H:i:s');
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
                $profile_picture = 'http://local.hq_trivia.lk/storage/'.$file_path;
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
                        'address1' => $address1,
                        'address2' => $address2,
                        'zip_code' => $zip_code,
                        'profile_picture' => $profile_picture,
                        'file_extension' => $file_extension,
                        'earn_total' => 0,
                        'withdraw_total' => 0,
                        'earn_balance' => 0,
                        'is_active' => 1, 
                        'created_at' => $date_time,
                        'updated_at' => $date_time
                    ]);

            $output['success'] = true;
            $output['data']['user'] = $user;
            $output['message'] = "User registered successfully";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }

    public function getAuthenticatedUser(Request $request)
    {
        try {
            $data = $request->input();
            $lang = isset($data['lang'])? $data['lang']: 'en';
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
            $sql = "SELECT u.`id` AS 'user_id', u.`chamber_id`, c.`chamber_name`, u.`user_type_id`, ut.`user_type`, 
                    u.`full_name`, u.`email_address`, u.`nic_number`, u.`mobile_number`, u.`address`, u.`salary_type_id`, st.`salary_type`,  
                    u.`salary`, u.`total_leave`, u.`get_leave`, u.`has_verified`, u.`os_type`, u.`push_id`, 
                    u.`is_active`, u.`created_at`, u.`updated_at`, c.`task_notify_time`, c.`appointment_notify_time`, c.`todo_notify_time`
                    FROM `users` AS u 
                    LEFT JOIN `chambers` AS c ON c.`id` = u.`chamber_id`
                    LEFT JOIN `salary_types` AS st ON st.`id` = u.`salary_type_id`
                    INNER JOIN `user_types` AS ut ON ut.`id` = u.`user_type_id` 
                    WHERE u.`is_active` = 1 AND u.`id` = " . $user->id ."
                    ORDER BY u.`id` DESC 
                    LIMIT 1";
            //$output['data'] = DB::select($sql);
            $user_data = DB::select($sql);
            if(sizeof($user_data) > 0) {
                foreach($user_data AS $auth_user) {
                    $output['data']['user_id'] = intval($auth_user->user_id);
                    $output['data']['user_type_id']  = intval($auth_user->user_type_id);
                    $output['data']['user_type']  = $auth_user->user_type;
                    $output['data']['chamber_id']  = intval($auth_user->chamber_id);
                    $output['data']['chamber_name']  = $auth_user->chamber_name;
                    $output['data']['full_name']  = $auth_user->full_name;
                    $output['data']['email_address']  = $auth_user->email_address;
                    $output['data']['mobile_number']  = $auth_user->mobile_number;
                    $output['data']['nic_number']  = $auth_user->nic_number;
                    $output['data']['address']  = $auth_user->address;
                    $output['data']['salary_type_id']  = intval($auth_user->salary_type_id);
                    $output['data']['salary']  = doubleval($auth_user->salary);
                    $output['data']['total_leave']  = intval($auth_user->total_leave);
                    $output['data']['get_leave']  = intval($auth_user->get_leave);
                    $output['data']['has_verified']  = intval($auth_user->has_verified);
                    $output['data']['push_id']  = $auth_user->push_id;
                    $output['data']['os_type']  = $auth_user->os_type; 
                    $output['data']['task_notify_time']  = $auth_user->task_notify_time;
                    $output['data']['appointment_notify_time']  = $auth_user->appointment_notify_time;                
                    $output['data']['todo_notify_time']  = $auth_user->todo_notify_time;               
                    $output['data']['is_active']  = $auth_user->is_active;
                    $output['data']['created_at']  = $auth_user->created_at;
                    $output['data']['updated_at']  = $auth_user->updated_at;
                }
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
            $data = $request->input();
            $lang = isset($data['lang'])? $data['lang']: 'en';
            $user_type_id = isset($data['user_type_id']) ? intval($data['user_type_id']) : 0;
            $chamber_id = isset($data['chamber_id'])? intval($data['chamber_id']) : 0;
            $user_id = isset($data['user_id'])? intval($data['user_id']) : 0;
            $search_data = isset($data['search_data']) ? $data['search_data'] : null;
            $sql = "SELECT u.`id` AS 'user_id', u.`chamber_id`, c.`chamber_name`, u.`user_type_id`, ut.`user_type`, 
                    u.`full_name`, u.`email_address`, u.`nic_number`, u.`mobile_number`, u.`address`, u.`salary_type_id`, st.`salary_type`,  
                    u.`salary`, u.`total_leave`, u.`get_leave`, u.`has_verified`, u.`os_type`, u.`push_id`, 
                    u.`is_active`, u.`created_at`, u.`updated_at`
                    FROM `users` AS u 
                    LEFT JOIN `chambers` AS c ON c.`id` = u.`chamber_id`
                    LEFT JOIN `salary_types` AS st ON st.`id` = u.`salary_type_id`
                    INNER JOIN `user_types` AS ut ON ut.`id` = u.`user_type_id` 
                    WHERE (u.`is_active` = 1 OR   u.`is_active` = 0)";
            if($user_type_id != 0) {
                if($user_type_id == -1) {
                    $sql .= " AND (u.`user_type_id` = 2 OR  u.`user_type_id` = 3  OR  u.`user_type_id` = 4)";
                } else {
                    $sql .= " AND u.`user_type_id` = " . $user_type_id;
                }
                
            }
            if($chamber_id != 0) {
                $sql .= " AND u.`chamber_id` = " . $chamber_id;
            }
            if($user_id != 0) {
                $sql .= " AND u.`id` = " . $user_id;
            }
            if($search_data != null) {
                $sql .= " AND (u.`full_name` LIKE '%" . $search_data . "%' OR u.`email_address` LIKE '%".$search_data."%' OR u.`mobile_number` LIKE '%".$search_data."%'
                            OR u.`nic_number` LIKE '%".$search_data."%' OR c.`chamber_name` LIKE '%".$search_data."%' OR ut.`user_type` LIKE '%".$search_data."%')";
            }
            $sql .= " ORDER BY u.`id` DESC";


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
            $credentials = $request->input();
            $lang = isset($credentials['lang'])? $credentials['lang']: 'en';
            $validator = Validator::make($credentials, [
                'token' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                $output['success'] = false;
                $output['data'] = null;
                //$output['message'] = $validator->messages();
                $output['message'] = "Invalid request.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
            $token = $credentials['token'];
            //Request is validated, do logout        
            JWTAuth::invalidate($token);
            $output['success'] = true;
            $output['data'] = null;
            $output['message'] = "User has been logged out successfully.";
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
            $data = $request->input();
            $lang = isset($data['lang'])? $data['lang']: 'en';
            $user_id = intval($data['user_id']);
            $password = $data['password'];
            $reenter_password = $data['reenter_password'];
            $updated_at = date("Y-m-d H:i:s");
            $updated_by = intval($data['updated_by']);
            if($reenter_password == $password) {
                $user_data = User::where('is_active', 1)->where('id', $user_id)->first();
                $new_password = bcrypt($password);
                $user_data->password = $new_password;
                $user_data->updated_by = $updated_by;
                $user_data->updated_at = $updated_at;
                $user_data->save();
                $output['success'] = true;
                $output['message'] = "User password change successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Password is missmatch.";
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
