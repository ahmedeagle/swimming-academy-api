<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Coach;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Validator;
use Hash;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::get();
        return view('admin.categories.index', compact('categories'));
    }


    public function loadHeroes(Request $request)
    {
        $category = Category::find($request->category_id);
        if (!$category) {
            return response()->json(['content' => null]);
        }
        $users = $category->allUsers;
        $view = view('admin.teams.heroes', compact('users'))->with('message', ' عفوا لايوجد اي لاعبين في هذا القسم فضلا قم باضافه لاعبين للقسم او اختر قسم اخر ثم المحاوله مجددا ')->renderSections();
        return response()->json([
            'content' => $view['main'],
        ]);
    }


    public function loadCategories(Request $request)
    {
        $academy = Academy::findOrfail($request->academy_id);
        $categories = $academy->categories;
        $coaches = Coach::active()->where('academy_id', $request->academy_id)->get();
        $categoryView = view('admin.academies.categories', compact('categories'))->renderSections();
        $coachView = view('admin.academies.coaches', compact('coaches'))->renderSections();

        return response()->json([
            'content' => $categoryView['main'],
            'coachesContent' => $coachView['main']
        ]);
    }


    public function loadCategoryTeams(Request $request)
    {
        $category = Category::findOrFail($request->category_id);
        $teams = Team::whereHas('times')->where('category_id',$request->category_id)->get();
        $view = view('admin.categories.teams', compact('teams'))->renderSections();
        return response()->json([
            'content' => $view['main'],
        ]);
    }


    public function loadCoaches(Request $request)
    {
        $category = Category::findOrFail($request->category_id);
        $coaches = $category->coaches;
        $view = view('admin.categories.coaches', compact('coaches'))->renderSections();
        return response()->json([
            'content' => $view['main'],
        ]);
    }

    public function create()
    {
        $academies = Academy::active()->select('id', 'name_ar as name')->get();
        return view('admin.categories.create', compact('academies'));
    }


    public function store(Request $request)
    {
        try {
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'max' => 'لابد الايزيد عدد اخرف الحقب عن 100 حرف بالمسافات ',
                'academy_id.exists' => 'الاكاديمية غير موجوده لدينا '
            ];

            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
                'academy_id' => 'required|exists:academies,id'
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request
            Category::create($request->except('_token'));
            notify()->success('تمت الاضافة بنجاح ');
            return redirect()->route('admin.categories.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update($id, Request $request)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                notify()->success(' القسم  غير موجوده لدينا ');
                return redirect()->route('admin.categories.edit', $id);
            }

            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'max' => 'لابد الايزيد عدد اخرف الحقب عن 100 حرف بالمسافات ',
            ];
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request
            $category = Category::find($id);
            $category->update($request->except('_token'));
            notify()->success('تمت التعديل بنجاح ');
            return redirect()->route('admin.categories.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        notify()->success('تمت حذف القسم بكل محتواها بنجاح ');
        return redirect()->route('admin.categories.all');
    }
}
