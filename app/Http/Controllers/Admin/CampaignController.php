<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->get();
    }

    /**
     * Mehtod for show campaign section page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function index() {
        $page_title = __('Donation Section');
        $languages = $this->languages;
        $data = Campaign::orderBy('id', 'desc')->paginate();
        $section_slug = Str::slug(SiteSectionConst::CAMPAIGNS_SECTION);
        $head_data = SiteSections::getData($section_slug)->first();

        return view('admin.sections.campaigns.index',compact(
            'page_title',
            'languages',
            'data',
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

        $slug = Str::slug(SiteSectionConst::CAMPAIGNS_SECTION);

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
    public function campaignItemStore(Request $request) {

        $validator = Validator::make($request->all(),[
            'our_goal'      => 'required',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        $desc_field = [
            'desc'     => "required|string"
        ];
        $title_filed = [
            'title'     => "required|string",
        ];

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','campaign-add');
        }
        $validated = $validator->validate();

        // Multiple language data set
        $language_wise_desc = $this->contentValidate($request,$desc_field);
        $language_wise_title = $this->contentValidate($request,$title_filed);

        $desc_data['language'] = $language_wise_desc;
        $title_data['language'] = $language_wise_title;


        $validated['desc']            = $desc_data;
        $validated['title']           = $title_data;
        $validated['to_go']           = $validated['our_goal'];
        $validated['slug']            = Str::slug($title_data['language']['en']['title']);
        $validated['created_at']      = now();
        $validated['admin_id']        = Auth::user()->id;

        // Check Image File is Available or not
        if($request->hasFile('image')) {
            $image = get_files_from_fileholder($request,'image');
            $upload = upload_files_from_path_dynamic($image,'campaigns');
            $validated['image'] = $upload;
        }

        try{
            Campaign::create($validated);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Campaign item added successfully!')]]);
    }

    /**
     * Mehtod for show campaign section page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function edit($id) {
        $page_title = __('Edit Campaign');
        $languages = $this->languages;
        $data = Campaign::findOrFail($id);

        return view('admin.sections.campaigns.edit',compact(
            'page_title',
            'languages',
            'data',
        ));
    }

    /**
     * Mehtod for update campaign item
     * @method POST
     * @param \Illuminate\Http\Request  $request
     */
    public function campaignItemUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'our_goal'      => 'required',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
            'target'        => 'required|string',
        ]);

        $desc_field = [
            'desc'     => "required|string"
        ];
        $title_filed = [
            'title'     => "required|string"
        ];

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','campaign-add');
        }
        $validated = $validator->validate();

        $campaign = Campaign::findOrFail($validated['target']);

        // Multiple language data set
        $language_wise_desc = $this->contentValidate($request,$desc_field);
        $language_wise_title = $this->contentValidate($request,$title_filed);

        $desc_data['language'] = $language_wise_desc;
        $title_data['language'] = $language_wise_title;


        $update_our_goal = $validated['our_goal'];
        $old_our_goal = $campaign->our_goal;
        $old_to_go = $campaign->to_go;

        if($update_our_goal > $old_our_goal){
            $amount = $update_our_goal - $old_our_goal;
            $update_to_go = $old_to_go + $amount;
        }elseif($update_our_goal < $old_our_goal){
            $amount =  $old_our_goal - $update_our_goal;
            $update_to_go = $old_to_go - $amount;
        }else{
            $update_to_go = $old_to_go;
        }

        $validated['desc']            = $desc_data;
        $validated['to_go']           = $update_to_go;
        $validated['title']           = $title_data;
        $validated['slug']            = Str::slug($title_data['language']['en']['title']);
        $validated['created_at']      = now();
        $validated['admin_id']        = Auth::user()->id;

        // Check Image File is Available or not
        if($request->hasFile('image')) {
            $image = get_files_from_fileholder($request,'image');
            $upload = upload_files_from_path_dynamic($image,'campaigns', $campaign->image);
            $validated['image'] = $upload;
        }

        try{
            $campaign->update($validated);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('admin.campaigns.index')->with(['success' => [__('Campaign item updated successfully!')]]);
    }

    public function campaignItemDelete(Request $request) {
        $request->validate([
            'target'    => 'required|string',
        ]);

        $campaign = Campaign::findOrFail($request->target);

        try{
            $image_link = get_files_path('campaigns') . '/' . $campaign->image;
            delete_file($image_link);
            $campaign->delete();
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Campaigns delete successfully!')]]);
    }

    /**
     * Mehtod for status update campaign item
     * @method PUT
     * @param \Illuminate\Http\Request  $request
     */
    public function campaignItemStatusUpdate(Request $request) {
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

        $campaign = Campaign::findOrFail($id);

        if(!$campaign) {
            $error = ['error' => [__('Campaign record not found in our system')]];
            return Response::error($error,null,404);
        }

        try{
            $campaign->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        }catch(Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again')]];
            return Response::error($error,null,500);
        }

        $success = ['success' => [__('Campaign status updated successfully!')]];
        return Response::success($success,null,200);
    }


    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages() {
        $languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->select("code","name")->get()->toArray();
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
    public function contentValidate($request,$basic_field_name,$modal = null) {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach($request->all() as $input_name => $input_value) {
            foreach($languages as $language) {
                $input_name_check = explode("_",$input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_",$input_name_check);
                if($input_lang_code == $language['code']) {
                    if(array_key_exists($input_name_check,$basic_field_name)) {
                        $langCode = $language['code'];
                        if($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        }else {
                            $validation_rules[$input_name] = str_replace("required","nullable",$basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if($modal == null) {
            $validated = Validator::make($request->all(),$validation_rules)->validate();
        }else {
            $validator = Validator::make($request->all(),$validation_rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal",$modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }
}
