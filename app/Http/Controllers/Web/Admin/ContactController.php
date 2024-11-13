<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Base\Services\ImageUploader\ImageUploaderContract;

class ContactController extends BaseController
{
    protected $contact;
      /**
     * The
     *
     * @var App\Base\Services\ImageUploader\ImageUploaderContract
     */
    protected $imageUploader;

    /**
     * ContactController constructor.
     *
     * @param \App\Models\Admin\Contact $contact
     */
    public function __construct(Contact $contact, ImageUploaderContract $imageUploader,)
    {
        $this->contact = $contact;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        $page = trans('pages_names.contact');

        $main_menu = 'manage-contact';
        $sub_menu = 'contact';

        return view('admin.contact.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {

        $query = $this->contact->query();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.contact._contact', compact('results'));
    }

    public function show($id){
        $page = trans('pages_names.contact');

        $main_menu = 'manage-contact';
        $sub_menu = 'contact';

        $result = $this->contact->find($id);
        return view('admin.contact.show', compact('result','page', 'main_menu', 'sub_menu'));
    }

    public function is_view(Request $request){
        // dd($request);
        $contact = Contact::find($request->contactId);

        if ($contact) {
            $contact->status = $contact->status === 'open' ? 'close' : 'open';
            $contact->save();

            return redirect()->back();
        }
    }
}
