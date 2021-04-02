<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\JsonStructureCreateRequest;
use App\Http\Resources\JsonStructoreResource;
use App\JsonStructure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonStructureController extends Controller
{

    /**
    * @SWG\Get(
    *         path="/api/jsonstructures",
    *         tags={"ADMIN/JSONTRUCTURE"},
    *         summary="Get list jsonstructure",
    *         @SWG\Parameter(
    *             name="page",
    *             description="Pagination page",
    *             in="query",
    *             type="integer"
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
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
    public function index(){
        $jsonStructure = JsonStructure::paginate();

        return JsonStructoreResource::collection($jsonStructure);
    }

    /**
    * @SWG\Get(
    *         path="/api/mobile/jsonstructure/{id}",
    *         tags={"MOBILE/JSONTRUCTURE"},
    *         summary="Get list Jsonstructure of Job",
    *         @SWG\Parameter(
    *             name="id",
    *             description="A ID string identifying this JsonStructure.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
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
    
    /**
    * @SWG\Get(
    *         path="/api/jsonstructures/{id}",
    *         tags={"ADMIN/JSONTRUCTURE"},
    *         summary="Get a JsonStructure by ID",
    *         @SWG\Parameter(
    *             name="id",
    *             description="A ID string identifying this jsonstructure.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
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
    public function show($id){

        return new JsonStructoreResource(JsonStructure::find($id));
    }


    /**
    * @SWG\Post(
    *         path="/api/jsonstructures",
    *         tags={"ADMIN/JSONTRUCTURE"},
    *         summary="Create a JsonStructures",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="content", type="string", example="string"),
    *           ),
    *               
    *      ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */

    public function store(JsonStructureCreateRequest $request){

        $jsonStructure = JsonStructure::create($request->only('code', 'content'));

        return response($jsonStructure, Response::HTTP_CREATED);
    }


    /**
    * @SWG\Put(
    *         path="/api/jsontructures/{id}",
    *         tags={"ADMIN/JSONTRUCTURE"},
    *         summary="Put a JsonStructure by ID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="content", type="string", example="string"),
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="id",
    *             description="A ID string identifying this jsonstructure.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */
    /**
    * @SWG\Patch(
    *         path="/api/jsonstructure/{id}",
    *         tags={"ADMIN/JSONTRUCTURE"},
    *         summary="Put a JsonStructure by ID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="content", type="string", example="string"),
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="id",
    *             description="A ID string identifying this jsonstructure.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */

    public function update(Request $request, $id){

        // $product = JsonStructure::find($id);

        // $product->update($request->only('code', 'content'));

        // return response($product, Response::HTTP_ACCEPTED);


        $jsonStructure = JsonStructure::find($id)->first();
        
        $data = $request->all();
        
        $jsonStructure->update($data);
        
       return response(new JsonStructoreResource($jsonStructure), Response::HTTP_ACCEPTED);
    
    }


    /**
    * @SWG\Delete(
    *         path="/api/jsonstructure/{id}",
    *         tags={"ADMIN/JSONTRUCTURE"},
    *         summary="Delete a JsonStructe by ID",
    *         @SWG\Parameter(
    *             name="id",
    *             description="A ID string identifying this jsonstructure.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
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

    public function destroy($id){
        
        JsonStructure::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

}
