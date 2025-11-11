<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Chapitres;
use App\Models\Cours;
use App\Models\Lessons;
use App\Models\UrlLesson;
use App\Models\Payments;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GlobalController extends Controller
{

     public function index()
    {
        // Get current month and previous month for comparison
        $currentMonth = Carbon::now()->month;
        $previousMonth = Carbon::now()->subMonth()->month;
        
        // Count apprenants (students)
        $currentApprenants = User::where('role', 'student')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $previousApprenants = User::where('role', 'student')
            ->whereMonth('created_at', $previousMonth)
            ->count();
        $apprenantsGrowth = $this->calculateGrowth($currentApprenants, $previousApprenants);
        
        // Count courses
        $currentCours = Cours::whereMonth('created_at', $currentMonth)->count();
        $previousCours = Cours::whereMonth('created_at', $previousMonth)->count();
        $coursGrowth = $this->calculateGrowth($currentCours, $previousCours);
        
        // Count projects (assuming you have a Project model or use a field in Cours)
        $currentProjets = Lessons::whereMonth('created_at', $currentMonth)->count();
        $previousProjets = Lessons::whereMonth('created_at', $previousMonth)->count();
        $projetsGrowth = $this->calculateGrowth($currentProjets, $previousProjets);
        
        // Calculate total balance
        $totalBalance = User::where('role','client')->where('status', 1)->where('is_demo',0)->count() * 180;
        $currentBalance = Payments::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->count() * 180;
        $previousBalance = Payments::where('status', 'completed')
            ->whereMonth('created_at', $previousMonth)
            ->count() * 180;
        $balanceGrowth = $this->calculateGrowth($currentBalance, $previousBalance);
        
        // Revenue data for the last 12 months
        $revenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payments::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count() * 180;
            $revenueData[] = round($revenue, 2);
        }
        
        // Daily sales for today
        $todaySales = Payments::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->count() * 180;
        
        // Active vs Inactive users
        $activeUsers = User::where('status', true)->count();
        $inactiveUsers = User::where('status', false)->count();
        $totalUsers = $activeUsers + $inactiveUsers;
        $activePercentage = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0;
        $inactivePercentage = 100 - $activePercentage;
        
        // Chapter viewing statistics (assuming you have a views tracking system)
        // Replace this with your actual view tracking logic
        $chapterViews = [
            'chapitre1' => 1,
            'chapitre2' => 2,
            'chapitre3' => 3,
            'chapitre4' => 4,
            'chapitre5' => 5,
            'chapitre6' => 6,
        ];
        
        // Hourly viewing statistics for today
        // Replace this with your actual tracking logic
        $hourlyViews = [];
        for ($hour = 6; $hour <= 13; $hour++) {
            // Mock data - replace with actual query
            $views = rand(20, 80);
            $hourlyViews[] = $views;
        }
        
        // Recent projects
        $recentProjects = Lessons::with('chapitre')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'name' => $lesson->title,
                    'chapitre' => $lesson->chapitre->title ?? 'N/A'
                ];
            });
        
        // Prepare statistics array
        $stats = [
            'apprenants' => [
                'count' => User::where('role', 'client')->count(),
                'growth' => $apprenantsGrowth
            ],
            'cours' => [
                'count' => Cours::count(),
                'growth' => $coursGrowth
            ],
            'projets' => [
                'count' => Lessons::count(),
                'growth' => $projetsGrowth
            ],
            'balance' => [
                'total' => $totalBalance,
                'growth' => $balanceGrowth
            ],
            'todaySales' => $todaySales,
            'revenueData' => $revenueData,
            'minRevenue' => min($revenueData),
            'maxRevenue' => max($revenueData),
            'userStatus' => [
                'active' => $activePercentage,
                'inactive' => $inactivePercentage
            ],
            'chapterViews' => array_values($chapterViews),
            'hourlyViews' => $hourlyViews,
            'recentProjects' => $recentProjects
        ];
        
        return view('admin.index', compact('stats'));
    }

     /**
     * Calculate growth percentage between two values
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        $growth = (($current - $previous) / $previous) * 100;
        return round($growth, 2);
    }

    public function chatIndex()
    {
        return view('admin.chat');
    }

    public function getChatCount()
    {
        // Fetch the total number of messages and unread messages
        $totalChat =Chat::count();
        $totalMessages =Message::count();
        $unreadMessages = Message::where('readed', 'non_lu')->count();

        return response()->json([
            'total_chat' => $totalChat,
            'total_messages' => $totalMessages,
            'unread_messages' => $unreadMessages,
        ]);
    }

    public function cours()
    {
        $cours = Cours::orderBy('visibility', 'asc')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.cours',compact('cours'));
    }

    public function users()
    {        
        return view('admin.users');
    }
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users_edit', compact('user'));
    }
    public function storeEditUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'firstname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'niveau' => 'required|string|max:255',
            'role' => 'required|in:agent,admin',
            'status' => 'required|in:0,1',
            'path_photo' => 'nullable|image|max:1024', // 1MB max
        ];

        // Ajouter les règles de validation pour le mot de passe si fourni
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'firstname' => $request->firstname,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'location' => $request->location,
            'niveau' => $request->niveau,
            'role' => $request->role,
            'language' => 'fr',
            'status' => $request->status,
        ];
   
        // Gérer l'upload de l'image
        if ($request->hasFile('path_photo')) {

            // Supprimer l'ancienne photo si elle existe
            if ($user->path_photo) {
                Storage::disk('public')->delete($user->path_photo);
            }
            $file = $request->file('path_photo');
            $fileName = Storage::disk('public')->putFile('photos', new File($file));
            $updateData['path_photo'] = Storage::url($fileName);

        }

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès.',
            ]);
        } else {
            return redirect()->route('update_profil_user', $user->id)
                ->with('message', 'Utilisateur mis à jour avec succès.');
        }
    }
    public function storeEditClient(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
      
        $rules = [
            'firstname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'path_photo' => 'nullable|image|max:10240', // 1MB max
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'firstname' => $request->firstname,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'location' => $request->location,
        ];
   
        // Gérer l'upload de l'image
        if ($request->hasFile('path_photo')) {

            // Supprimer l'ancienne photo si elle existe
            if ($user->path_photo) {
                Storage::disk('public')->delete($user->path_photo);
            }
            $file = $request->file('path_photo');
            $fileName = Storage::disk('public')->putFile('photos', new File($file));
            $updateData['path_photo'] = Storage::url($fileName);

        }

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès.',
            ]);
        } else {
            return redirect()->back()->with('message', 'Utilisateur mis à jour avec succès.');
        }
    }
    public function storeEditClientNumber(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('loginClient');
        }
      
        $rules = [
            'phone' => 'nullable|string|max:20',
        ];

     

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'phone' => $request->phone,
        ];

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès.',
            ]);
        } else {
            return redirect()->back()->with('message', 'Utilisateur mis à jour avec succès.');
        }
    }
    public function destroyUser($id)
    {
        $userId = Auth::id();
        $user_auth = User::find($userId);
    
        if (!$user_auth) {
            return redirect()->route('loginClient');
        }
        if($user_auth->role !== 'admin'){
            return response()->json(['error' => 'Acces Denied.']);
        }
        if($id == 1){
            return response()->json(['error' => 'Acces Denied (Admin).']);
        }

        $user = User::findOrFail($id);
        // Optionally, check if the authenticated user has permission to delete
        $user->delete();
        return response()->json(['message' => 'Profil supprimé avec succès']);
    }

    public function createAgent(Request $request)
    {
        $rules = [
            'firstname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'location' => 'nullable|string|max:255',
            'niveau' => 'required|string|max:255',
            'role' => 'required|in:agent,admin',
            'status' => 'required|in:0,1',
            'path_photo' => 'nullable|image|max:10240',
            'password' => 'required|min:8|confirmed',
        ];

        $messages = [
            'firstname.required' => 'Le prénom est requis.',
            'name.required' => 'Le nom de famille est requis.',
            'email.required' => 'L\'adresse e-mail est requise.',
            'email.email' => 'L\'adresse e-mail doit être valide.',
            'email.unique' => 'L\'email est déjà utilisé.',
            'phone.unique' => 'Le numéro de téléphone est déjà utilisé.',
            'niveau.required' => 'Le poste est requis.',
            'role.required' => 'Le rôle est requis.',
            'role.in' => 'Le rôle doit être "agent" ou "admin".',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "Actif" ou "Inactif".',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'path_photo.image' => 'La photo doit être une image (jpeg, png, jpg).',
            'path_photo.max' => 'La photo ne doit pas dépasser 10 Mo.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'firstname' => $request->firstname,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'location' => $request->location,
            'niveau' => $request->niveau,
            'role' => $request->role,
            'language' => 'fr',
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('path_photo')) {
            $file = $request->file('path_photo');
            $fileName = Storage::disk('public')->putFile('photos', $file);
            $data['path_photo'] = Storage::url($fileName);
        }

        User::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur ajouté avec succès.',
            ], 200);
        }

        return redirect()->route('users')->with('message', 'Utilisateur ajouté avec succès.');
    }

    public function ajout_cour()
    {
        return view('admin.ajout_cour');
    }

    public function saveCourse(Request $request)
    {
        $userId = auth()->id();
        $courseId = $request->input('course_id');
        $data = $request->only([
            'title', 'keyword', 'top_bar', 'button', 'price_init', 'price_promo', 'description', 'visibility'
        ]);
        
        $data['user_id'] = $userId;
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Storage::disk('public')->putFile('courses_img', new File($file));
            $data['path_banner'] = Storage::url($fileName);
        } else {
            $data['path_banner'] = null;
        }
    
        if ($courseId) {
            $course = Cours::findOrFail($courseId);
            $course->title = $data['title'];
            $course->keyword = $data['keyword'];
            $course->top_bar = $data['top_bar'];
            $course->button = $data['button'];
            $course->price_init = $data['price_init'];
            $course->price_promo = $data['price_promo'];
            $course->description = $data['description'];
            if($data['path_banner']){
                if($course->path_banner){
                    $bannerPath = str_replace('/storage/', '', $course->path_banner);
                    Storage::disk('public')->delete($bannerPath);
                }
                $course->path_banner = $data['path_banner'];
            }
            $course->visibility = $data['visibility'];
            $course->save();
        } else {
            $course = Cours::create($data);
            $courseId = $course->id;
        }
    
        return response()->json([
            'success' => true,
            'courseId' => $courseId,
        ]);
    }
    
    public function getChapters($courseId)
    {
        $chapters = Chapitres::with('lessons')
                                ->where('type','chapitre')
                                ->where('cour_id', $courseId)
                                ->orderBy('order_num', 'asc')
                                ->get();

        return response()->json($chapters);
    }
    
    public function details_chapitre($chapitreId) {
        $chapitre = Chapitres::find($chapitreId);
        if($chapitre){
            return response()->json([
                'success' => true,
                'chapitre' => $chapitre,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'chapitre' => null,
            ]);
        }
       
    }

    public function getCourseDetails($courseId) {
        $course = Cours::with('user')->find($courseId);
    
        return response()->json([
            'success' => true,
            'course' => $course,
        ]);
    }

    public function getCertificaTest($courseId)
    {
        $chapters = Chapitres::with('lessons')->where('type','certifica')->where('cour_id', $courseId)->get();

        return response()->json($chapters);
    }
    
    public function ajout_chapitre(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'path_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024000',
                'path_resume' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240000',
                'timer_hours_chapitre' => 'required|min:0|max:20',
                'timer_minutes_chapitre' => 'required|min:0|max:59',
                'timer_seconds_chapitre' => 'required|min:0|max:59',
                'cour_id' => 'required|exists:cours,id',
                'id_chapitre' => 'nullable|exists:chapitres,id',
                'type' => 'required'
            ]);
            $pathBannerImg = null;
            $data['path_banner'] = "";
            if ($request->hasFile('path_banner')) {
                $banner = $request->file('path_banner');
                $bannerPath = Storage::disk('public')->putFile('chapitre_img', new File($banner));
                $data['path_banner'] = Storage::url($bannerPath);
            }
            
            $pathResumeImg = null;
            $data['path_resume'] = "";
     
            if ($request->hasFile('path_resume')) {
                $resume = $request->file('path_resume');
                $resumePath = Storage::disk('public')->putFile('chapitre_resume', new File($resume));
                $data['path_resume'] = Storage::url($resumePath); 
            }
            
            if(!empty($validatedData['id_chapitre'])){
                $chapter = Chapitres::findOrFail($validatedData['id_chapitre']);
                $chapter->title = $validatedData['title'];
                $chapter->description = $validatedData['description'];
                if($chapter->path_banner){
                    if($data['path_banner']){
                        if($chapter->path_banner){
                            $bannerPath = str_replace('/storage/', '', $chapter->path_banner);
                            Storage::disk('public')->delete($bannerPath);
                        }
                    }
                }
                if($data['path_banner']){
                    $chapter->path_banner = $data['path_banner'];
                }
                if($chapter->path_resume){
                    if($chapter->path_resume){
                        $resumePath = str_replace('/storage/', '', $chapter->path_resume);
                        Storage::disk('public')->delete($resumePath);
                    }
                }
                if($data['path_resume']){
                    $chapter->path_resume = $data['path_resume'];
                }
                $chapter->timer_hours = $validatedData['timer_hours_chapitre']; 
                $chapter->timer_minutes = $validatedData['timer_minutes_chapitre']; 
                $chapter->timer_seconds = $validatedData['timer_seconds_chapitre']; 
                $chapter->save();
            } else {
                $chapter = Chapitres::create([
                    'title' => $validatedData['title'],
                    'description' => $validatedData['description'],
                    'cour_id' => $validatedData['cour_id'],
                    'path_banner' => $data['path_banner'],
                    'path_resume' =>$data['path_resume'], 
                    'timer_hours' => $validatedData['timer_hours_chapitre'], 
                    'timer_minutes' => $validatedData['timer_minutes_chapitre'], 
                    'timer_seconds' => $validatedData['timer_seconds_chapitre'], 
                    'type' => $validatedData['type'],
                ]);
            }
           
    
            return response()->json([
                'success' => true,
                'chapter' => $chapter,
                'message' => 'Chapter saved successfully!',
            ]);
    
        } catch (ValidationException $e) {
            // Return JSON response with validation errors
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function delete_chapitre($id)
    {
        try {
            $chapter = Chapitres::findOrFail($id);
            if ($chapter->path_banner) {
                $bannerPath = str_replace('/storage/', '', $chapter->path_banner); // Convert to relative path
                Storage::disk('public')->delete($bannerPath); // Delete using the relative path
            }
            if ($chapter->path_resume) {
                $resumePath = str_replace('/storage/', '', $chapter->path_resume); 
                Storage::disk('public')->delete($resumePath); 
            }

            $lessons = Lessons::where('chapitre_id', $id)->get();
            foreach ($lessons as $lesson) {
                if ($lesson->path_icon) {
                    $iconPath = str_replace('/storage/', '', $lesson->path_icon);
                    Storage::disk('public')->delete($iconPath);
                }
                if ($lesson->path_video) {
                    $videoPath = str_replace('/storage/', '', $lesson->path_video);
                    Storage::disk('public')->delete($videoPath);
                }
                if ($lesson->path_projet) {
                    $projetPath = str_replace('/storage/', '', $lesson->path_projet);
                    Storage::disk('public')->delete($projetPath);
                }

                // Delete the lesson record
                $lesson->delete();
            }
            $chapter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chapter and associated lessons deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the chapter.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function reorder_chapitres(Request $request)
    {
        $newOrder = $request->input('new_order'); // array of chapter IDs
        $courseId = Chapitres::find($request->input('moved_chapter_id'))->cour_id;

        foreach ($newOrder as $index => $chapterId) {
            Chapitres::where('id', $chapterId)
                ->where('cour_id', $courseId) // make sure it belongs to same course
                ->update(['order_num' => $index]);
        }

        return response()->json(['status' => 'success']);
    }
    public function toggleStatus(Request $request, $id)
    {
        $chapitre = Chapitres::find($id);

        if (!$chapitre) {
            return response()->json(['success' => false, 'message' => 'Chapitre non trouvé'], 404);
        }

        // Inverse le statut (0 devient 1, 1 devient 0)

        $chapitre->status = !$chapitre->status;
        $chapitre->save();

        return response()->json(['success' => true, 'status' => $chapitre->status]);
    }
    //Lesson Projet
    public function getLessonDetails($id)
    {
        try {
            $lesson = Lessons::with('urls')->findOrFail($id);

            return response()->json([
                'success' => true,
                'lesson' => $lesson,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found.',
            ], 404);
        }
    }
    public function ajout_lesson(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'chapitre_id' => 'required|exists:chapitres,id',
                'path_img' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'path_video' => 'nullable|mimes:mp4,mov,avi',
                'path_source' => 'nullable',
                'lessonVideoHours' => 'required|min:0|max:20',
                'lessonVideoMinutes' => 'required|min:0|max:59',
                'lessonVideoSeconds' => 'required|min:0|max:59',
                'idLesson' => 'nullable',
                'typeModal' => 'required',
                'order_num' => 'nullable|integer|min:1',
                'link_titles' => 'nullable|array',
                'link_titles.*' => 'required_with:link_urls.*|string|max:255',
                'link_urls' => 'nullable|array',
                'link_urls.*' => 'required_with:link_titles.*|url',
                'delete_source_file' => 'nullable|boolean',
            ]);
            
            $data = [];
            $pathLessonImg = null;
            if ($request->hasFile('path_img')) {
                $image = $request->file('path_img');
                $imagePath = Storage::disk('public')->putFile('lessons_img', new File($image));
                $data['path_icon'] = Storage::url($imagePath);
            }
    
            $pathLessonVid = null;
            if ($request->hasFile('path_video')) {
                $video = $request->file('path_video');
                $videoPath = Storage::disk('public')->putFile('lessons_video', new File($video));
                $data['path_video'] = Storage::url($videoPath);
            }

            $pathLessonProjet = null;
            if ($request->hasFile('path_source')) {
                $video = $request->file('path_source');
                $videoPath = Storage::disk('public')->putFile('lessons_projets', $video);
                $data['path_projet'] = Storage::url($videoPath);
            }
            
            if($validatedData['idLesson']){
                $lesson = Lessons::findOrFail($validatedData['idLesson']);
                $originalOrder = $lesson->order_num;
                
                $lesson->title = $validatedData['title'];
                $lesson->description = $validatedData['description'];
                if($request->hasFile('path_img')){
                    if($lesson->path_icon){
                        $iconPath = str_replace('/storage/', '', $lesson->path_icon);
                        Storage::disk('public')->delete($iconPath);
                    }
                    $lesson->path_icon = $data['path_icon'];
                }
                if($request->hasFile('path_video')){
                    if($lesson->path_video){
                        $videoPath = str_replace('/storage/', '', $lesson->path_video);
                        Storage::disk('public')->delete($videoPath);
                    }
                    $lesson->path_video = $data['path_video'];
                }
                if ($request->hasFile('path_source')) {
                    if ($lesson->path_projet) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $lesson->path_projet));
                    }
                    $lesson->path_projet = $data['path_projet'];
                } elseif ($request->input('delete_source_file') == true) {
                    if ($lesson->path_projet) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $lesson->path_projet));
                    }
                    $lesson->path_projet = null;
                }
                $lesson->lessonVideoHours = $validatedData['lessonVideoHours'];
                $lesson->lessonVideoMinutes = $validatedData['lessonVideoMinutes'];
                $lesson->lessonVideoSeconds = $validatedData['lessonVideoSeconds'];
                $lesson->visibility = 1;
                $newOrder = $request->input('new_order');
            
                if ($newOrder && (int)$newOrder !== $originalOrder) {
               
                    $this->reorderLessonsInChapter($lesson->chapitre_id, $lesson->id, (int)$newOrder);
                }
        
                $lesson->save();
                
            } else {
                $lastOrder = Lessons::where('chapitre_id', $validatedData['chapitre_id'])->max('order_num') ?? 0;
                $order_num = $lastOrder + 1;
                $lesson = Lessons::create([
                    'title' => $validatedData['title'],
                    'description' => $validatedData['description'],
                    'path_icon' => $data['path_icon'],
                    'path_video' => $data['path_video'] ?? null, 
                    'path_projet' => $data['path_projet'] ?? '',
                    'lessonVideoHours' => $validatedData['lessonVideoHours'],
                    'lessonVideoMinutes' => $validatedData['lessonVideoMinutes'],
                    'lessonVideoSeconds' => $validatedData['lessonVideoSeconds'],
                    'type' => $validatedData['typeModal'],
                    'visibility' => 1,
                    'chapitre_id' => $validatedData['chapitre_id'],
                    'order_num' => $order_num,
                ]);
            }
           
            $lesson->urls()->delete();

            $linkTitles = $request->input('link_titles', []);
            $linkUrls = $request->input('link_urls', []);
      
            if (count($linkTitles) > 0) {
                for ($i = 0; $i < count($linkTitles); $i++) {
                    if (!empty($linkTitles[$i]) && !empty($linkUrls[$i])) {
                        UrlLesson::create([
                            'lesson_id' => $lesson->id,
                            'title' => $linkTitles[$i],
                            'url' => $linkUrls[$i],
                        ]);
                    }
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'lesson' => $lesson,
                'message' => 'lesson saved successfully!',
            ]);
    
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }
    /**
 * Reorders lessons within a specific chapter.
 *
 * @param string $chapitreId The ID of the chapter where reordering happens.
 * @param string $lessonId The ID of the lesson being moved.
 * @param int $newOrder The new order number for the lesson.
 */
