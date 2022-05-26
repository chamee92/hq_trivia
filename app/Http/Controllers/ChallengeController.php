<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Challenge;
use App\Models\UserChallenge;
use App\Models\Question;

class ChallengeController extends Controller
{
  
    public function addChallenge(Request $request)
    {
        try {
            $data = $request->input();
            $challenge_name = isset($data['challenge_name']) ? $data['challenge_name'] : null;
            $challenge_Description = isset($data['challenge_Description']) ? $data['challenge_Description'] : null;
            $file_extension = isset($data['file_extension']) ? $data['file_extension'] : null;
            $start_date_time = isset($data['start_date_time']) ? $data['start_date_time'] : null;
            $end_date_time = isset($data['end_date_time']) ? $data['end_date_time'] : null;
            $video_duration = isset($data['video_duration']) ? intval($data['video_duration']) : 0;
            $quiz_duration = isset($data['quiz_duration']) ? intval($data['quiz_duration']) : 0;
            $question_duration = isset($data['question_duration']) ? intval($data['question_duration']) : 0;
            $question_start_time = isset($data['question_start_time']) ? $data['question_start_time'] : null;
            $question_end_time = isset($data['question_end_time']) ? $data['question_end_time'] : null;
            $total_price = isset($data['total_price']) ? doubleval($data['total_price']) : 0;
            $total_coin = isset($data['total_coin']) ? doubleval($data['total_coin']) : 0;
            $question_count = isset($data['question_count']) ? intval($data['question_count']) : 0;
            $question_price = isset($data['question_price']) ? doubleval($data['question_price']) : 0;
            $question_coin = isset($data['question_coin']) ? doubleval($data['question_coin']) : 0;
            
            $created_at = date("Y-m-d H:i:s");

            if($challenge_name != null ) {
                //file upload
                if($request->file()) {
                    $ext = pathinfo($request->challenge_image->getClientOriginalName(), PATHINFO_EXTENSION);
                    if($ext == null || $ext == ''){
                        $ext = $file_extension;
                    }
                    $file_name = time().'_'.$challenge_name . '.' . $ext;
                    $file_path = $request->file('challenge_image')->storeAs('uploads/challenges/images', $file_name, 'public');
                    $image_path = 'https://hq.docketapps.com/storage/'.$file_path;
                } else {
                    $image_path = null;
                }

                $challenge_data = Challenge::create([
                                    "challenge_name" => $challenge_name,
                                    "challenge_description" => $challenge_Description,
                                    "image_path" => $image_path,
                                    "start_date_time" => $start_date_time,
                                    "end_date_time" => $end_date_time,
                                    "video_duration" => $video_duration,
                                    "quiz_duration" => $quiz_duration,
                                    "question_duration" => $question_duration,
                                    "question_start_time" => $question_start_time,
                                    "question_end_time" => $question_end_time,
                                    "total_price" => $total_price,
                                    "total_coin" => $total_coin,
                                    "question_count" => $question_count,
                                    "question_price" => $question_price,
                                    "question_coin" => $question_coin,
                                    "is_active" => 1,
                                    "created_at" => $created_at,
                                    "udapted_at" => $created_at,
                                ]);

                $output['success'] = true;
                $output['message'] = "Challenge created successfully.";
                $output['data']['chalange_id'] = $challenge_data->id;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function addChallengeVideo(Request $request)
    {
        try {
            $data = $request->input();
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $file_extension = isset($data['file_extension']) ? $data['file_extension'] : null;
            $updated_at = date("Y-m-d H:i:s");

            $challenge_data = Challenge::where('id', $challenge_id)->orderBy('id', 'DESC')->first();


            if($challenge_id > 0 && isset($challenge_data->id)) {
                //file upload
                if($request->file()) {
                    $ext = pathinfo($request->challenge_video->getClientOriginalName(), PATHINFO_EXTENSION);
                    if($ext == null || $ext == ''){
                        $ext = $file_extension;
                    }
                    $file_name = time().'_'.$challenge_data->challenge_name . '.' . $ext;
                    $file_path = $request->file('challenge_video')->storeAs('uploads/challenges/video', $file_name, 'public');
                    $video_path = 'https://hq.docketapps.com/storage/'.$file_path;
                } else {
                    $video_path = null;
                }
                $challenge_data->video_path = $video_path;
                $challenge_data->updated_at = $updated_at;
                $challenge_data->save();

                $output['success'] = true;
                $output['message'] = "Challenge video upload successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function addChallengeWatch(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $status = isset($data['status']) ? intval($data['status']) : 1;
            $updated_at = date("Y-m-d H:i:s");
            $challenge_data = Challenge::where('id', $challenge_id)->orderBy('id', 'DESC')->first();

            if($challenge_id > 0 && $user_id > 0 && isset($challenge_data->id)) {
                $total_watch = intval($challenge_data->total_watch);
                if($status == 1) {
                    $challenge_data->total_watch = $total_watch + 1;
                    UserChallenge::updateOrCreate(
                        [
                            'user_id' => $user_id,
                            'challenge_id' => $challenge_id
                        ],
                        [
                            'has_watched' => 1,
                            'created_at' => $updated_at,
                            'updated_at' => $updated_at
                        ]
                    );
                } else {
                    $challenge_data->total_watch = $total_watch - 1;
                    UserChallenge::updateOrCreate(
                        [
                            'user_id' => $user_id,
                            'challenge_id' => $challenge_id
                        ],
                        [
                            'has_watched' => 0,
                            'created_at' => $updated_at,
                            'updated_at' => $updated_at
                        ]
                    );
                }
                
                $challenge_data->updated_at = $updated_at;
                $challenge_data->save();

                $output['success'] = true;
                $output['message'] = "Challenge watch mark successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function addChallengeLike(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $status = isset($data['status']) ? intval($data['status']) : 0;
            $updated_at = date("Y-m-d H:i:s");
            $challenge_data = Challenge::where('id', $challenge_id)->orderBy('id', 'DESC')->first();

            if($challenge_id > 0 && $user_id > 0 && isset($challenge_data->id)) {
                $total_like = intval($challenge_data->total_like);
                if($status == 1) {
                    $challenge_data->total_like = $total_like + 1;
                    UserChallenge::updateOrCreate(
                        [
                            'user_id' => $user_id,
                            'challenge_id' => $challenge_id
                        ],
                        [
                            'has_like' => 1,
                            'is_active' => 1,
                            'created_at' => $updated_at,
                            'updated_at' => $updated_at
                        ]
                    );
                    $output['message'] = "Challenge like mark successfully.";
                } else {
                    $challenge_data->total_like = $total_like - 1;
                    UserChallenge::updateOrCreate(
                        [
                            'user_id' => $user_id,
                            'challenge_id' => $challenge_id
                        ],
                        [
                            'has_like' => 0,
                            'is_active' => 1,
                            'created_at' => $updated_at,
                            'updated_at' => $updated_at
                        ]
                    );
                    $output['message'] = "Challenge dislike mark successfully.";
                }
                
                $challenge_data->updated_at = $updated_at;
                $challenge_data->save();

                $output['success'] = true;
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function editChallenge(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $challenge_name = isset($data['challenge_name']) ? $data['challenge_name'] : null;
            $challenge_Description = isset($data['challenge_Description']) ? $data['challenge_Description'] : null;
            $start_date_time = isset($data['start_date_time']) ? $data['start_date_time'] : null;
            $end_date_time = isset($data['end_date_time']) ? $data['end_date_time'] : null;
            $video_duration = isset($data['video_duration']) ? intval($data['video_duration']) : 0;
            $quiz_duration = isset($data['quiz_duration']) ? intval($data['quiz_duration']) : 0;
            $question_duration = isset($data['question_duration']) ? intval($data['question_duration']) : 0;
            $question_start_time = isset($data['question_start_time']) ? $data['question_start_time'] : null;
            $question_end_time = isset($data['question_end_time']) ? $data['question_end_time'] : null;
            $total_price = isset($data['total_price']) ? doubleval($data['total_price']) : 0;
            $total_coin = isset($data['total_coin']) ? doubleval($data['total_coin']) : 0;
            $question_count = isset($data['question_count']) ? intval($data['question_count']) : 0;
            $question_price = isset($data['question_price']) ? doubleval($data['question_price']) : 0;
            $question_coin = isset($data['question_coin']) ? doubleval($data['question_coin']) : 0;
            $updated_at = date("Y-m-d H:i:s");

            if($challenge_id > 0 && $challenge_name != null ) {

                $challenge_data = Challenge::where('id', $challenge_id)->orderBy('id', 'DESC')->first();
                $challenge_data->challenge_name = $challenge_name;
                $challenge_data->challenge_Description = $challenge_Description;
                $challenge_data->start_date_time = $start_date_time;
                $challenge_data->end_date_time = $end_date_time;
                $challenge_data->video_duration = $video_duration;
                $challenge_data->quiz_duration = $quiz_duration;
                $challenge_data->question_duration = $question_duration;
                $challenge_data->question_start_time = $question_start_time;
                $challenge_data->question_end_time = $question_end_time;
                $challenge_data->total_price = $total_price;
                $challenge_data->total_coin = $total_coin;
                $challenge_data->question_count = $question_count;
                $challenge_data->question_price = $question_price;
                $challenge_data->question_coin = $question_coin;
                $challenge_data->updated_at = $updated_at;
                $challenge_data->save();

                $output['success'] = true;
                $output['message'] = "Challenge data edited successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function addChallengeImage(Request $request)
    {
        try {
            $data = $request->input();
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $file_extension = isset($data['file_extension']) ? $data['file_extension'] : null;
            $updated_at = date("Y-m-d H:i:s");

            $challenge_data = Challenge::where('id', $challenge_id)->orderBy('id', 'DESC')->first();


            if($challenge_id > 0 && isset($challenge_data->id)) {
                //file upload
                if($request->file()) {
                    $ext = pathinfo($request->challenge_image->getClientOriginalName(), PATHINFO_EXTENSION);
                    if($ext == null || $ext == ''){
                        $ext = $file_extension;
                    }
                    $file_name = time().'_'.$challenge_data->challenge_name . '.' . $ext;
                    $file_path = $request->file('challenge_image')->storeAs('uploads/challenges/images', $file_name, 'public');
                    $image_path = 'https://hq.docketapps.com/storage/'.$file_path;
                } else {
                    $image_path = null;
                }
                $challenge_data->image_path = $image_path;
                $challenge_data->updated_at = $updated_at;
                $challenge_data->save();

                $output['success'] = true;
                $output['message'] = "Challenge image upload successfully.";
                $output['data'] = null;
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function nextChallenge(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $date_time = date("Y-md H:i:s");
            $sql = "SELECT c.`id` AS 'challenge_id', c.`challenge_name`, c.`challenge_Description`, c.`image_path`, c.`video_path`, 
                    c.`start_date_time`, c.`end_date_time`, c.`video_duration`, c.`quiz_duration`, c.`question_duration`, 
                    c.`question_start_time`, c.`question_end_time`, c.`total_price`, c.`total_coin`, c.`question_count`, 
                    c.`question_price`, c.`question_coin`, c.`total_watch`, c.`total_like`
                    FROM `challenges` AS c 
                    WHERE c.`is_Active` = 1 AND c.`start_date_time` >= '".$date_time."' 
                    ORDER BY c.`id` ASC 
                    LIMIT 1";
            $challenge_data = DB::select($sql);
            if(sizeof($challenge_data) > 0 ) {
                foreach($challenge_data AS $challenge) {
                    $output['data']['challenge_id'] = $challenge->challenge_id;
                    $output['data']['challenge_name'] = $challenge->challenge_name;
                    $output['data']['challenge_Description'] = $challenge->challenge_Description;
                    $output['data']['image_path'] = $challenge->image_path;
                    $output['data']['video_path'] = $challenge->video_path;
                    $output['data']['start_date_time'] = $challenge->start_date_time;
                    $output['data']['end_date_time'] = $challenge->end_date_time;
                    $output['data']['video_duration'] = $challenge->video_duration;
                    $output['data']['quiz_duration'] = $challenge->quiz_duration;
                    $output['data']['question_duration'] = $challenge->question_duration;
                    $output['data']['question_start_time'] = $challenge->question_start_time;
                    $output['data']['question_end_time'] = $challenge->question_end_time;
                    $output['data']['total_price'] = $challenge->total_price;
                    $output['data']['total_coin'] = $challenge->total_coin;
                    $output['data']['question_count'] = $challenge->question_count;
                    $output['data']['question_price'] = $challenge->question_price;
                    $output['data']['question_coin'] = $challenge->question_coin;
                    $output['data']['total_watch'] = $challenge->total_watch;
                    $output['data']['total_like'] = $challenge->total_like;
                }
                $output['data']['server_time'] = $date_time;
                $output['data']['question'] = Question::select("id AS question_id", "question", "answer1", "answer2", "answer3", "correct_answer")->where('challenge_id', $output['data']['challenge_id'])->where('is_active', 1)->orderBy('id', 'ASC')->get();
                $output['success'] = true;
                $output['message'] = "User password change successfully.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function oldChallengeList(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $limit =  isset($data['limit']) ? intval($data['limit']): 100;
            $date_time = date("Y-m-d H:i:s");
            $sql = "SELECT c.`id` AS 'challenge_id', c.`challenge_name`, c.`challenge_Description`, c.`image_path`, c.`video_path`, 
                    c.`start_date_time`, c.`end_date_time`, c.`video_duration`, c.`quiz_duration`, c.`question_duration`, 
                    c.`question_start_time`, c.`question_end_time`, c.`total_price`, c.`total_coin`, c.`question_count`, 
                    c.`question_price`, c.`question_coin`, c.`total_watch`, c.`total_like`
                    FROM `challenges` AS c 
                    WHERE c.`is_Active` = 1 AND c.`start_date_time` <= '".$date_time."' 
                    ORDER BY c.`id` DESC 
                    LIMIT " . $limit;
            $challenge_data = DB::select($sql);
            if(sizeof($challenge_data) > 0 ) {
                foreach($challenge_data AS $index => $challenge) {
                    $output['data']['challenge'][$index]['challenge_id'] = $challenge->challenge_id;
                    $output['data']['challenge'][$index]['challenge_name'] = $challenge->challenge_name;
                    $output['data']['challenge'][$index]['challenge_Description'] = $challenge->challenge_Description;
                    $output['data']['challenge'][$index]['image_path'] = $challenge->image_path;
                    $output['data']['challenge'][$index]['video_path'] = $challenge->video_path;
                    $output['data']['challenge'][$index]['start_date_time'] = $challenge->start_date_time;
                    $output['data']['challenge'][$index]['end_date_time'] = $challenge->end_date_time;
                    $output['data']['challenge'][$index]['video_duration'] = $challenge->video_duration;
                    $output['data']['challenge'][$index]['quiz_duration'] = $challenge->quiz_duration;
                    $output['data']['challenge'][$index]['question_duration'] = $challenge->question_duration;
                    $output['data']['challenge'][$index]['question_start_time'] = $challenge->question_start_time;
                    $output['data']['challenge'][$index]['question_end_time'] = $challenge->question_end_time;
                    $output['data']['challenge'][$index]['total_price'] = $challenge->total_price;
                    $output['data']['challenge'][$index]['total_coin'] = $challenge->total_coin;
                    $output['data']['challenge'][$index]['question_count'] = $challenge->question_count;
                    $output['data']['challenge'][$index]['question_price'] = $challenge->question_price;
                    $output['data']['challenge'][$index]['question_coin'] = $challenge->question_coin;
                    $output['data']['challenge'][$index]['total_watch'] = $challenge->total_watch;
                    $output['data']['challenge'][$index]['total_like'] = $challenge->total_like;

                    $output['data']['challenge'][$index]['question'] = Question::where('challenge_id', $challenge->challenge_id)->where('is_active', 1)->orderBy('id', 'ASC')->get();
                }
            }
            
            $output['data']['server_time'] = $date_time;
            $output['success'] = true;
            $output['message'] = "Old Challenge list passed successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function newchallengeList(Request $request)
    {
        try {
            $data = $request->input();
            $limit =  isset($data['limit']) ? intval($data['limit']): 100;
            $date_time = date("Y-m-d H:i:s");
            $sql = "SELECT c.`id` AS 'challenge_id', c.`challenge_name`, c.`challenge_Description`, c.`image_path`, c.`video_path`, 
                    c.`start_date_time`, c.`end_date_time`, c.`video_duration`, c.`quiz_duration`, c.`question_duration`, 
                    c.`question_start_time`, c.`question_end_time`, c.`total_price`, c.`total_coin`, c.`question_count`, 
                    c.`question_price`, c.`question_coin`, c.`total_watch`, c.`total_like`
                    FROM `challenges` AS c 
                    WHERE c.`is_Active` = 1 AND c.`start_date_time` >= '".$date_time."' 
                    ORDER BY c.`id` DESC 
                    LIMIT " . $limit;
            $challenge_data = DB::select($sql);
            if(sizeof($challenge_data) > 0 ) {
                foreach($challenge_data AS $index => $challenge) {
                    $output['data']['challenge'][$index]['challenge_id'] = $challenge->challenge_id;
                    $output['data']['challenge'][$index]['challenge_name'] = $challenge->challenge_name;
                    $output['data']['challenge'][$index]['challenge_Description'] = $challenge->challenge_Description;
                    $output['data']['challenge'][$index]['image_path'] = $challenge->image_path;
                    $output['data']['challenge'][$index]['video_path'] = $challenge->video_path;
                    $output['data']['challenge'][$index]['start_date_time'] = $challenge->start_date_time;
                    $output['data']['challenge'][$index]['end_date_time'] = $challenge->end_date_time;
                    $output['data']['challenge'][$index]['video_duration'] = $challenge->video_duration;
                    $output['data']['challenge'][$index]['quiz_duration'] = $challenge->quiz_duration;
                    $output['data']['challenge'][$index]['question_duration'] = $challenge->question_duration;
                    $output['data']['challenge'][$index]['question_start_time'] = $challenge->question_start_time;
                    $output['data']['challenge'][$index]['question_end_time'] = $challenge->question_end_time;
                    $output['data']['challenge'][$index]['total_price'] = $challenge->total_price;
                    $output['data']['challenge'][$index]['total_coin'] = $challenge->total_coin;
                    $output['data']['challenge'][$index]['question_count'] = $challenge->question_count;
                    $output['data']['challenge'][$index]['question_price'] = $challenge->question_price;
                    $output['data']['challenge'][$index]['question_coin'] = $challenge->question_coin;
                    $output['data']['challenge'][$index]['total_watch'] = $challenge->total_watch;
                    $output['data']['challenge'][$index]['total_like'] = $challenge->total_like;

                    $output['data']['challenge'][$index]['question'] = Question::where('challenge_id', $challenge->challenge_id)->where('is_active', 1)->orderBy('id', 'ASC')->get();
                }
                $output['data']['server_time'] = $date_time;
                $output['success'] = true;
                $output['message'] = "Old challenge list passed successfully.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Data didn't passed correctly!.";
                return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
            }
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function viewChallengeUserList(Request $request)
    {
        try {
            $data = $request->input();
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;

            if($challenge_id > 0 ) {
                $user_data = User::join('user_challenges', 'user_challenges.user_id', '')->where('id', $user_id)->orderBy('id', 'DESC')->first();
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
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function deleteChallenge(Request $request)
    {
        try {
            $data = $request->input();
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
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
}