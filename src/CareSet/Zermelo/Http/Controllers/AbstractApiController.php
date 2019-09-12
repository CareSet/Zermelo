<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 5/2/19
 * Time: 12:33 PM
 */

namespace CareSet\Zermelo\Http\Controllers;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AbstractApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
