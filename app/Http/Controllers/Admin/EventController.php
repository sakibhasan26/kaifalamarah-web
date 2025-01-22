<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Campaign;
use App\Models\Admin\Event;
use Illuminate\Support\Str;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->get();
    }

    /**
     * Mehtod for show event page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function index()
    {
        $page_title = __('Events');
        $languages = $this->languages;
        $data = Event::with('category:id,name')->orderBy('id', 'desc')->paginate();
        $allCategory = CategoryType::where('type', 2)->orderByDesc('id')->get();
        $section_slug = Str::slug(SiteSectionConst::EVENT_SECTION);
        $head_data = SiteSections::getData($section_slug)->first();
        return view('admin.sections.events.index', compact(
            'page_title',
            'languages',
            'data',
            'allCategory',
            'head_data'
        ));
    }

    /**
     * Mehtod for update campaign section heading and title
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function headingUpdate(Request $request) {

        $basic_field_name = [
            // 'title' => "required|string|max:100",
            'heading' => "required|string|max:100"
        ];

        $slug = Str::slug(SiteSectionConst::EVENT_SECTION);

        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Mehtod for store campaign item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category'      => 'required',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        $details_field = [
            'details'     => "required|string"
        ];
        $title_filed = [
            'title'     => "required|string",
        ];
        $tags = [
            'tags'     => "required|array",
        ];

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'event-add');
        }
        $validated = $validator->validate();

        // Multiple language data set
        $language_wise_desc = $this->contentValidate($request, $details_field);
        $language_wise_title = $this->contentValidate($request, $title_filed);
        $language_wise_tags = $this->contentValidate($request, $tags);

        $desc_data['language']  = $language_wise_desc;
        $title_data['language'] = $language_wise_title;
        $tag_data['language']   = $language_wise_tags;


        $validated['details']    = $desc_data;
        $validated['category_id'] = $validated['category'];
        $validated['title']      = $title_data;
        $validated['tags']       = $tag_data;
        $validated['slug']       = Str::slug($title_data['language']['en']['title']);
        $validated['created_at'] = now();
        $validated['admin_id']   = Auth::user()->id;

        // Check Image File is Available or not
        if ($request->hasFile('image')) {
            $image = get_files_from_fileholder($request, 'image');
            $upload = upload_files_from_path_dynamic($image, 'events');
            $validated['image'] = $upload;
        }

        try {
            Event::create($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Event added successfully!')]]);
    }

    /**
     * Mehtod for show event eidt page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function edit($id)
    {
        $page_title = __('Event Edit');
        $languages = $this->languages;
        $data = Event::with('category:id,name')->findOrFail($id);
        $allCategory = CategoryType::where('type', 2)->orderByDesc('id')->get();

        return view('admin.sections.events.edit', compact(
            'page_title',
            'languages',
            'data',
            'allCategory',
        ));
    }

    /**
     * Mehtod for update event
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'target'   => 'required',
            'image'    => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        $details_field = [
            'details'     => "required|string"
        ];
        $title_filed = [
            'title'     => "required|string",
        ];
        $tags = [
            'tags'     => "required|array",
        ];

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'event-edit');
        }
        $validated = $validator->validate();

        $event = Event::findOrFail($validated['target']);

        // Multiple language data set
        $language_wise_desc = $this->contentValidate($request, $details_field);
        $language_wise_title = $this->contentValidate($request, $title_filed);
        $language_wise_tags = $this->contentValidate($request, $tags);

        $desc_data['language']  = $language_wise_desc;
        $title_data['language'] = $language_wise_title;
        $tag_data['language']   = $language_wise_tags;


        $validated['details']    = $desc_data;
        $validated['category_id'] = $validated['category'];
        $validated['title']      = $title_data;
        $validated['tags']       = $tag_data;
        $validated['slug']       = Str::slug($title_data['language']['en']['title']);
        $validated['created_at'] = now();
        $validated['admin_id']   = Auth::user()->id;

        // Check Image File is Available or not
        if ($request->hasFile('image')) {
            $image = get_files_from_fileholder($request, 'image');
            $upload = upload_files_from_path_dynamic($image, 'events', $event->image);
            $validated['image'] = $upload;
        }

        try {
            $event->update($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('admin.events.index')->with(['success' => [__('Event updated successfully!')]]);
    }

    /**
     * Mehtod for status update event
     * @method PUT
     * @param \Illuminate\Http\Request  $request
     */
    public function statusUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'status'                    => 'required|boolean',
            'data_target'               => 'required|string',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error,null,400);
        }
        $validated = $validator->safe()->all();
        $id = $validated['data_target'];

        $event = Event::findOrFail($id);

        if(!$event) {
            $error = ['error' => [__('Event record not found in our system')]];
            return Response::error($error,null,404);
        }

        try{
            $event->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        }catch(Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again')]];
            return Response::error($error,null,500);
        }

        $success = ['success' => [__('Event status updated successfully!')]];
        return Response::success($success,null,200);
    }

    /**
     * Mehtod for delete event
     * @method PUT
     * @param \Illuminate\Http\Request  $request
     */
    public function delete(Request $request) {
        $request->validate([
            'target'    => 'required|string',
        ]);

        $event = Event::findOrFail($request->target);

        try{
            $image_link = get_files_path('events') . '/' . $event->image;
            delete_file($image_link);
            $event->delete();
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Event delete successfully!')]]);
    }

    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages()
    {
        $languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->select("code", "name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request, $basic_field_name, $modal = null)
    {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach ($request->all() as $input_name => $input_value) {
            foreach ($languages as $language) {
                $input_name_check = explode("_", $input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_", $input_name_check);
                if ($input_lang_code == $language['code']) {
                    if (array_key_exists($input_name_check, $basic_field_name)) {
                        $langCode = $language['code'];
                        if ($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        } else {
                            $validation_rules[$input_name] = str_replace("required", "nullable", $basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if ($modal == null) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        } else {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal", $modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }
}
