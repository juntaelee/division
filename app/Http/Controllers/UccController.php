<?php

namespace App\Http\Controllers;

use App\ProductLogWatchUCC;
use App\Status;
use App\AdultUCC;
use App\Http\Redis;
use App\Exceptions\CustomException;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class UccController extends Controller
{

    public function __construct()
    {
    }

    /*
     * UCC 리스트 조회
     */
    public function ucc(Request $request)
    {
        $this->validate($request, [
            'current' => 'required|numeric',
            'rowCount' => 'required|numeric'
        ]);

        $current = (int)$request->current;
        $limit = (int)$request->rowCount;
        $offset = ($current-1)* $limit;

        $where = [];
        if (in_array($request->where, array('Youtube','TVPot','Pandora','Naver','Nate','Afreeca Sport','Freechal','Mgoon','Diodeo','Migame','Cyworld','GomTV','MySpace','Yahoo Video','Paran','Dailymotion'))) {
            $where[] = ['Where', '=', $request->input('where')];
        }

        if (in_array($request->adult, array('Y', 'N', 'X'))) {
            $where[] = ['Adult', '=', $request->input('adult')];
        }

        $list = ProductLogWatchUCC::where($where)
                                    ->offset($offset)
                                    ->limit($limit)
                                    ->orderBy('Log', 'desc')
                                    ->select('Log', 'Where', 'Player', 'Title', 'Thumbnail', 'Referer', 'Adult', 'Date_Entry')
                                    ->get();

        $total = ProductLogWatchUCC::where($where)
                                    ->count();

        $ucc = [
            'list' => $list,
            'total' => $total
        ];

        return $ucc;
    }

    /*
     * UCC 썸네일 이미지에 19/Check 표시 
     */
    public function thumbnail(Request $request)
    {
        /*$this->validate($request, [
            'pk' => 'required|numeric'
        ]);

        $pk = (int)$request->pk;

        $ucc = ProductLogWatchUCC::find($pk);

        $url = str_replace('https://', 'http://', $ucc->Thumbnail);

        $full_filename = parse_url($url, PHP_URL_PATH);

        if (preg_match("/\.png/i", $full_filename)) {
            $im = imagecreatefrompng($url);
        } elseif (preg_match("/\.jp(e)?g/i", $full_filename)) {
            $im = imagecreatefromjpeg($url);
        } else {
            $im = imagecreatefromgif($url);
        }

        $old_x = imagesx($im);
        $old_y = imagesy($im);

        $new_x = 120;
        $new_y = 90;

        $thumbnail = ImageCreateTrueColor($new_x, $new_y);

        imagecopyresampled($thumbnail, $im, 0, 0, 0, 0, $new_x, $new_y, $old_x, $old_y);

        if ($ucc['Adult'] == 'Y') {
            $adult = imagecreatefrompng(resource_path('assets/images/ic_19.png'));
            imagecopy($thumbnail, $adult, imagesx($thumbnail) - 27, imagesy($thumbnail) - 27, 0, 0, imagesx($adult), imagesy($adult));
        } elseif ($ucc['Adult'] == 'N') {
            $adult = imagecreatefromgif(resource_path('assets/images/check_null.gif'));
            imagecopy($thumbnail, $adult, imagesx($thumbnail) - 27, imagesy($thumbnail) - 27, 0, 0, imagesx($adult), imagesy($adult));
        }

        header('Content-type: image/jpeg');
        imagejpeg($thumbnail);

        imagedestroy($im);
        imagedestroy($thumbnail);*/

        $this->validate($request, [
            'pk' => 'required|numeric'
        ]);

        $pk = (int)$request->pk;

        $ucc = ProductLogWatchUCC::find($pk);

        $url = str_replace('https://', 'http://', $ucc->Thumbnail);

        $img = Image::make($url);

        $img->resize(120, 90);

        if ($ucc['Adult'] == 'Y') {
            $img->insert(resource_path('assets/images/ic_19.png'), 'bottom-right', 10, 10);
        } elseif ($ucc['Adult'] == 'N') {
            $img->insert(resource_path('assets/images/check_null.gif'), 'bottom-right', 10, 10);
        }

        return $img->response();
    }

    /*
     * UCC 분류하기
     */
    public function marking(Request $request)
    {
        $res = array();
        $redis = new Redis();

        $this->validate($request, [
            'type' => 'required|in:normal,normal2,xxx',
            'log' => 'required|numeric'
        ]);

        $type = $request->type;
        $log = $request->log;

        $i = 0;
        if ($type == 'xxx') {
            $ucc = ProductLogWatchUCC::find($log);

            if ($ucc->Adult == 'Y') {
                throw new CustomException('이미 확인된 영상입니다.', 500);
            }

            $where = [
                ['Where', '=', $ucc->Where],
                ['Play_Key', '=', $ucc->Play_Key]
            ];

            ProductLogWatchUCC::where($where)
                                ->update(['Adult' => 'Y']);

            $key = $ucc->Where.$ucc->Play_Key;
            // $redis->sAdd('XUCC', $key);

            $column = 'Adult_Ucc';
            $i++;
        } elseif ($type == 'normal') {
            $rowCount = ($request->rowCount == null) ? 30 : $request->rowCount;

            if ($request->maxLog == 0) {
                $where = [
                    ['Log', '>=', $log],
                    ['Adult', 'X']
                ];

                $items = ProductLogWatchUCC::where($where)
                                            ->limit($rowCount)
                                            ->get();
            } else {
                $items = ProductLogWatchUCC::whereBetween('Log', [$log, $request->maxLog])
                                            ->where('Adult', 'X')
                                            ->limit($rowCount)
                                            ->get();
            }

            foreach ($items as $item) {
                $where = [
                    ['Where', '=', $item->Where],
                    ['Play_Key', '=', $item->Play_Key]
                ];

                ProductLogWatchUCC::where($where)
                                    ->update(['Adult' => 'N']);

                $key = $item->Where.$item->Play_Key;
                // $redis->sRem('XUCC', $key);
                $i++;
            }
            $res['items'] = $items;
            $res['i'] = $i;

            $column = 'Common_Ucc';
        } elseif ($type == 'normal2') {
            $ucc = ProductLogWatchUCC::find($log);

            if ($ucc->Adult == 'N') {
                throw new CustomException('이미 확인된 영상입니다.', 500);
            }

            $where = [
                ['Where', '=', $ucc->Where],
                ['Play_Key', '=', $ucc->Play_Key]
            ];

            ProductLogWatchUCC::where($where)
                                ->update(['Adult' => 'N']);

            $key = $ucc->Where.$ucc->Play_Key;
            // $redis->sRem('XUCC', $key);

            $column = 'Common_Ucc';
            $i++;
        }

        /*$count = Status::where([
                            ['Date_Entry', '=', DB::raw('curdate()')],
                            ['User', '=', Auth::user()->user]
                        ])
                        ->pluck($column)
                        ->first();

        if ($count == null) {
            Status::insert(
                        ['User' => Auth::user()->user, $column => 1, 'Date_Entry' => DB::raw('curdate()')]
                    );
        } else {
            $counter = (int)$count + $i;

            Status::where([
                        ['Date_Entry', '=', DB::raw('curdate()')],
                        ['User', '=', Auth::User()->user]
                    ])
                    ->update([$column => $counter]);
        }*/

        $res['Success'] = true;

        return response()->json($res);
    }

    public function status(Request $request)
    {
        $res = array();

        $this->validate($request, [
            'sDate' => 'required',
            'eDate' => 'required'
        ]);

        $sDate = $request->sDate;
        $eDate = $request->eDate;

        // $res = AdultUCC::find(678606);
        $rows = AdultUCC::whereRaw('Date_Entry BETWEEN "'.$sDate.'" AND DATE_ADD("'.$eDate.'", INTERVAL 1 DAY)')
                        ->select(
                            DB::raw('DATE(`Date_Entry`) AS `Date`'), 
                            DB::raw('SUM(IF(`Where`="Youtube", 1, 0)) AS `Youtube`'),
                            DB::raw('SUM(IF(`Where`="TVPot", 1, 0)) AS `TVPot`'),
                            DB::raw('SUM(IF(`Where`="Pandora", 1, 0)) AS `Pandora`'),
                            DB::raw('SUM(IF(`Where`="Naver", 1, 0)) AS `Naver`'),
                            DB::raw('SUM(IF(`Where`="Nate", 1, 0)) AS `Nate`'),
                            DB::raw('SUM(IF(`Where`="Afreeca Sport", 1, 0)) AS `Afreeca Sport`'),
                            DB::raw('SUM(IF(`Where`="Freechal", 1, 0)) AS `Freechal`'),
                            DB::raw('SUM(IF(`Where`="Mgoon", 1, 0)) AS `Mgoon`'),
                            DB::raw('SUM(IF(`Where`="Diodeo", 1, 0)) AS `Diodeo`'),
                            DB::raw('SUM(IF(`Where`="Migame", 1, 0)) AS `Migame`'),
                            DB::raw('SUM(IF(`Where`="Cyworld", 1, 0)) AS `Cyworld`'),
                            DB::raw('SUM(IF(`Where`="GomTV", 1, 0)) AS `GomTV`'),
                            DB::raw('SUM(IF(`Where`="MySpace", 1, 0)) AS `MySpace`'),
                            DB::raw('SUM(IF(`Where`="Yahoo Video", 1, 0)) AS `Yahoo Video`'),
                            DB::raw('SUM(IF(`Where`="Paran", 1, 0)) AS `Paran`'),
                            DB::raw('SUM(IF(`Where`="Dailymotion", 1, 0)) AS `Dailymotion`'),
                            DB::raw('COUNT(*) AS `Total`')
                        )
                        ->groupBy('Date')
                        ->orderBy('Date', 'desc')
                        ->get();
        
        $total = AdultUCC::select(
                            DB::raw('SUM(IF(`Where`="Youtube", 1, 0)) AS `Youtube`'),
                            DB::raw('SUM(IF(`Where`="TVPot", 1, 0)) AS `TVPot`'),
                            DB::raw('SUM(IF(`Where`="Pandora", 1, 0)) AS `Pandora`'),
                            DB::raw('SUM(IF(`Where`="Naver", 1, 0)) AS `Naver`'),
                            DB::raw('SUM(IF(`Where`="Nate", 1, 0)) AS `Nate`'),
                            DB::raw('SUM(IF(`Where`="Afreeca Sport", 1, 0)) AS `Afreeca Sport`'),
                            DB::raw('SUM(IF(`Where`="Freechal", 1, 0)) AS `Freechal`'),
                            DB::raw('SUM(IF(`Where`="Mgoon", 1, 0)) AS `Mgoon`'),
                            DB::raw('SUM(IF(`Where`="Diodeo", 1, 0)) AS `Diodeo`'),
                            DB::raw('SUM(IF(`Where`="Migame", 1, 0)) AS `Migame`'),
                            DB::raw('SUM(IF(`Where`="Cyworld", 1, 0)) AS `Cyworld`'),
                            DB::raw('SUM(IF(`Where`="GomTV", 1, 0)) AS `GomTV`'),
                            DB::raw('SUM(IF(`Where`="MySpace", 1, 0)) AS `MySpace`'),
                            DB::raw('SUM(IF(`Where`="Yahoo Video", 1, 0)) AS `Yahoo Video`'),
                            DB::raw('SUM(IF(`Where`="Paran", 1, 0)) AS `Paran`'),
                            DB::raw('SUM(IF(`Where`="Dailymotion", 1, 0)) AS `Dailymotion`'),
                            DB::raw('COUNT(*) AS `Total`')
                        )->first();

        $res['rows'] = $rows;
        $res['total'] = $total;
        
        return response()->json($res);
    }
}
