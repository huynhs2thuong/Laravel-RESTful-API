<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Photo_elevation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Uuid;

class Photo_ElevationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
    * @SWG\Get(
    *         path="/api/photo_elevations",
    *         tags={"PUBLIC"},
    *         summary="Get list photo-elevation",
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
        $photo_elevation = Photo_elevation::orderBy('id','asc')->get();
        return response()->json([
            'status' => 'Success',
            'message' => Exception::LIST_PHOTO_ELEVATION,
            'data' => $photo_elevation
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
        $photo_elevation = Photo_elevation::create($data);

        return response($photo_elevation, Response::HTTP_CREATED);
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
