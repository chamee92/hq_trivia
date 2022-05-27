<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Models\Question;
use App\Models\UserQuestion;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Setting;

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
                $output['data']['challenge_id'] = $challenge_data->id;
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
            $date_time = date("Y-m-d H:i:s");
            $sql = "SELECT c.`id` AS 'challenge_id', c.`challenge_name`, c.`challenge_Description`, c.`image_path`, c.`video_path`, 
                    c.`start_date_time`, c.`end_date_time`, c.`video_duration`, c.`quiz_duration`, c.`question_duration`, 
                    c.`question_start_time`, c.`question_end_time`, c.`total_price`, c.`total_coin`, c.`question_count`, 
                    c.`question_price`, c.`question_coin`, c.`total_watch`, c.`total_like`
                    FROM `challenges` AS c 
                    WHERE c.`is_Active` = 1 AND c.`question_end_time` >= '".$date_time."' 
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
                $output['message'] = "Challenge data passed successfully.";
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
                    WHERE c.`is_Active` = 1 AND c.`question_end_time` <= '".$date_time."' 
                    ORDER BY c.`id` DESC 
                    LIMIT " . $limit;
            $challenge_data = DB::select($sql);
            
            $output['data']['challenge'] = null;
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
            $output['message'] = "Old Challenge list passed successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function newchallengeList(Request $request)
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
                    WHERE c.`is_Active` = 1 AND c.`question_end_time` >= '".$date_time."' 
                    ORDER BY c.`id` DESC 
                    LIMIT " . $limit;
            $challenge_data = DB::select($sql);
            $output['data']['challenge'] = null;
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
            $output['message'] = "New challenge list passed successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        }  catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function addQuestion(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $question = isset($data['question']) ? $data['question'] : null;
            $answer1 = isset($data['answer1']) ? $data['answer1'] : null;
            $answer2 = isset($data['answer2']) ? $data['answer2'] : null;
            $answer3 = isset($data['answer3']) ? $data['answer3'] : null;
            $correct_answer = isset($data['correct_answer']) ? intval($data['correct_answer']) : 0;
            $created_at = date("Y-m-d H:i:s");

            if($challenge_id > 0 && $question != null && $answer1 != null && $answer2 != null && $answer3 != null && $correct_answer > 0 ) {
                $question_data = Question::create([
                                        "challenge_id" => $challenge_id,
                                        "question" => $question,
                                        "answer1" => $answer1,
                                        "answer2" => $answer2,
                                        "answer3" => $answer3,
                                        "correct_answer" => $correct_answer,
                                        "is_active" => 1,
                                        "created_at" => $created_at,
                                        "updated_at" => $created_at
                                    ]);

                $output['success'] = true;
                $output['message'] = "Question data added successfully.";
                $output['data']['question_id'] = $question_data->id;
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
  
    public function deleteQuestion(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $question_id = isset($data['question_id']) ? intval($data['question_id']) : 0;
            $status = isset($data['status']) ? intval($data['status']) : 0;
            $updated_at = date("Y-m-d H:i:s");

            if($challenge_id > 0 && $question_id > 0) {
                $output['success'] = true;
                if($status == 1) {
                    $output['message'] = "Question activated successfully.";
                } else {
                    $status = 0;
                    $output['message'] = "Question deactivated successfully.";
                }
                $question_data = Question::where('id', $question_id)->orderBy('id', 'DESC')->first();
                $question_data->is_active = $status;
                $question_data->updated_at = $updated_at;
                $question_data->save();
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
  
    public function viewQuestion(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;

            if($challenge_id > 0 ) {
                $output['data']['question_data'] = Question::select('id AS question_id', 'question', 'answer1', 'answer2', 'answer3', 'correct_answer')
                                                            ->where('challenge_id', $challenge_id)->where('is_active', 1)
                                                            ->orderBy('id', 'ASC')->get();

                $output['success'] = true;
                $output['message'] = "Question data passed successfully.";
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
  
    public function viewChallengeUserList(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;

            if($challenge_id > 0 ) {
                $output['data']['user_data'] = User::join('user_challenges', 'user_challenges.user_id', 'users.id')->where('user_challenges.challenge_id', $challenge_id)->where('user_challenges.is_active', 1)->where('users.is_active', 1)
                                                ->select('user_challenges.user_id', 'users.first_name', 'users.last_name', 'users.mobile_number', 'users.email_address', 'users.address1', 
                                                        'users.address2', 'users.zip_code', 'users.profile_picture', 'user_challenges.has_watched', 'user_challenges.has_like', 
                                                        'user_challenges.has_attend_quiz', 'user_challenges.correct_answer_count' , 'user_challenges.wrong_answer_count', 'user_challenges.earn_amount', 'user_challenges.earn_coin')
                                                ->orderBy('users.id', 'DESC')->get();

                $output['success'] = true;
                $output['message'] = "Challenge, user list passed successfully.";
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
  
    public function viewUserChallengeList(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

            if($user_id > 0 ) {
                $output['data']['challenge_data'] = Challenge::join('user_challenges', 'user_challenges.challenge_id', 'challenges.id')->where('user_challenges.user_id', $user_id)->where('user_challenges.is_active', 1)->where('challenges.is_active', 1)
                                                ->select('user_challenges.challenge_id', 'challenges.challenge_name', 'challenges.challenge_Description', 'challenges.image_path', 'challenges.video_path', 'challenges.start_date_time', 
                                                        'challenges.end_date_time', 'challenges.video_duration', 'challenges.quiz_duration', 'challenges.question_duration', 'challenges.question_start_time', 'challenges.question_end_time',
                                                        'challenges.total_price', 'challenges.total_coin', 'challenges.question_count', 'challenges.question_price', 'challenges.question_coin', 'challenges.total_watch', 'challenges.total_like', 
                                                        'user_challenges.has_watched', 'user_challenges.has_like','user_challenges.has_attend_quiz', 'user_challenges.correct_answer_count' , 'user_challenges.wrong_answer_count', 'user_challenges.earn_amount', 
                                                        'user_challenges.earn_coin')
                                                ->orderBy('challenges.id', 'DESC')->get();

                $output['success'] = true;
                $output['message'] = "User, challenge list passed successfully.";
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
  
    public function deleteChallenge(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $status = isset($data['status']) ? intval($data['status']) : 0;
            $updated_at = date("Y-m-d H:i:s");

            if($challenge_id > 0) {
                $output['success'] = true;
                if($status == 1) {
                    $output['message'] = "Challenge activated successfully.";
                } else {
                    $status = 0;
                    $output['message'] = "Challenge deactivated successfully.";
                }
                $challenge_data = Challenge::where('id', $challenge_id)->orderBy('id', 'DESC')->first();
                $challenge_data->is_active = $status;
                $challenge_data->updated_at = $updated_at;
                $challenge_data->save();
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
  
    public function viewChallengeUserQuestion(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;

            if($user_id > 0 && $challenge_id > 0) {
                $output['data']['answer_data'] = Question::join('user_questions', 'user_questions.question_id', 'questions.id')->where('user_questions.user_id', $user_id)
                                                    ->where('user_questions.challenge_id', $challenge_id)->where('user_questions.is_active', 1)->where('questions.is_active', 1)
                                                    ->select('questions.id AS question_id', 'questions.question', 'questions.answer1', 'questions.answer2', 'questions.answer3', 'questions.correct_answer', 
                                                            'user_questions.your_answer', 'user_questions.is_correct', 'user_questions.earn_amount', 'user_questions.earn_coin')
                                                    ->orderBy('questions.id', 'ASC')->get();

                $output['success'] = true;
                $output['message'] = "User, answer list passed successfully.";
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
  
    public function completeQuestion(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $question_id = isset($data['question_id']) ? intval($data['question_id']) : 0;
            $your_answer = isset($data['your_answer']) ? intval($data['your_answer']) : 0;
            $earn_amount = isset($data['earn_amount']) ? doubleval($data['earn_amount']) : 0;
            $earn_coin = isset($data['earn_coin']) ? doubleval($data['earn_coin']) : 0;
            $is_correct = isset($data['is_correct']) ? intval($data['is_correct']) : 0;
            $updated_at = date("Y-m-d H:i:s");

            if($challenge_id > 0 && $user_id > 0 && $question_id > 0) {
                $user_question_data = UserQuestion::updateOrCreate(
                                        [
                                            'user_id' => $user_id,
                                            'challenge_id' => $challenge_id,
                                            'question_id' => $question_id
                                        ],
                                        [
                                            'your_answer' => $your_answer,
                                            'earn_amount' => $earn_amount,
                                            'earn_coin' => $earn_coin,
                                            'is_correct' => $is_correct,
                                            'is_active' => 1,
                                            'created_at' => $updated_at,
                                            'updated_at' => $updated_at
                                        ]);
                                        
                $output['success'] = true;
                $output['data']['user_question_id'] = $user_question_data->id;
                $output['message'] = "User, question completed successfully.";
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
  
    public function completeChallenge(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $challenge_id = isset($data['challenge_id']) ? intval($data['challenge_id']) : 0;
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $correct_answer_count = isset($data['correct_answer_count']) ? intval($data['correct_answer_count']) : 0;
            $wrong_answer_count = isset($data['wrong_answer_count']) ? intval($data['wrong_answer_count']) : 0;
            $earn_amount = isset($data['earn_amount']) ? doubleval($data['earn_amount']) : 0;
            $earn_coin = isset($data['earn_coin']) ? doubleval($data['earn_coin']) : 0;
            $has_payment = isset($data['has_payment']) ? intval($data['has_payment']) : 0;
            $updated_at = date("Y-m-d H:i:s");

            if($challenge_id > 0 && $user_id > 0) {
                $user_challenge_data = UserChallenge::updateOrCreate(
                                        [
                                            'user_id' => $user_id,
                                            'challenge_id' => $challenge_id
                                        ],
                                        [
                                            'has_attend_quiz' => 1,
                                            'correct_answer_count' => $correct_answer_count,
                                            'wrong_answer_count' => $wrong_answer_count,
                                            'earn_amount' => $earn_amount,
                                            'earn_coin' => $earn_coin,
                                            'is_active' => 1,
                                            'created_at' => $updated_at,
                                            'updated_at' => $updated_at
                                        ]);
                if($has_payment == 1 && $earn_amount > 0) {
                    $payment_description = "Quiz winner price";

                    /**Fee ledger part */
                    $last_ledger = Ledger::select('balance')->where('is_active', 1)->where('user_id', $user_id)->orderBy('id', 'DESC')->first();
                    $last_balance = isset($last_ledger->balance) ? ($last_ledger->balance) : 0.00;
                    $balance = $last_balance + $earn_amount;
                    $last_coin_balance = isset($last_ledger->coin_balance) ? ($last_ledger->coin_balance) : 0.00;
                    $coin_balance = $last_coin_balance + $earn_coin;
                    /**Add payment ledger */
                    $ledger = Ledger::create([
                                           'user_id' => $user_id, 
                                           'payment_type_id' => 1,
                                           'payment_id' => $user_challenge_data->id, 
                                           'description' => $payment_description, 
                                           'amount' => $earn_amount, 
                                           'balance' => $balance, 
                                           'coin_amount' => $earn_coin, 
                                           'coin_balance' => $coin_balance, 
                                           'status' => 1,
                                           'is_active' => 1,
                                           'created_at' => $updated_at,
                                           'updated_at' => $updated_at
                                        ]);

                    $user_data = User::where('id', $user_id)->orderBy('id', 'DESC')->first();
                    if(isset($user_data->id)) {
                        $earn_total = doubleval($user_data->earn_total) + $earn_amount;
                        $earn_balance = doubleval($user_data->earn_balance) + $earn_amount;
                        $earn_coin_total = doubleval($user_data->earn_coin_total) + $earn_coin;
                        $earn_coin_balance = doubleval($user_data->earn_coin_balance) + $earn_coin;
                        $user_data->earn_total = $earn_total;
                        $user_data->earn_balance = $earn_balance;
                        $user_data->earn_coin_total = $earn_coin_total;
                        $user_data->earn_coin_balance = $earn_coin_balance;
                        $user_data->updated_at = $updated_at;
                        $user_data->save();
                    }
                    
                }                     
                $output['success'] = true;
                $output['data']['user_challenge_id'] = $user_challenge_data->id;
                $output['message'] = "User, challenge completed successfully.";
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
  
    public function allChallengeLike(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

            if($user_id > 0 ) {
                $output['data']['challenge_data'] = Challenge::join('user_challenges', 'user_challenges.challenge_id', 'challenges.id')->where('user_challenges.user_id', $user_id)
                                                ->where('user_challenges.is_active', 1)->where('challenges.is_active', 1)->where('user_challenges.has_like', 1)
                                                ->select('user_challenges.challenge_id', 'challenges.challenge_name', 'challenges.challenge_Description', 'challenges.image_path', 'challenges.video_path', 'challenges.start_date_time', 
                                                        'challenges.end_date_time', 'challenges.video_duration', 'challenges.quiz_duration', 'challenges.question_duration', 'challenges.question_start_time', 'challenges.question_end_time',
                                                        'challenges.total_price', 'challenges.total_coin', 'challenges.question_count', 'challenges.question_price', 'challenges.question_coin', 'challenges.total_watch', 'challenges.total_like', 
                                                        'user_challenges.has_watched', 'user_challenges.has_like','user_challenges.has_attend_quiz', 'user_challenges.correct_answer_count' , 'user_challenges.wrong_answer_count', 'user_challenges.earn_amount', 
                                                        'user_challenges.earn_coin')
                                                ->orderBy('challenges.id', 'DESC')->get();

                $output['success'] = true;
                $output['message'] = "User, challenge list passed successfully.";
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
  
    public function requestPayment(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $description = isset($data['description']) ? $data['description'] : null;
            $amount = isset($data['amount']) ? doubleval($data['amount']) : 0;
            $coin_amount = isset($data['coin_amount']) ? doubleval($data['coin_amount']) : 0;
            
            $created_at = date("Y-m-d H:i:s");

            if($user_id > 0 && $amount > 0) {

                $payment_data = Payment::create([
                                    "user_id" => $user_id,
                                    "description" => $description,
                                    "amount" => $amount,
                                    "paid_amount" => 0,
                                    "coin_amount" => $coin_amount,
                                    "paid_coin_amount" => 0,
                                    "status" => 0,
                                    "is_active" => 1,
                                    "created_at" => $created_at,
                                    "udapted_at" => $created_at
                                ]);

                $invoice_number = "IN".sprintf("%'.07d", $payment_data->id);
                $payment_add = Payment::where('id', $payment_data->id)->orderBy('id', 'DESC')->first();
                $payment_add->invoice_number = $invoice_number;
                $payment_add->save();

                $payment_description = "Payment Request (". $invoice_number .")";
                /**Fee ledger part */
                $last_ledger = Ledger::where('is_active', 1)->where('user_id', $user_id)->orderBy('id', 'DESC')->first();
                $last_balance = isset($last_ledger->balance) ? ($last_ledger->balance) : 0.00;
                $balance = $last_balance - $amount;
                $last_coin_balance = isset($last_ledger->coin_balance) ? ($last_ledger->coin_balance) : 0.00;
                $coin_balance = $last_coin_balance - $coin_amount;
                /**Add payment ledger */
                $ledger = Ledger::create([
                                       'user_id' => $user_id, 
                                       'payment_type_id' => 2,
                                       'payment_id' => $payment_data->id, 
                                       'description' => $payment_description, 
                                       'amount' => $amount, 
                                       'balance' => $balance, 
                                       'coin_amount' => $coin_amount, 
                                       'coin_balance' => $coin_balance, 
                                       'status' => 0,
                                       'is_active' => 1,
                                       'created_at' => $created_at,
                                       'updated_at' => $created_at
                                    ]);

                $user_data = User::where('id', $user_id)->orderBy('id', 'DESC')->first();
                if(isset($user_data->id)) {
                    $pending_withdraw_total = doubleval($user_data->pending_withdraw_total) + $amount;
                    $earn_balance = doubleval($user_data->earn_balance) - $amount;
                    $pending_withdraw_coin_total = doubleval($user_data->pending_withdraw_coin_total) + $coin_amount;
                    $earn_coin_balance = doubleval($user_data->earn_coin_balance) - $coin_amount;
                    $user_data->pending_withdraw_total = $pending_withdraw_total;
                    $user_data->earn_balance = $earn_balance;
                    $user_data->pending_withdraw_coin_total = $pending_withdraw_coin_total;
                    $user_data->earn_coin_balance = $earn_coin_balance;
                    $user_data->updated_at = $created_at;
                    $user_data->save();
                }

                $output['success'] = true;
                $output['message'] = "Payment created successfully.";
                $output['data']['invoice_number'] = $invoice_number;
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
  
    public function confirmPayment(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $payment_id = isset($data['payment_id']) ? intval($data['payment_id']) : 0;
            $status = isset($data['status']) ? intval($data['status']) : 0;
            $description = isset($data['description']) ? $data['description'] : null;
            
            $created_at = date("Y-m-d H:i:s");

            if($payment_id > 0) {
                $payment_data = Payment::where('id', $payment_id)->orderBy('id', 'DESC')->first();
                $invoice_number = $payment_data->invoice_number;
                $amount = doubleval($payment_data->amount);
                $coin_amount = doubleval($payment_data->coin_amount);
                $user_id = intval($payment_data->user_id);
                if($status == 1) {
                    $paid_amount = doubleval($payment_data->amount);
                    $paid_coin_amount = doubleval($payment_data->coin_amount);
                    $payment_data->paid_amount = $paid_amount;
                    $payment_data->paid_coin_amount = $paid_coin_amount;
                    $payment_data->status = $status;
                    $output['message'] = "Payment accepted successfully.";
    
                    $user_data = User::where('id', $user_id)->orderBy('id', 'DESC')->first();
                    if(isset($user_data->id)) {
                        $pending_withdraw_total = doubleval($user_data->pending_withdraw_total) - $amount;
                        $withdraw_total = doubleval($user_data->withdraw_total) + $amount;
                        $pending_withdraw_coin_total = doubleval($user_data->pending_withdraw_coin_total) - $coin_amount;
                        $withdraw_coin_total = doubleval($user_data->withdraw_coin_total) + $coin_amount;
                        $user_data->pending_withdraw_total = $pending_withdraw_total;
                        $user_data->withdraw_total = $withdraw_total;
                        $user_data->pending_withdraw_coin_total = $pending_withdraw_coin_total;
                        $user_data->withdraw_coin_total = $withdraw_coin_total;
                        $user_data->updated_at = $created_at;
                        $user_data->save();
                    }
                } else {
                    $status = -1;
                    $payment_data->status = $status;
                    $output['message'] = "Payment rejected successfully.";
                    $payment_description = "Payment Rejected (". $invoice_number .")";
                    /**Fee ledger part */
                    $last_ledger = Ledger::where('is_active', 1)->where('user_id', $user_id)->orderBy('id', 'DESC')->first();
                    $last_balance = isset($last_ledger->balance) ? ($last_ledger->balance) : 0.00;
                    $balance = $last_balance + $amount;
                    $last_coin_balance = isset($last_ledger->coin_balance) ? ($last_ledger->coin_balance) : 0.00;
                    $coin_balance = $last_coin_balance + $coin_amount;
                    /**Add payment ledger */
                    $ledger = Ledger::create([
                                           'user_id' => $user_id, 
                                           'payment_type_id' => 3,
                                           'payment_id' => $payment_id, 
                                           'description' => $payment_description, 
                                           'amount' => $amount, 
                                           'balance' => $balance, 
                                           'coin_amount' => $coin_amount, 
                                           'coin_balance' => $coin_balance, 
                                           'status' => 1,
                                           'is_active' => 1,
                                           'created_at' => $created_at,
                                           'updated_at' => $created_at
                                        ]);
    
                    $user_data = User::where('id', $user_id)->orderBy('id', 'DESC')->first();
                    if(isset($user_data->id)) {
                        $pending_withdraw_total = doubleval($user_data->pending_withdraw_total) - $amount;
                        $earn_balance = doubleval($user_data->earn_balance) + $amount;
                        $pending_withdraw_coin_total = doubleval($user_data->pending_withdraw_coin_total) - $coin_amount;
                        $earn_coin_balance = doubleval($user_data->earn_coin_balance) + $coin_amount;
                        $user_data->pending_withdraw_total = $pending_withdraw_total;
                        $user_data->earn_balance = $earn_balance;
                        $user_data->pending_withdraw_coin_total = $pending_withdraw_coin_total;
                        $user_data->earn_coin_balance = $earn_coin_balance;
                        $user_data->updated_at = $created_at;
                        $user_data->save();
                    }
                }
                $payment_data->transaction_data = $description;
                $payment_data->save();

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
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function getPayment(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
            $status = isset($data['status']) ? intval($data['status']) : 0;

            if($user_id > 0) {
                $output['data']['payment_data'] = Payment::where('payments.user_id', $user_id)->where('payments.status', $status)->where('payments.is_active', 1)
                                                    ->select('payments.id AS payment_id', 'payments.invoice_number', 'payments.description', 'payments.amount', 
                                                            'payments.paid_amount', 'payments.coin_amount', 'payments.paid_coin_amount', 'payments.status', 
                                                            'payments.transaction_data', 'payments.user_id', 'users.first_name', 'users.last_name', 'users.email_address'
                                                            , 'users.mobile_number', 'users.address1', 'users.address2', 'users.zip_code', 'users.profile_picture')
                                                    ->orderBy('payments.id', 'DESC')->get();
            } else {
                $output['data']['payment_data'] = Payment::where('payments.user_id', $user_id)->where('payments.status', $status)->where('payments.is_active', 1)
                                                    ->select('payments.id AS payment_id', 'payments.invoice_number', 'payments.description', 'payments.amount', 
                                                            'payments.paid_amount', 'payments.coin_amount', 'payments.paid_coin_amount', 'payments.status', 
                                                            'payments.transaction_data', 'payments.user_id', 'users.first_name', 'users.last_name', 'users.email_address'
                                                            , 'users.mobile_number', 'users.address1', 'users.address2', 'users.zip_code', 'users.profile_picture')
                                                    ->orderBy('payments.id', 'DESC')->get();
            }
            $output['success'] = true;
            $output['message'] = "Payments data passed successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
  
    public function getLedger(Request $request)
    {
        try {
            $data = json_decode($request->getContent(),true);
            $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

            if($user_id > 0) {
                $output['data']['ledger_data'] = Ledger::select('description', 'status', 'amount', 'balance', 'coin_amount', 'coin_balance')
                                                    ->where('user_id', $user_id)->where('is_active', 1)->orderBy('id', 'DESC')->get();

                $output['success'] = true;
                $output['message'] = "Ledger data passed successfully.";
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
    public function getSetting(Request $request)
    {
        try {
            $output['data']['setting_data'] = Setting::select('data', 'value')->where('is_active', 1)->orderBy('id', 'DESC')->get();
            $output['success'] = true;
            $output['message'] = "Setting data passed successfully.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);
        }  catch (\Exception $e) {
            dd($e);
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Server error. Please contact admin.";
            return response()->json(['success' => $output['success'],'message' => $output['message'], 'output' => $output['data']], 200);

        }
    }
}