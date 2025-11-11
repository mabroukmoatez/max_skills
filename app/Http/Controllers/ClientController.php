<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Chapitres;
use App\Models\Cours;
use App\Models\Lessons;
use App\Models\LessonsProjects;
use App\Models\Payment;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use App\Events\MessageSent;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
   
    public function cours()
    {   
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
        
        $cour = Cours::findOrFail('9f705dc0-61fc-4b19-9b10-9fe097f618de');
        $chapitres = Chapitres::where('type','chapitre')->where('cour_id', '9f705dc0-61fc-4b19-9b10-9fe097f618de')->get();

        $certifica = Chapitres::where('type','certifica')->where('cour_id', '9f705dc0-61fc-4b19-9b10-9fe097f618de')->get();
        $photo_admin = User::find(1)->path_photo;
        $notifications = Notification::where('reciver_id', auth()->id())
                                ->where('status', false)
                                ->get();

        return view('client.cour', compact('cour','chapitres','certifica','photo_admin','notifications'));

       // return view('client.cours');
    } 
    
    public function courById($id)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
        // Allow access if user is admin or client
        if (!in_array($user->role, ['admin', 'client', 'agent'])) {
            abort(403, 'Unauthorized action !!!.');
        }
        $cour = Cours::findOrFail($id);
        $chapitres = Chapitres::where('type','chapitre')->where('cour_id', $id)->orderBy('order_num', 'asc')->get();

        $certifica = Chapitres::where('type','certifica')->where('cour_id', $id)->orderBy('order_num', 'asc')->get();
        $photo_admin = User::find(1)->path_photo;
        $notifications = Notification::where('reciver_id', auth()->id())
                                ->where('status', false)
                                ->get();
        return view('client.cour', compact('cour','chapitres','certifica','photo_admin','notifications'));
    }

    public function courPay($id)
    {
      
          $cour = Cours::findOrFail($id);
        $chapitres = Chapitres::where('type','chapitre')->where('cour_id', $id)->orderBy('order_num', 'asc')->get();

        $certifica = Chapitres::where('type','certifica')->where('cour_id', $id)->orderBy('order_num', 'asc')->get();
        $photo_admin = User::find(1)->path_photo;
        $notifications = Notification::where('reciver_id', auth()->id())
                                ->where('status', false)
                                ->get();
        return view('client.payment', compact('cour','chapitres','certifica','photo_admin','notifications'));
    }
    

    public function chapitreById($id)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
        if ($id != 67 && $id != 68 && $user->status == 1 && $user->is_demo == 1) {
             return redirect()->to('/cours');
        }

        $chapitre = Chapitres::with('lessons')
                            ->where('id', $id)
                            ->first();
        
        if (!$chapitre) {
            abort(404, 'Chapter not found');
        }

        $lessons = $chapitre->lessons;
        $cour = Cours::findOrFail($chapitre->cour_id);
        $totalSeconds = 0;
        foreach ($lessons as $lesson) {
            $totalSeconds += ($lesson->lessonVideoHours ?? 0) * 3600; // Hours to seconds
            $totalSeconds += ($lesson->lessonVideoMinutes ?? 0) * 60; // Minutes to seconds
            $totalSeconds += ($lesson->lessonVideoSeconds ?? 0); // Seconds
        }
    
        // Convert total seconds to hours, minutes, and seconds
        $hours = floor($totalSeconds / 3600); // Total hours
        $remainingSecondsAfterHours = $totalSeconds % 3600; // Remaining seconds after extracting hours
        $minutes = floor($remainingSecondsAfterHours / 60); // Total minutes
        $seconds = $remainingSecondsAfterHours % 60; // Remaining seconds
    
        // Format the total duration as "X hours Y minutes Z seconds"
        $totalDuration = '';
        if ($hours > 0) {
            $totalDuration .= $hours.($hours == 1 ? 'hr' : 'hr') . ' ';
        }
        if ($minutes > 0) {
            $totalDuration .= $minutes.($minutes == 1 ? 'min' : 'min') . ' ';
        }
        if ($seconds > 0 || empty($totalDuration)) { // Include seconds only if there's no other value or it's non-zero
            $totalDuration .= $seconds.($seconds == 1 ? 'sec' : 'sec');
        }
        $photo_admin = User::find(1)->path_photo;
        $notifications = Notification::where('reciver_id', auth()->id())
                                        ->where('status', false)
                                        ->get();
        return view('client.chapitre',compact('cour','lessons','chapitre','totalDuration','photo_admin','notifications'));
    }
    
    public function newChat (Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
        //$userId = auth()->id();
        $lessonId = $request->input('lesson_id');
        $findLesson = Lessons::find($lessonId);
        if($findLesson){
            $verif_chat = Chat::where('user_id',$userId)->where('lesson_id',$lessonId)->first();
            if($verif_chat){
                $new_chat = $verif_chat;
                return response()->json([
                    'success' => true,
                    'chat_id' => $new_chat->id,
                    'message' => 'Chat reloaded successfuly',
                ]);
            } else {
                $new_chat = New Chat();
                $new_chat->user_id = $userId;
                $new_chat->lesson_id  = $lessonId;
                $new_chat->save();
                return response()->json([
                    'success' => true,
                    'chat_id' => $new_chat->id,
                    'message' => 'New Chat created successfuly',
                ]);
            }
           
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found',
            ]);
        }
    }
   
    public function newMessage(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
        $chatId = $request->input('chat_id');
        // Check if the chat exists
        $verif_chat = Chat::find($chatId);

        if ($verif_chat) {
            // If chat exists, handle messages
            $this->handleMessages($request, $chatId, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Messages created successfully',
            ]);
        } else {
            // If chat does not exist, check if lesson_id is provided
            $lessonId = $request->input('lesson_id');
            if (!$lessonId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson ID is required to create a new chat',
                ]);
            }

            // Call newChat() to create a new chat
            $newChatResponse = $this->newChat(new Request([
                'lesson_id' => $lessonId,
            ]));

            // Extract the chat ID from the JSON response
            $newChatData = json_decode($newChatResponse->getContent(), true);
            if (isset($newChatData['success']) && $newChatData['success'] === true) {
                $chatId = $newChatData['chat_id'];

                // Handle messages for the newly created chat
                $this->handleMessages($request, $chatId, $userId);

                return response()->json([
                    'success' => true,
                    'message' => 'New chat created and messages saved successfully',
                ]);
            } else {
                // If newChat() failed, return the error response
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create a new chat',
                ]);
            }
        }
    }

    private function handleMessages(Request $request, $chatId, $userId)
    {   
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
    
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            if (!is_array($files)) {
                $files = [$files];
            }
        
            foreach ($files as $file) {
                try {
                    // Store the file in the 'public' disk under the 'client' directory
                    $fileName = Storage::disk('public')->putFile('client', $file);
                    $filePath = Storage::url($fileName);

                    if ($filePath) {
                        // Create a new message for the file
                        $new_message = new Message();
                        $new_message->chat_id = $chatId;
                        $new_message->sender_id = $userId;
                        $new_message->message = $filePath;
                        $new_message->type = 'file';
                        $message->readed = 'non_lu';
                        $new_message->save();

                        $message_notif = Message::create([
                            'chat_id' => $chat->id,
                            'sender_id' => auth()->id(),
                            'message' => "New project uploaded: $url",
                            'type' => 'link',
                            'readed' => false,
                            'page_type' => 'course',
                            'page_id' => 1,
                        ]);
                
                        broadcast(new MessageSent($message_notif));

                    }
                } catch (\Exception $e) {
                
                }
            }
        }

        // Check if a regular message is included in the request
        if ($request->has('message')) {
            // Create a new message for the regular text
            $new_message = new Message();
            $new_message->chat_id = $chatId;
            $new_message->sender_id = $userId;
            $new_message->message = $request->input('message');
            $new_message->type = 'message';
            $message->readed = 'non_lu';
            $new_message->save();

            $message_notif = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => auth()->id(),
                'message' => "New project uploaded: $url",
                'type' => 'link',
                'readed' => false,
                'page_type' => 'course',
                'page_id' => 1,
            ]);
    
            broadcast(new MessageSent($message_notif));
        }
    }

    public function uploadFile(Request $request): JsonResponse
    {
        if ($request->isMethod('OPTIONS')) {
            return response()->json(["status's" => 'options_request_handled'], 200)
                ->header('Access-Control-Allow-Origin', 'https://www.maxskills.tn' )
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        }
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            //return redirect()->route('loginClient');
              return response()->json(['error' => 'Unauthenticated.'], 401)
                ->header('Access-Control-Allow-Origin', 'https://www.maxskills.tn' );
        }
       
        $file = $request->file('file');

        // Create or find chat (without lesson_id initially)
        $pageType = 'chat';
        $pageId = 1;

        $chat = Chat::firstOrCreate(
            [
                'user_id' => $user->id,
                'page_type' => $pageType,
                'course_id' =>  'chat' ? $pageId : null,
                'chapter_id' => $pageType === 'chat' ? null : $pageId,
            ]
        );


        // Store the file
        $path = $file->store('uploads', 'public');

        // Create a new message
        $message = new Message();
        $message->chat_id = $chat->id;
        $message->sender_id = $userId;
        $message->message = 'storage/' . $path;
        $message->page_type = 'chat';
        $message->page_id = 1;
        $message->is_admin = false ;
        $message->type = 'file';
        $message->readed = false;
        $message->sent_at = now();

        $message->save();


         return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path,
            'message_id' => $message->id
        ], 200) // Le statut 200 est le défaut, mais c'est bien de l'expliciter
        ->header('Access-Control-Allow-Origin', 'https://www.maxskills.tn' )
        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
    }

    // Update lesson_id for all messages when the submit button is clicked
    public function updateLessonId(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
        $request->validate([
            'message_ids' => 'array',
        ]);

        $messageIds = $request->input('message_ids');
        $lessonId = $request->input('lesson_id');

        // // Find or create the chat with the lesson_id
        // $chat = Chat::where('user_id', $userId)->where('lesson_id', $lessonId)->first();
        // if (!$chat) {
        //     $chat = Chat::where('user_id', $userId)->whereNull('lesson_id')->first();
        //     if ($chat) {
        //         $chat->lesson_id = $lessonId;
        //         $chat->save();
        //     } else {
        //         $chat = new Chat();
        //         $chat->user_id = $userId;
        //         $chat->lesson_id = $lessonId;
        //         $chat->save();
        //     }
        // }

        // // Update all messages with the new chat_id (and thus lesson_id)
        // Message::whereIn('id', $messageIds)->update(['chat_id' => $chat->id]);
        $messages = Message::whereIn('id', $messageIds)->get() ;
        return response()->json(['message' => 'Lesson ID updated successfully','messages' => $messages]);
    }
    public function upload(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }

        try {
            // Clear all existing output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
    
            // Set headers for SSE
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
    
            // Check if files exist
            if (!$request->hasFile('file') || !$request->file('file')) {
                echo "data: {" . json_encode([
                    'success' => false,
                    'message' => 'No files were found in the request.',
                ]) . "}\n\n";
                ob_flush();
                flush();
                return;
            }
    
            $chatId = $request->input('chat_id');
            $lessonId = $request->input('lesson_id');
    
            // Handle chat logic
            $chat_verif = Chat::find($chatId);
            if ($chat_verif && $chatId) {
                $chat = $chat_verif;
            } else {
                $chat = new Chat();
                $chat->user_id = $userId;
                $chat->lesson_id = $lessonId;
                $chat->save();
            }
    
            $uploadedFiles = [];
            $totalFiles = count($request->file('file'));
            $processedFiles = 0;
    
            foreach ($request->file('file') as $file) {
                if ($file->isValid()) {
                    $receiver = new FileReceiver($file, $request, HandlerFactory::classFromRequest($request));
                    if ($receiver->isUploaded()) {
                        $save = $receiver->receive();
    
                        while (!$save->isFinished()) {
                            $handler = $save->handler();
                            $progress = $handler->getPercentageDone();
    
                            // Send progress update to the client
                            echo "data: {" . json_encode([
                                'success' => true,
                                'progress' => $progress,
                                'finished' => false
                            ]) . "}\n\n";
                            ob_flush();
                            flush();
                        }
    
                        // Save file after upload is complete
                        $finalPath = Storage::disk('public')->putFile('client_uploaded', $file);
    
                        // Create message
                        $message = new Message();
                        $message->chat_id = $chat->id;
                        $message->sender_id = $userId;
                        $message->message = Storage::url($finalPath);
                        $message->type = 'file';
                        $message->readed = 'non_lu';
                        $message->save();
                        
                        $message_notif = Message::create([
                            'chat_id' => $chat->id,
                            'sender_id' => auth()->id(),
                            'message' => "New project uploaded: $url",
                            'type' => 'link',
                            'readed' => false,
                            'page_type' => 'course',
                            'page_id' => 1,
                        ]);
                
                        broadcast(new MessageSent($message_notif));

                        $uploadedFiles[] = $message->message;
                        $processedFiles++;
    
                        // Calculate overall progress
                        $overallProgress = ($processedFiles / $totalFiles) * 100;
    
                        // Send overall progress update
                        echo "data: {" . json_encode([
                            'success' => true,
                            'progress' => $overallProgress,
                            'finished' => $processedFiles === $totalFiles
                        ]) . "}\n\n";
    
                        // Close the SSE connection after the file is processed
                        if ($processedFiles === $totalFiles) {
                            echo "data: {" . json_encode([
                                'success' => true,
                                'message' => 'Files uploaded successfully',
                                'files' => $uploadedFiles,
                                'progress' => 100,
                                'finished' => true
                            ]) . "}\n\n";
                            ob_flush();
                            flush();
                            return; // Exit the method after the last file is processed
                        }
    
                        ob_flush();
                        flush();
                    }
                }
            }
    
            if (empty($uploadedFiles)) {
                throw new \Exception('No files were successfully uploaded.');
            }
    
        } catch (\Exception $e) {
            \Log::error('Upload error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            echo "data: {" . json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]) . "}\n\n";
            ob_flush();
            flush();
            return; // Ensure the method exits after sending the error response
        }
    }
    /**
     * Saves the file with a unique filename and organized storage path.
     *
     * @param UploadedFile $file
     * @return array
     */
    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
    
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
    
        // Group files by the date (week)
        $dateFolder = date("Y-m-W");
    
        // Build the file path
        $filePath = "upload/{$mime}/{$dateFolder}/";
        $finalPath = storage_path("app/" . $filePath);
    
        // Ensure the directory exists
        if (!is_dir($finalPath)) {
            mkdir($finalPath, 0777, true);
        }
    
        // Move the file to the final path
        $file->move($finalPath, $fileName);
    
        return [
            'path' => $filePath,
            'name' => $fileName,
            'mime_type' => $mime,
        ];
    }
    
    /**
     * Create a unique filename for the uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // Filename without extension
    
        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;
    
        return $filename;
    }

}