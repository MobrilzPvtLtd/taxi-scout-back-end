<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\ApiController;
use App\Models\Admin\Faq;
use App\Models\Admin\Gallery;
use App\Models\Admin\OurTeam;
use App\Models\Admin\Partner;
use App\Transformers\BlogTransformer;

class StoreFrontController extends ApiController
{
    /**
     * Get all the blogs.
     *@hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function gallery()
    {
        $galleries = Gallery::where('active', true)->get();

        $galleryData = [];

        foreach ($galleries as $gallery) {
            $params= [
                'id' => $gallery->id,
                'image' => asset('gallery/'.$gallery->image),
                'active'=>(bool)$gallery->active,
                'created_at'=>$gallery->created_at,
                'updated_at' => $gallery->updated_at,
            ];

            $galleryData[] = $params;
        }

        return $this->respondOk($galleryData);
    }

    public function faq()
    {
        $faqs = Faq::where('active', true)->get();

        $faqData = [];

        foreach ($faqs as $faq) {
            $params= [
                'id' => $faq->id,
                'question'=>$faq->question,
                'answer'=>$faq->answer,
                'active'=>(bool)$faq->active,
                'created_at'=>$faq->created_at,
                'updated_at' => $faq->updated_at,
            ];

            $faqData[] = $params;
        }

        return $this->respondOk($faqData);
    }

    public function ourTeam()
    {
        $ourTeam = OurTeam::where('status', true)->get();

        $ourTeamData = [];

        foreach ($ourTeam as $team) {
            $params= [
                'id' => $team->id,
                'title'=>$team->title,
                'name'=>$team->name,
                'mobile'=>$team->mobile,
                'email'=>$team->email,
                'image' => asset('team/'.$team->image),
                'description'=>$team->description,
                'status'=>(bool)$team->status,
                'created_at'=>$team->created_at,
                'updated_at' => $team->updated_at,
            ];

            $ourTeamData[] = $params;
        }

        return $this->respondOk($ourTeamData);
    }

    public function ourPartner()
    {
        $partners = Partner::where('active', true)->get();

        $partnerData = [];

        foreach ($partners as $partner) {
            $params= [
                'id' => $partner->id,
                'image' => asset('partner/'.$partner->image),
                'active'=>(bool)$partner->active,
                'created_at'=>$partner->created_at,
                'updated_at' => $partner->updated_at,
            ];

            $partnerData[] = $params;
        }

        return $this->respondOk($partnerData);
    }

}