private function reorderLessonsInChapter($chapitreId, $lessonId, $newOrder)
{
    DB::transaction(function () use ($chapitreId, $lessonId, $newOrder) {
        $lessonToMove = Lessons::findOrFail($lessonId);
        $oldOrder = $lessonToMove->order_num;

        // Do nothing if the order hasn't changed
        if ($newOrder == $oldOrder) {
            return;
        }

        // Temporarily move the lesson out of the way to prevent unique constraint issues
        $lessonToMove->order_num = -1; // or null if the column is nullable
        $lessonToMove->save();

        if ($newOrder < $oldOrder) {
            // Moved UP (e.g., from 5 to 2). Shift lessons between 2 and 4 (inclusive) DOWN by one.
            Lessons::where('chapitre_id', $chapitreId)
                   ->where('order_num', '>=', $newOrder)
                   ->where('order_num', '<', $oldOrder)
                   ->increment('order_num');
        } else { // $newOrder > $oldOrder
            // Moved DOWN (e.g., from 2 to 5). Shift lessons between 3 and 5 (inclusive) UP by one.
            Lessons::where('chapitre_id', $chapitreId)
                   ->where('order_num', '>', $oldOrder)
                   ->where('order_num', '<=', $newOrder)
                   ->decrement('order_num');
        }

        // Place the moved lesson into its new, final position
        $lessonToMove->order_num = $newOrder;
        $lessonToMove->save();
    });
}
    public function reorder_lessons(Request $request)
    {
        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'new_order' => 'required|integer|min:1',
        ]);

        $lessonId = $validatedData['lesson_id'];
        $newOrder = $validatedData['new_order'];

        try {
            // Use a database transaction to ensure data integrity
            DB::transaction(function () use ($lessonId, $newOrder) {
                $lessonToMove = Lessons::findOrFail($lessonId);
                $oldOrder = $lessonToMove->order_num;
                $chapitreId = $lessonToMove->chapitre_id;

                // Do nothing if the order hasn't changed
                if ($oldOrder == $newOrder) {
                    return;
                }

                // Temporarily set the moving lesson's order to null (or a value outside the range)
                // to avoid unique constraint violations if you have them.
                $lessonToMove->order_num = null;
                $lessonToMove->save();

                if ($newOrder < $oldOrder) {
                    // The lesson is moved UP (e.g., from 4 to 2).
                    // Increment the order of lessons between the new position and the old one.
                    // Lessons at positions 2 and 3 will be shifted to 3 and 4.
                    Lessons::where('chapitre_id', $chapitreId)
                        ->where('order_num', '>=', $newOrder)
                        ->where('order_num', '<', $oldOrder)
                        ->increment('order_num');
                } else { // $newOrder > $oldOrder
                    // The lesson is moved DOWN (e.g., from 2 to 4).
                    // Decrement the order of lessons between the old position and the new one.
                    // Lessons at positions 3 and 4 will be shifted to 2 and 3.
                    Lessons::where('chapitre_id', $chapitreId)
                        ->where('order_num', '>', $oldOrder)
                        ->where('order_num', '<=', $newOrder)
                        ->decrement('order_num');
                }

                // Finally, place the moved lesson in its new position
                $lessonToMove->order_num = $newOrder;
                $lessonToMove->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Lessons reordered successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while reordering.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function delete_lesson_projet($id)
    {
        try {
            $lessons = Lessons::findOrFail($id);
            if ($lessons->path_icon) {
                $iconPath = str_replace('/storage/', '', $lessons->path_icon);
                Storage::disk('public')->delete($iconPath);
            }
            if ($lessons->path_video) {
                $videoPath = str_replace('/storage/', '', $lessons->path_video); 
                Storage::disk('public')->delete($videoPath); 
            }
            if ($lessons->path_projet) {
                $projetPath = str_replace('/storage/', '', $lessons->path_projet); 
                Storage::disk('public')->delete($projetPath); 
            }
            $lessons->delete();

            return response()->json([
                'success' => true,
                'message' => 'deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    private function getFileExtension($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            // Add more MIME types as needed
        ];

        return $extensions[$mimeType] ?? 'bin'; // Default to 'bin' if MIME type is unknown
    }
    // Retrieve course data
    public function getCourse($id)
    {
        $course = Cours::findOrFail($id);

        return response()->json([
            'success' => true,
            'course' => $course,
        ]);
    }

    public function delete_cour($id)
    {
        try {
            $cour = Cours::findOrFail($id);
            if($cour){
                $cour->visibility = 2;
                $cour->save();
            }
            

            return redirect()->back()->with('success', 'Le cours et tous les chapitres et leçons associés ont été archivé avec succès.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Cours non trouvé.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression du cours : ' . $e->getMessage());
        }
    }

    //Clients
    public function getClients()
    {
        return view('admin.clients');
    }
    
}