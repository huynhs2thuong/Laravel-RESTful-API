<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Photo_tag;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Uuid;

class Photo_TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
    * @SWG\Get(
    *         path="/api/photo_tag",
    *         tags={"PUBLIC"},
    *         summary="Get list photo-tag",
    *         @SWG\Response(
    *             response=200,
    *             description="Successful operation"
    *         ),

    *         @SWG\Response(
    *             response=500,
    *             description="Server error"
    *         ),
    *   security={{"ApiKeyAuth":{}}},
    * )
    */
    public function index()
    {
        $photo_tag = Photo_tag::orderBy('id','desc')->get();
        return response()->json([
            'status' => 'Success',
            'message' => Exception::LIST_PHOTO_TAG,
            'data' => $photo_tag
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $sid = Uuid::generate()->string;
        $data['sid'] = $sid;
        $photo_tag = Photo_tag::create($data);

        return response($photo_tag, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
